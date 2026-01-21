<?php
declare(strict_types=1);

namespace App\Http\Admin\Controller\Permission;

use App\Helper\Attribute\Permission;
use App\Http\Admin\AbstractController;
use App\Http\Admin\Request\AdminOpLogRequest;
use App\Http\Admin\Service\Permission\AdminOpLogService;
use App\Http\Common\Result;
use Hyperf\Validation\Annotation\Scene;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * 管理员操作日志
 */
final class AdminOpLogController extends AbstractController
{
    public function __construct(
        private readonly AdminOpLogService $service,
    ) {}

    /**
     * 分页查询
     *
     * @return Result
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Permission('permission:oplog:index')]
    public function index(): Result
    {
        return $this->success($this->service->page($this->getRequestData(), $this->getCurrentPage(), $this->getPageSize()));
    }

    /**
     * 详情
     *
     * @param AdminOpLogRequest $request
     * @return Result
     */
    #[Scene('detail')]
    #[Permission('permission:oplog:detail')]
    public function detail(AdminOpLogRequest $request): Result
    {
        return $this->success($this->service->findById($request->validated()['id']));
    }
}