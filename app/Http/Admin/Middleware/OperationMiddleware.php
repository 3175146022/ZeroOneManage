<?php

declare(strict_types=1);

namespace App\Http\Admin\Middleware;

use App\Helper\Support\RequestSupport;
use App\Helper\Traits\ParserRouterTrait;
use App\Http\Admin\Controller\CurrentUser;
use App\Http\Admin\Event\Logstash\RequestOperationEvent;
use App\Http\Admin\Repository\Permission\AdminRepository;
use App\Http\Common\Request\ClientIpTrait;
use Hyperf\HttpServer\Router\Dispatched;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use function Hyperf\Collection\collect;

/**
 * 管理员操作日志
 */
final class OperationMiddleware implements MiddlewareInterface
{
    use ParserRouterTrait;
    use ClientIpTrait;

    public function __construct(
        private readonly CurrentUser              $currentUser,
        private readonly EventDispatcherInterface $dispatcher,
        private readonly ContainerInterface       $container,
        private readonly AdminRepository          $adminRepository,
    ) {}

    /**
     *
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        if ($response->getStatusCode() !== 200) {
            return $response;
        }
        $dispatched = $request->getAttribute(Dispatched::class);
        $parseResult = $this->parse($dispatched?->handler?->callback);
        if (!$parseResult) {
            return $response;
        }
        // 获取路由说明信息
        $options = $dispatched->handler->options;
        $routerDescription = $options['desc'] ?? '';
        if ($routerDescription) {
            $adminInfo = $this->getAdminInfo($request);
            $this->dispatcher->dispatch(new RequestOperationEvent(
                adminId: (int)($adminInfo['id'] ?? 0),
                username: (string)($adminInfo['username'] ?? ''),
                path: $request->getUri()->getPath(),
                title: $routerDescription,
                body: $this->getBodyFormat($request) ?: '',
                ip: $this->container->get(RequestSupport::class)->getClientIp(),
                userAgent: $request->getHeaderLine('User-Agent'),
                method: $request->getMethod(),
            ));
        }

        return $response;
    }

    /**
     * 获取管理员信息
     *
     * @param ServerRequestInterface $request
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function getAdminInfo(ServerRequestInterface $request): array
    {
        if ($request->getUri()->getPath() === '/admin/login') {
            $body = $request->getParsedBody() ?? [];
            $condition = ['username' => $body['username'] ?? ''];
            $user = $this->adminRepository->getQuery()->where($condition)->first();
        } else {
            $user = $this->currentUser->user();
        }

        return [
            'id' => $user?->id ?? 0,
            'username' => $user?->username ?? '',
        ];
    }

    /**
     * 获取请求参数
     *
     * @param ServerRequestInterface $request
     * @return bool|string
     */
    private function getBodyFormat(ServerRequestInterface $request): bool|string
    {
        $getBody = $request->getQueryParams();
        $postBody = $request->getParsedBody() ?? [];

        // 特殊接口处理
        if ($request->getUri()->getPath() === '/admin/login') {
            $postBody = collect($postBody)->except(['password'])->toArray();
        }

        $body = array_merge($getBody, $postBody);

        return !empty($body) ? json_encode($body, JSON_UNESCAPED_UNICODE) : '';
    }
}
