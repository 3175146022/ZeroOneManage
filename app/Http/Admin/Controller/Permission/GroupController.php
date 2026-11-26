<?php
declare(strict_types=1);

namespace App\Http\Admin\Controller\Permission;

use App\Helper\Attribute\Permission;
use App\Http\Admin\AbstractController;
use App\Http\Admin\Request\GroupRequest;
use App\Http\Admin\Service\Permission\GroupService;
use App\Http\Common\ApiDocumentation;
use App\Http\Common\Result;
use Exception;
use Hyperf\Swagger\Annotation\HyperfServer;
use Hyperf\Validation\Annotation\Scene;
use OpenApi\Attributes as OA;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * 权限-角色组
 */
#[HyperfServer('http')]
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
    #[OA\Get(
        path: '/admin/permission/group/index',
        operationId: 'listGroups',
        summary: '分页查询角色组',
        security: ApiDocumentation::ADMIN,
        tags: ['权限管理/角色组'],
        parameters: [
            new OA\QueryParameter(name: 'page', description: '页码，默认 1', schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\QueryParameter(name: 'per_page', description: '每页数量，默认 10', schema: new OA\Schema(type: 'integer', default: 10)),
            new OA\QueryParameter(name: 'id', schema: new OA\Schema(type: 'integer')),
            new OA\QueryParameter(name: 'name', schema: new OA\Schema(type: 'string')),
            new OA\QueryParameter(name: 'status', schema: new OA\Schema(type: 'integer', enum: [1, 2, 3])),
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
                            new OA\Property(
                                property: 'data',
                                description: '角色组分页数据',
                                allOf: [new OA\Schema(ref: '#/components/schemas/GroupPage')],
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
        return $this->success($this->service->page($this->getRequestData(), $this->getCurrentPage(), $this->getPageSize()));
    }

    /**
     * 角色组详情
     *
     * @param GroupRequest $request
     * @return Result
     */
    #[Scene('detail')]
    #[OA\Get(
        path: '/admin/permission/group/detail',
        operationId: 'getGroup',
        summary: '查询角色组详情',
        security: ApiDocumentation::ADMIN,
        tags: ['权限管理/角色组'],
        parameters: [
            new OA\QueryParameter(name: 'id', required: true, schema: new OA\Schema(type: 'integer', minimum: 2)),
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
                                description: '角色组资料',
                                allOf: [new OA\Schema(ref: '#/components/schemas/Group')],
                            ),
                        ]),
                    ]),
                    new OA\Schema(ref: '#/components/schemas/ApiError'),
                ]),
            ),
            new OA\Response(response: 500, description: '服务内部错误'),
        ],
    )]
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
    #[OA\Post(
        path: '/admin/permission/group/create',
        operationId: 'createGroup',
        summary: '创建角色组',
        security: ApiDocumentation::ADMIN,
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(required: ['pid', 'name', 'rules', 'status'], allOf: [new OA\Schema(ref: '#/components/schemas/GroupRequest')])),
        tags: ['权限管理/角色组'],
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
    #[OA\Post(
        path: '/admin/permission/group/update',
        operationId: 'updateGroup',
        summary: '更新角色组',
        security: ApiDocumentation::ADMIN,
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(required: ['id', 'pid', 'name', 'rules', 'status'], allOf: [new OA\Schema(ref: '#/components/schemas/GroupRequest')])),
        tags: ['权限管理/角色组'],
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
    #[OA\Post(
        path: '/admin/permission/group/delete',
        operationId: 'deleteGroup',
        summary: '删除角色组',
        security: ApiDocumentation::ADMIN,
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/ManagedIdRequest')),
        tags: ['权限管理/角色组'],
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
    #[OA\Get(
        path: '/admin/permission/group/option',
        operationId: 'getGroupOptions',
        summary: '获取角色组选项',
        security: ApiDocumentation::ADMIN,
        tags: ['权限管理/角色组'],
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

    /**
     * 获取用户组菜单权限
     *
     * @param GroupRequest $request
     * @return Result
     */
    #[Scene('getAdminMenuIds')]
    #[OA\Get(
        path: '/admin/permission/group/menuIds',
        operationId: 'getGroupMenuIds',
        summary: '获取角色组菜单权限 ID',
        security: ApiDocumentation::ADMIN,
        tags: ['权限管理/角色组'],
        parameters: [
            new OA\QueryParameter(name: 'id', required: true, schema: new OA\Schema(type: 'integer', minimum: 2)),
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
                                description: '角色组已分配的菜单规则 ID 列表，ID 以字符串返回',
                                allOf: [new OA\Schema(ref: '#/components/schemas/MenuIds')],
                            ),
                        ]),
                    ]),
                    new OA\Schema(ref: '#/components/schemas/ApiError'),
                ]),
            ),
            new OA\Response(response: 500, description: '服务内部错误'),
        ],
    )]
    public function getGroupMenuIds(GroupRequest $request): Result
    {
        return $this->success($this->service->getGroupMenuIds((int)$request->validated()['id']));
    }
}
