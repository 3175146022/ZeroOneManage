<?php
declare(strict_types=1);

namespace App\Http\Admin\Controller\Permission;

use App\Helper\Attribute\Permission;
use App\Http\Admin\AbstractController;
use App\Http\Admin\Request\GroupRequest;
use App\Http\Admin\Service\Permission\GroupService;
use App\Http\Common\Result;
use Exception;
use Hyperf\Validation\Annotation\Scene;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * 权限-角色组
 */
final class GroupController extends AbstractController
{
    public function __construct(
        private readonly GroupService $service,
    ) {}

    /**
     * 角色组列表
     *
     * @return Result
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Permission('permission:group:index')]
    public function index(): Result
    {
        return $this->success($this->service->page($this->getRequestData(), $this->getCurrentPage(), $this->getPageSize()));
    }

    /**
     * 角色组详情
     *
     * @param GroupRequest $request
     * @return Result
     */
    #[Scene('detail')]
    public function detail(GroupRequest $request): Result
    {
        return $this->success($this->service->findById($request->validated()['id']));
    }

    /**
     * 创建
     *
     * @param GroupRequest $request
     * @return Result
     */
    #[Scene('create')]
    #[Permission('permission:group:create')]
    public function create(GroupRequest $request): Result
    {
        $this->service->create($this->service->paramsFormat($request->validated()));
        return $this->success();
    }

    /**
     * 修改
     *
     * @param GroupRequest $request
     * @return Result
     */
    #[Scene('update')]
    #[Permission('permission:group:update')]
    public function update(GroupRequest $request): Result
    {
        $params = $this->service->paramsFormat($request->validated());
        $this->service->updateById($params['id'], $params);
        return $this->success();
    }

    /**
     * 删除
     *
     * @param GroupRequest $request
     * @return Result
     * @throws Exception
     */
    #[Scene('delete')]
    #[Permission('permission:group:delete')]
    public function delete(GroupRequest $request): Result
    {
        $this->service->deleteById($request->validated()['id']);
        return $this->success();
    }

    /**
     * 角色组下拉列表
     *
     * @return Result
     */
    public function option(): Result
    {
        return $this->success($this->service->option());
    }

    /**
     * 获取用户组菜单权限
     *
     * @param GroupRequest $request
     * @return Result
     */
    #[Scene('getAdminMenuIds')]
    public function getGroupMenuIds(GroupRequest $request): Result
    {
        return $this->success($this->service->getGroupMenuIds((int)$request->validated()['id']));
    }
}