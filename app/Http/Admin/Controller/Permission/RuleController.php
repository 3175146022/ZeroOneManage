<?php
declare(strict_types=1);

namespace App\Http\Admin\Controller\Permission;

use App\Helper\Attribute\Permission;
use App\Http\Admin\AbstractController;
use App\Http\Admin\Request\RuleRequest;
use App\Http\Admin\Service\Permission\RuleService;
use App\Http\Common\Result;
use Exception;
use Hyperf\Validation\Annotation\Scene;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * 权限 - 规则
 */
final class RuleController extends AbstractController
{
    public function __construct(
        private readonly RuleService $service,
    ) {}

    /**
     * 列表
     *
     * @return Result
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Permission('permission:rule:index')]
    public function index(): Result
    {
        return $this->success($this->service->getList($this->getRequestData()));
    }

    /**
     * 详情
     *
     * @param RuleRequest $request
     * @return Result
     */
    #[Scene('detail')]
    public function detail(RuleRequest $request): Result
    {
        return $this->success($this->service->findById($request->validated()['id']));
    }

    /**
     * 创建
     *
     * @param RuleRequest $request
     * @return Result
     */
    #[Scene('create')]
    #[Permission('permission:rule:create')]
    public function create(RuleRequest $request): Result
    {
        $this->service->create($request->validated());
        return $this->success();
    }

    /**
     * 修改
     *
     * @param RuleRequest $request
     * @return Result
     */
    #[Scene('update')]
    #[Permission('permission:rule:update')]
    public function update(RuleRequest $request): Result
    {
        $params = $request->validated();
        $this->service->updateById($params['id'], $params);
        return $this->success();
    }

    /**
     * 删除
     *
     * @param RuleRequest $request
     * @return Result
     * @throws Exception
     */
    #[Scene('delete')]
    #[Permission('permission:rule:delete')]
    public function delete(RuleRequest $request): Result
    {
        $this->service->deleteById($request->validated()['id']);
        return $this->success();
    }

    /**
     * 获取权限规则下拉选项
     *
     * @return Result
     */
    public function option(): Result
    {
        return $this->success($this->service->option());
    }
}