<?php

declare(strict_types=1);

namespace App\Http\Admin\Middleware;

use App\Exception\BusinessException;
use App\Helper\Attribute\Permission;
use App\Helper\Traits\ParserRouterTrait;
use App\Http\Admin\Controller\CurrentUser;
use Hyperf\Collection\Arr;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\HttpServer\Router\Dispatched;
use Hyperf\Validation\UnauthorizedException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use function Hyperf\Translation\__;

/**
 * 权限验证中间件
 */
final class PermissionMiddleware implements MiddlewareInterface
{
    use ParserRouterTrait;

    public function __construct(
        private readonly CurrentUser $currentUser,
    ) {}

    /**
     * 中间件处理逻辑
     *
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $user = $this->currentUser->user();
        if (!$user) {
            throw new UnauthorizedException();
        }

        // 账号禁用
        if ($user->isHidden() || $user->isDisabled()) {
            throw new BusinessException(message: __('permission.login.accountDisable'));
        }

        // 超级管理员
        if ($user->isSuperAdmin()) {
            return $handler->handle($request);
        }

        $this->check($request->getAttribute(Dispatched::class));
        return $handler->handle($request);
    }

    /**
     * 检查权限
     *
     * @param Dispatched $dispatched
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function check(Dispatched $dispatched): void
    {
        $parseResult = $this->parse($dispatched->handler->callback);
        if (!$parseResult) {
            return;
        }
        [$controller, $method] = $parseResult;
        $annotations = AnnotationCollector::getClassMethodAnnotation($controller, $method);
        $classAnnotation = AnnotationCollector::getClassAnnotation($controller, Permission::class);
        $permissions = [];
        $classAnnotation && $permissions[] = $classAnnotation;
        $methodPermission = Arr::get($annotations, Permission::class);
        $methodPermission && $permissions[] = $methodPermission;
        foreach ($permissions as $permission) {
            $this->handlePermission($permission);
        }
    }

    /**
     * 处理权限
     *
     * @param Permission $permission
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function handlePermission(Permission $permission): void
    {
        $operation = $permission->getOperation();
        $codes = $permission->getCode();
        foreach ($codes as $code) {
            $permissions = $this->currentUser->user()->permissions ?? [];
            $isMenu = in_array($code, $permissions);
            if ($operation === Permission::OPERATION_AND && !$isMenu) {
                throw new UnauthorizedException();
            }
            if ($operation === Permission::OPERATION_OR && $isMenu) {
                return;
            }
        }
        if ($operation === Permission::OPERATION_OR) {
            throw new UnauthorizedException();
        }
    }
}
