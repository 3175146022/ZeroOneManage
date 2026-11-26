<?php
declare(strict_types=1);

namespace App\Http\Admin\Controller\Permission;

use App\Helper\Attribute\Permission;
use App\Http\Admin\AbstractController;
use App\Http\Admin\Request\RuleRequest;
use App\Http\Admin\Service\Permission\RuleService;
use App\Http\Common\ApiDocumentation;
use App\Http\Common\Result;
use Exception;
use Hyperf\Swagger\Annotation\HyperfServer;
use Hyperf\Validation\Annotation\Scene;
use OpenApi\Attributes as OA;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * 权限 - 规则
 */
#[HyperfServer('http')]
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
    #[OA\Get(
        path: '/admin/permission/rule/index',
        operationId: 'listRules',
        summary: '查询权限菜单树',
        security: ApiDocumentation::ADMIN,
        tags: ['权限管理/菜单规则'],
        parameters: [
            new OA\QueryParameter(name: 'id', schema: new OA\Schema(type: 'integer')),
            new OA\QueryParameter(name: 'status', schema: new OA\Schema(type: 'integer', enum: [1, 2, 3])),
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
                            new OA\Property(
                                property: 'data',
                                description: '目录、菜单和权限规则树',
                                allOf: [new OA\Schema(ref: '#/components/schemas/Rules')],
                            ),
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
        return $this->success($this->service->getList($this->getRequestData()));
    }

    /**
     * 详情
     *
     * @param RuleRequest $request
     * @return Result
     */
    #[Scene('detail')]
    #[OA\Get(
        path: '/admin/permission/rule/detail',
        operationId: 'getRule',
        summary: '查询权限规则详情',
        security: ApiDocumentation::ADMIN,
        tags: ['权限管理/菜单规则'],
        parameters: [
            new OA\QueryParameter(name: 'id', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: '响应体包含业务状态码、消息和数据',
                content: new OA\JsonContent(oneOf: [
                    new OA\Schema(allOf: [
                        new OA\Schema(ref: '#/components/schemas/ApiResponse'),
                        new OA\Schema(properties: [
                            new OA\Property(
                                property: 'data',
                                description: '菜单规则资料',
                                allOf: [new OA\Schema(ref: '#/components/schemas/Rule')],
                            ),
                        ]),
                    ]),
                    new OA\Schema(ref: '#/components/schemas/ApiError'),
                ]),
            ),
            new OA\Response(response: 500, description: '服务内部错误'),
        ],
    )]
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
    #[OA\Post(
        path: '/admin/permission/rule/create',
        operationId: 'createRule',
        description: '目录需要 route_path 和 icon，菜单还需要 route_name 与 component，权限规则需要 perm。',
        summary: '创建目录、菜单或权限规则',
        security: ApiDocumentation::ADMIN,
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(required: ['pid', 'type', 'name', 'weight', 'status'], allOf: [new OA\Schema(ref: '#/components/schemas/RuleRequest')])),
        tags: ['权限管理/菜单规则'],
        responses: [
            new OA\Response(
                response: 200,
                description: '响应体包含业务状态码、消息和数据',
                content: new OA\JsonContent(oneOf: [
                    new OA\Schema(allOf: [
                        new OA\Schema(ref: '#/components/schemas/ApiResponse'),
                        new OA\Schema(properties: [
                            new OA\Property(
                                property: 'data',
                                description: '操作成功，无附加数据，返回空数组',
                                allOf: [new OA\Schema(ref: '#/components/schemas/EmptyData')],
                            ),
                        ]),
                    ]),
                    new OA\Schema(ref: '#/components/schemas/ApiError'),
                ]),
            ),
            new OA\Response(response: 500, description: '服务内部错误'),
        ],
    )]
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
    #[OA\Post(
        path: '/admin/permission/rule/update',
        operationId: 'updateRule',
        description: '目录需要 route_path 和 icon。菜单还需要 route_name 与 component。权限规则需要 perm。',
        summary: '更新权限规则',
        security: ApiDocumentation::ADMIN,
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(required: ['id', 'pid', 'type', 'name', 'weight', 'status'], allOf: [new OA\Schema(ref: '#/components/schemas/RuleRequest')])),
        tags: ['权限管理/菜单规则'],
        responses: [
            new OA\Response(
                response: 200,
                description: '响应体包含业务状态码、消息和数据',
                content: new OA\JsonContent(oneOf: [
                    new OA\Schema(allOf: [
                        new OA\Schema(ref: '#/components/schemas/ApiResponse'),
                        new OA\Schema(properties: [
                            new OA\Property(
                                property: 'data',
                                description: '操作成功，无附加数据，返回空数组',
                                allOf: [new OA\Schema(ref: '#/components/schemas/EmptyData')],
                            ),
                        ]),
                    ]),
                    new OA\Schema(ref: '#/components/schemas/ApiError'),
                ]),
            ),
            new OA\Response(response: 500, description: '服务内部错误'),
        ],
    )]
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
    #[OA\Post(
        path: '/admin/permission/rule/delete',
        operationId: 'deleteRule',
        summary: '删除权限规则',
        security: ApiDocumentation::ADMIN,
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/RuleIdRequest')),
        tags: ['权限管理/菜单规则'],
        responses: [
            new OA\Response(
                response: 200,
                description: '响应体包含业务状态码、消息和数据',
                content: new OA\JsonContent(oneOf: [
                    new OA\Schema(allOf: [
                        new OA\Schema(ref: '#/components/schemas/ApiResponse'),
                        new OA\Schema(properties: [
                            new OA\Property(
                                property: 'data',
                                description: '操作成功，无附加数据，返回空数组',
                                allOf: [new OA\Schema(ref: '#/components/schemas/EmptyData')],
                            ),
                        ]),
                    ]),
                    new OA\Schema(ref: '#/components/schemas/ApiError'),
                ]),
            ),
            new OA\Response(response: 500, description: '服务内部错误'),
        ],
    )]
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
    #[OA\Get(
        path: '/admin/permission/rule/option',
        operationId: 'getRuleOptions',
        summary: '获取权限规则选项',
        security: ApiDocumentation::ADMIN,
        tags: ['权限管理/菜单规则'],
        responses: [
            new OA\Response(
                response: 200,
                description: '响应体包含业务状态码、消息和数据',
                content: new OA\JsonContent(oneOf: [
                    new OA\Schema(allOf: [
                        new OA\Schema(ref: '#/components/schemas/ApiResponse'),
                        new OA\Schema(properties: [
                            new OA\Property(
                                property: 'data',
                                description: '下拉选项树',
                                allOf: [new OA\Schema(ref: '#/components/schemas/Options')],
                            ),
                        ]),
                    ]),
                    new OA\Schema(ref: '#/components/schemas/ApiError'),
                ]),
            ),
            new OA\Response(response: 500, description: '服务内部错误'),
        ],
    )]
    public function option(): Result
    {
        return $this->success($this->service->option());
    }
}
