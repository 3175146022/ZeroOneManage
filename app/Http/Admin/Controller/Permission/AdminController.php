<?php
declare(strict_types=1);

namespace App\Http\Admin\Controller\Permission;

use App\Helper\Attribute\Permission;
use App\Http\Admin\AbstractController;
use App\Http\Admin\Request\AdminRequest;
use App\Http\Admin\Service\Permission\AdminService;
use App\Http\Admin\Service\Permission\RuleService;
use App\Http\Common\ApiDocumentation;
use App\Http\Common\Result;
use Exception;
use Hyperf\Collection\Arr;
use Hyperf\Swagger\Annotation\HyperfServer;
use Hyperf\Validation\Annotation\Scene;
use OpenApi\Attributes as OA;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * 管理员控制器
 */
#[HyperfServer('http')]
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
    #[OA\Get(
        path: '/admin/permission/admin/index',
        operationId: 'listAdmins',
        summary: '分页查询管理员',
        security: ApiDocumentation::ADMIN,
        tags: ['权限管理'],
        parameters: [
            new OA\QueryParameter(name: 'page', schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\QueryParameter(name: 'per_page', schema: new OA\Schema(type: 'integer', default: 10)),
            new OA\QueryParameter(name: 'id', schema: new OA\Schema(type: 'integer')),
            new OA\QueryParameter(name: 'username', schema: new OA\Schema(type: 'string')),
            new OA\QueryParameter(name: 'nickname', schema: new OA\Schema(type: 'string')),
            new OA\QueryParameter(name: 'mobile', schema: new OA\Schema(type: 'string')),
            new OA\QueryParameter(name: 'email', schema: new OA\Schema(type: 'string')),
            new OA\QueryParameter(name: 'status', schema: new OA\Schema(type: 'integer', enum: [1, 2, 3])),
            new OA\QueryParameter(name: 'group', description: '传入 1 返回完整角色组信息', schema: new OA\Schema(type: 'integer', enum: [1])),
            new OA\QueryParameter(name: 'order_by', description: '排序字段，多个字段用逗号分隔', schema: new OA\Schema(type: 'string')),
            new OA\QueryParameter(name: 'order_by_direction', description: '未显式指定方向时使用的排序方向', schema: new OA\Schema(type: 'string', enum: ['asc', 'desc'], default: 'desc')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: '响应体包含业务状态码、消息和数据',
                content: new OA\JsonContent(oneOf: [
                    new OA\Schema(allOf: [
                        new OA\Schema(ref: '#/components/schemas/ApiResponse'),
                        new OA\Schema(properties: [
                            new OA\Property(property: 'data', ref: '#/components/schemas/AdminPage'),
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
    #[OA\Post(
        path: '/admin/permission/admin/create',
        operationId: 'createAdmin',
        summary: '创建管理员',
        security: ApiDocumentation::ADMIN,
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(allOf: [new OA\Schema(ref: '#/components/schemas/AdminRequest')], required: ['group_ids', 'username', 'nickname', 'password', 'status'])),
        tags: ['权限管理'],
        responses: [
            new OA\Response(
                response: 200,
                description: '响应体包含业务状态码、消息和数据',
                content: new OA\JsonContent(oneOf: [
                    new OA\Schema(allOf: [
                        new OA\Schema(ref: '#/components/schemas/ApiResponse'),
                        new OA\Schema(properties: [
                            new OA\Property(property: 'data', ref: '#/components/schemas/EmptyData'),
                        ]),
                    ]),
                    new OA\Schema(ref: '#/components/schemas/ApiError'),
                ]),
            ),
            new OA\Response(response: 500, description: '服务内部错误'),
        ],
    )]
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
    #[OA\Get(
        path: '/admin/permission/admin/detail',
        operationId: 'getAdmin',
        summary: '查询管理员详情',
        security: ApiDocumentation::ADMIN,
        tags: ['权限管理'],
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
                            new OA\Property(property: 'data', ref: '#/components/schemas/Admin'),
                        ]),
                    ]),
                    new OA\Schema(ref: '#/components/schemas/ApiError'),
                ]),
            ),
            new OA\Response(response: 500, description: '服务内部错误'),
        ],
    )]
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
    #[OA\Post(
        path: '/admin/permission/admin/update',
        operationId: 'updateAdmin',
        description: '请提交完整的 group_ids 列表。省略时服务按空列表处理角色组。password 可省略。',
        summary: '更新管理员',
        security: ApiDocumentation::ADMIN,
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(allOf: [new OA\Schema(ref: '#/components/schemas/AdminRequest')], required: ['id', 'username', 'nickname', 'status'])),
        tags: ['权限管理'],
        responses: [
            new OA\Response(
                response: 200,
                description: '响应体包含业务状态码、消息和数据',
                content: new OA\JsonContent(oneOf: [
                    new OA\Schema(allOf: [
                        new OA\Schema(ref: '#/components/schemas/ApiResponse'),
                        new OA\Schema(properties: [
                            new OA\Property(property: 'data', ref: '#/components/schemas/EmptyData'),
                        ]),
                    ]),
                    new OA\Schema(ref: '#/components/schemas/ApiError'),
                ]),
            ),
            new OA\Response(response: 500, description: '服务内部错误'),
        ],
    )]
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
    #[OA\Post(
        path: '/admin/permission/admin/delete',
        operationId: 'deleteAdmin',
        summary: '删除管理员',
        security: ApiDocumentation::ADMIN,
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/ManagedIdRequest')),
        tags: ['权限管理'],
        responses: [
            new OA\Response(
                response: 200,
                description: '响应体包含业务状态码、消息和数据',
                content: new OA\JsonContent(oneOf: [
                    new OA\Schema(allOf: [
                        new OA\Schema(ref: '#/components/schemas/ApiResponse'),
                        new OA\Schema(properties: [
                            new OA\Property(property: 'data', ref: '#/components/schemas/EmptyData'),
                        ]),
                    ]),
                    new OA\Schema(ref: '#/components/schemas/ApiError'),
                ]),
            ),
            new OA\Response(response: 500, description: '服务内部错误'),
        ],
    )]
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
    #[OA\Get(
        path: '/admin/current_user/info',
        operationId: 'getCurrentUser',
        summary: '获取当前管理员资料',
        security: ApiDocumentation::ADMIN,
        tags: ['当前用户'],
        responses: [
            new OA\Response(
                response: 200,
                description: '响应体包含业务状态码、消息和数据',
                content: new OA\JsonContent(oneOf: [
                    new OA\Schema(allOf: [
                        new OA\Schema(ref: '#/components/schemas/ApiResponse'),
                        new OA\Schema(properties: [
                            new OA\Property(property: 'data', ref: '#/components/schemas/CurrentUser'),
                        ]),
                    ]),
                    new OA\Schema(ref: '#/components/schemas/ApiError'),
                ]),
            ),
            new OA\Response(response: 500, description: '服务内部错误'),
        ],
    )]
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
    #[OA\Get(
        path: '/admin/current_user/permissions',
        operationId: 'getCurrentUserPermissions',
        summary: '获取当前管理员菜单路由',
        security: ApiDocumentation::ADMIN,
        tags: ['当前用户'],
        responses: [
            new OA\Response(
                response: 200,
                description: '响应体包含业务状态码、消息和数据',
                content: new OA\JsonContent(oneOf: [
                    new OA\Schema(allOf: [
                        new OA\Schema(ref: '#/components/schemas/ApiResponse'),
                        new OA\Schema(properties: [
                            new OA\Property(property: 'data', ref: '#/components/schemas/MenuRoutes'),
                        ]),
                    ]),
                    new OA\Schema(ref: '#/components/schemas/ApiError'),
                ]),
            ),
            new OA\Response(response: 500, description: '服务内部错误'),
        ],
    )]
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