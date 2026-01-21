<?php
declare(strict_types=1);

namespace App\Http\Admin\Controller\Permission;

use App\Helper\Attribute\Permission;
use App\Http\Admin\AbstractController;
use App\Http\Admin\Request\AdminRequest;
use App\Http\Admin\Service\Permission\AdminService;
use App\Http\Admin\Service\Permission\RuleService;
use App\Http\Common\Result;
use Exception;
use Hyperf\Collection\Arr;
use Hyperf\Validation\Annotation\Scene;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * 管理员控制器
 */
final class AdminController extends AbstractController
{
    public function __construct(
        private readonly AdminService $service,
        private readonly RuleService  $ruleService,
    ) {}

    /**
     * 列表
     *
     * @return Result
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Permission('permission:admin:index')]
    public function index(): Result
    {
        return $this->success(
            $this->service->page($this->getRequestData(), $this->getCurrentPage(), $this->getPageSize())
        );
    }

    /**
     * 创建
     *
     * @param AdminRequest $request
     * @return Result
     */
    #[Scene('create')]
    #[Permission('permission:admin:create')]
    public function create(AdminRequest $request): Result
    {
        $this->service->createAdmin($request->validated());
        return $this->success();
    }

    /**
     * 详情
     *
     * @param AdminRequest $request
     * @return Result
     */
    #[Scene('detail')]
    public function detail(AdminRequest $request): Result
    {
        return $this->success($this->service->detail((int)$request->validated()['id']));
    }

    /**
     * 修改
     *
     * @param AdminRequest $request
     * @return Result
     */
    #[Scene('update')]
    #[Permission('permission:admin:update')]
    public function update(AdminRequest $request): Result
    {
        $this->service->modify($request->validated());
        return $this->success();
    }

    /**
     * 删除
     *
     * @param AdminRequest $request
     * @return Result
     * @throws Exception
     */
    #[Scene('delete')]
    #[Permission('permission:admin:delete')]
    public function delete(AdminRequest $request): Result
    {
        $this->service->deleteById($request->validated()['id']);
        return $this->success();
    }

    /**
     * 获取当前用户信息
     *
     * @return Result
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getCurrentUser(): Result
    {
        return $this->success($this->service->getCurrentUser($this->getUserToken()) ?? []);
    }

    /**
     * 获取当前用户权限
     *
     * @return Result
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getCurrentUserRoutes(): Result
    {
        // 当前用户信息
        $currentUser = $this->service->getCurrentUser($this->getUserToken());
        if (!$currentUser) {
            return $this->success();
        }
        // 当前管理员拥有的权限
        $condition = [];
        // 非超管需要过滤权限
        if (!$currentUser->isSuperAdmin()) {
            $currentUserMenus = $this->service->getAdminMenuIds($currentUser->id);
            Arr::set($condition, 'ids', $currentUserMenus);
        }
        return $this->success($this->ruleService->getCurrentUserRoutes($condition));
    }
}