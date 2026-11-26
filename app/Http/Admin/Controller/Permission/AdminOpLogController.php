<?php
declare(strict_types=1);

namespace App\Http\Admin\Controller\Permission;

use App\Helper\Attribute\Permission;
use App\Http\Admin\AbstractController;
use App\Http\Admin\Request\AdminOpLogRequest;
use App\Http\Admin\Service\Permission\AdminOpLogService;
use App\Http\Common\ApiDocumentation;
use App\Http\Common\Result;
use Hyperf\Swagger\Annotation\HyperfServer;
use Hyperf\Validation\Annotation\Scene;
use OpenApi\Attributes as OA;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * 管理员操作日志
 */
#[HyperfServer('http')]
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
    #[OA\Get(
        path: '/admin/permission/oplog/index',
        operationId: 'listOperationLogs',
        summary: '分页查询管理员操作日志',
        security: ApiDocumentation::ADMIN,
        tags: ['权限管理'],
        parameters: [
            new OA\QueryParameter(name: 'page', schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\QueryParameter(name: 'per_page', schema: new OA\Schema(type: 'integer', default: 10)),
            new OA\QueryParameter(name: 'id', schema: new OA\Schema(type: 'integer')),
            new OA\QueryParameter(name: 'order_by', description: '排序字段，多个字段用逗号分隔', schema: new OA\Schema(type: 'string')),
            new OA\QueryParameter(name: 'order_by_direction', description: '未显式指定方向时使用的排序方向', schema: new OA\Schema(type: 'string', default: 'desc', enum: ['asc', 'desc'])),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: '响应体包含业务状态码、消息和数据',
                content: new OA\JsonContent(oneOf: [
                    new OA\Schema(allOf: [
                        new OA\Schema(ref: '#/components/schemas/ApiResponse'),
                        new OA\Schema(properties: [
                            new OA\Property(property: 'data', ref: '#/components/schemas/OperationLogPage'),
                        ]),
                    ]),
                    new OA\Schema(ref: '#/components/schemas/ApiError'),
                ]),
            ),
            new OA\Response(response: 500, description: '服务内部错误'),
        ],
    )]
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
    #[OA\Get(
        path: '/admin/permission/oplog/detail',
        operationId: 'getOperationLog',
        summary: '查询管理员操作日志详情',
        security: ApiDocumentation::ADMIN,
        tags: ['权限管理'],
        parameters: [
            new OA\QueryParameter(name: 'id', required: true, schema: new OA\Schema(type: 'integer', minimum: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: '响应体包含业务状态码、消息和数据',
                content: new OA\JsonContent(oneOf: [
                    new OA\Schema(allOf: [
                        new OA\Schema(ref: '#/components/schemas/ApiResponse'),
                        new OA\Schema(properties: [
                            new OA\Property(property: 'data', ref: '#/components/schemas/OperationLog'),
                        ]),
                    ]),
                    new OA\Schema(ref: '#/components/schemas/ApiError'),
                ]),
            ),
            new OA\Response(response: 500, description: '服务内部错误'),
        ],
    )]
    public function detail(AdminOpLogRequest $request): Result
    {
        return $this->success($this->service->findById($request->validated()['id']));
    }
}