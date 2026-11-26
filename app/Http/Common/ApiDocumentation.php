<?php

declare(strict_types=1);

namespace App\Http\Common;

use OpenApi\Attributes as OA;

#[OA\OpenApi(openapi: '3.0.0')]
#[OA\Info(version: '1.0.0', description: '管理后台及认证接口。HTTP 200 的响应体中，code=200 表示成功，0 表示业务失败，403 表示未登录或权限不足，404 表示资源不存在，422 表示参数校验失败。APP_DEBUG=true 时跳过管理端签名校验。', title: 'ZeroOne API')]
#[OA\Tag(name: '认证', description: 'API 证书与管理员登录会话')]
#[OA\Tag(name: '当前用户', description: '当前管理员信息、权限和会话操作')]
#[OA\Tag(name: '权限管理/管理员', description: '管理员资料及账户管理')]
#[OA\Tag(name: '权限管理/角色组', description: '角色组及角色菜单授权')]
#[OA\Tag(name: '权限管理/菜单规则', description: '目录、菜单和访问权限规则')]
#[OA\Tag(name: '权限管理/操作日志', description: '管理员操作日志记录和查询')]
#[OA\SecurityScheme(securityScheme: 'accessToken', type: 'apiKey', description: '管理员登录或刷新凭证接口返回的 access_token', name: 'X-Access-Token', in: 'header')]
#[OA\SecurityScheme(securityScheme: 'appKey', type: 'apiKey', description: '通过 GET /oauth/certificate 获取的 app_key', name: 'app_key', in: 'header')]
#[OA\SecurityScheme(securityScheme: 'requestSignature', type: 'apiKey', description: '对请求参数排序后使用 app_secret 执行 HMAC-SHA256 得到的十六进制摘要', name: 'signature', in: 'query')]
#[OA\SecurityScheme(securityScheme: 'requestTimestamp', type: 'apiKey', description: 'Unix 秒级时间戳，服务器时间偏差需在 300 秒以内', name: 'timestamp', in: 'query')]
#[OA\Schema(schema: 'ApiResponse', required: ['code', 'message', 'data'], properties: [
    new OA\Property(property: 'code', description: '业务状态码，200 表示成功', type: 'integer', enum: [200], example: 200),
    new OA\Property(property: 'message', description: '接口返回的处理结果提示', type: 'string', example: 'success'),
    new OA\Property(property: 'data', description: '按接口定义返回的对象、列表或空数组', oneOf: [
        new OA\Schema(type: 'object', additionalProperties: true),
        new OA\Schema(type: 'array', items: new OA\Items()),
    ]),
], type: 'object')]
#[OA\Schema(schema: 'TokenPair', description: '登录会话的访问令牌和刷新令牌', required: ['access_token', 'refresh_token'], properties: [
    new OA\Property(property: 'access_token', description: '用于 X-Access-Token 请求头', type: 'string'),
    new OA\Property(property: 'refresh_token', description: '用于刷新会话', type: 'string'),
], type: 'object')]
#[OA\Schema(schema: 'Certificate', description: '用于请求签名的应用凭证', required: ['app_key', 'app_secret'], properties: [
    new OA\Property(property: 'app_key', description: '应用标识，请通过 app_key 请求头提交', type: 'string'),
    new OA\Property(property: 'app_secret', description: '仅签名时使用，请妥善保管', type: 'string'),
], type: 'object')]
#[OA\Schema(schema: 'Captcha', description: '图形验证码及其标识', required: ['key', 'image'], properties: [
    new OA\Property(property: 'key', description: '验证码标识，登录时作为 captcha_key 传回', type: 'string'),
    new OA\Property(property: 'image', description: 'data:image/png;base64 格式的验证码图片', type: 'string'),
], type: 'object')]
#[OA\Schema(schema: 'ManagedIdRequest', required: ['id'], properties: [
    new OA\Property(property: 'id', type: 'integer', minimum: 2, example: 2),
], type: 'object')]
#[OA\Schema(schema: 'LoginRequest', required: ['username', 'password', 'captcha', 'captcha_key'], properties: [
    new OA\Property(property: 'username', type: 'string', example: 'admin'),
    new OA\Property(property: 'password', type: 'string', format: 'password'),
    new OA\Property(property: 'captcha', type: 'string'),
    new OA\Property(property: 'captcha_key', type: 'string'),
], type: 'object')]
#[OA\Schema(schema: 'AdminRequest', allOf: [
    new OA\Schema(properties: [
        new OA\Property(property: 'id', type: 'integer', minimum: 2),
        new OA\Property(property: 'group_ids', type: 'array', items: new OA\Items(type: 'integer')),
        new OA\Property(property: 'username', type: 'string', maxLength: 30, minLength: 3),
        new OA\Property(property: 'nickname', type: 'string'),
        new OA\Property(property: 'mobile', type: 'string', nullable: true),
        new OA\Property(property: 'email', type: 'string', format: 'email', nullable: true),
        new OA\Property(property: 'password', type: 'string', format: 'password'),
        new OA\Property(property: 'status', type: 'integer', enum: [1, 2]),
    ], type: 'object'),
])]
#[OA\Schema(schema: 'GroupRequest', properties: [
    new OA\Property(property: 'id', type: 'integer', minimum: 2),
    new OA\Property(property: 'pid', type: 'integer', minimum: 1),
    new OA\Property(property: 'name', type: 'string'),
    new OA\Property(property: 'rules', type: 'string', pattern: '^[0-9]+(,[0-9]+)*$', example: '1,2,3'),
    new OA\Property(property: 'status', type: 'integer', enum: [1, 2]),
], type: 'object')]
#[OA\Schema(schema: 'RuleRequest', properties: [
    new OA\Property(property: 'id', type: 'integer'),
    new OA\Property(property: 'pid', type: 'integer'),
    new OA\Property(property: 'type', description: '1 目录，2 菜单，3 权限', type: 'integer', enum: [1, 2, 3]),
    new OA\Property(property: 'name', type: 'string', maxLength: 100),
    new OA\Property(property: 'route_name', type: 'string', nullable: true),
    new OA\Property(property: 'route_path', type: 'string', nullable: true),
    new OA\Property(property: 'component', type: 'string', nullable: true),
    new OA\Property(property: 'icon', type: 'string', nullable: true),
    new OA\Property(property: 'perm', type: 'string', nullable: true),
    new OA\Property(property: 'redirect', type: 'string', nullable: true),
    new OA\Property(property: 'remark', type: 'string', nullable: true),
    new OA\Property(property: 'weight', type: 'integer'),
    new OA\Property(property: 'status', type: 'integer', enum: [1, 2]),
    new OA\Property(property: 'always_show', type: 'integer', enum: [1, 2], nullable: true),
    new OA\Property(property: 'keep_alive', type: 'integer', enum: [1, 2], nullable: true),
], type: 'object')]
#[OA\Schema(schema: 'ApiError', required: ['code', 'message', 'data'], properties: [
    new OA\Property(property: 'code', description: '业务错误码，0 为业务失败，403 为未登录或权限不足，404 为资源不存在，422 为参数校验失败', type: 'integer', enum: [0, 403, 404, 422]),
    new OA\Property(property: 'message', description: '错误原因或首条参数校验提示', type: 'string'),
    new OA\Property(property: 'data', description: '错误响应的数据字段，返回空数组', type: 'array', items: new OA\Items(type: 'string'), maxItems: 0),
], type: 'object')]
#[OA\Schema(schema: 'RuleIdRequest', required: ['id'], properties: [
    new OA\Property(property: 'id', type: 'integer'),
], type: 'object')]
#[OA\Schema(schema: 'EmptyData', description: '操作成功，无附加数据，返回空数组', type: 'array', items: new OA\Items(type: 'object'), maxItems: 0)]
#[OA\Schema(schema: 'CurrentUser', description: '当前登录管理员资料', properties: [
    new OA\Property(property: 'id', description: '管理员 ID', type: 'integer'),
    new OA\Property(property: 'username', description: '登录账号', type: 'string'),
    new OA\Property(property: 'nickname', description: '显示昵称', type: 'string'),
    new OA\Property(property: 'avatar', description: '头像地址', type: 'string'),
    new OA\Property(property: 'status', description: '账号状态字符串，1 正常，2 隐藏，3 禁用', type: 'string'),
    new OA\Property(property: 'loginFailure', description: '已记录的登录失败次数', type: 'integer'),
    new OA\Property(property: 'loginTime', description: '最近一次登录时间', type: 'string'),
    new OA\Property(property: 'isSuperAdmin', description: '超级管理员标记，1 是，2 否', type: 'integer'),
    new OA\Property(property: 'code', description: '角色标识，例如 SuperAdmin', type: 'string'),
    new OA\Property(property: 'permissions', description: '当前账号拥有的权限标识列表', type: 'array', items: new OA\Items(type: 'string')),
], type: 'object')]
#[OA\Schema(schema: 'Admin', description: '管理员资料及所属角色组', properties: [
    new OA\Property(property: 'id', description: '管理员 ID', type: 'integer'),
    new OA\Property(property: 'username', description: '登录账号', type: 'string'),
    new OA\Property(property: 'nickname', description: '显示昵称', type: 'string'),
    new OA\Property(property: 'avatar', description: '头像地址', type: 'string'),
    new OA\Property(property: 'email', description: '电子邮箱', type: 'string', nullable: true),
    new OA\Property(property: 'mobile', description: '手机号码', type: 'string', nullable: true),
    new OA\Property(property: 'status', description: '账号状态，1 正常，2 隐藏，3 禁用', type: 'integer'),
    new OA\Property(property: 'login_time', description: '最近一次登录时间，未登录时可为空', type: 'string', nullable: true),
    new OA\Property(property: 'group', description: '管理员所属的角色组列表', type: 'array', items: new OA\Items(ref: '#/components/schemas/Group')),
], type: 'object')]
#[OA\Schema(schema: 'Group', description: '角色组资料', properties: [
    new OA\Property(property: 'id', description: '角色组 ID', type: 'integer'),
    new OA\Property(property: 'pid', description: '上级角色组 ID', type: 'integer'),
    new OA\Property(property: 'name', description: '角色组名称', type: 'string'),
    new OA\Property(property: 'status', description: '角色组状态，1 正常，2 隐藏，3 禁用', type: 'integer'),
    new OA\Property(property: 'children', description: '下级角色组列表', type: 'array', items: new OA\Items(ref: '#/components/schemas/Group')),
], type: 'object')]
#[OA\Schema(schema: 'MenuMeta', properties: [
    new OA\Property(property: 'title', description: '菜单显示标题', type: 'string'),
    new OA\Property(property: 'icon', description: '菜单图标标识', type: 'string', nullable: true),
    new OA\Property(property: 'hidden', description: '是否隐藏该菜单', type: 'boolean'),
    new OA\Property(property: 'alwaysShow', description: '目录只有一个子路由时是否仍显示目录', type: 'boolean'),
    new OA\Property(property: 'keepAlive', description: '是否缓存该菜单对应的页面', type: 'boolean'),
    new OA\Property(property: 'params', description: '路由参数，当前返回 null', type: 'object', nullable: true),
], type: 'object')]
#[OA\Schema(schema: 'MenuRoute', properties: [
    new OA\Property(property: 'name', description: '前端路由名称', type: 'string'),
    new OA\Property(property: 'path', description: '前端路由路径', type: 'string'),
    new OA\Property(property: 'component', description: '前端页面组件标识或路径', type: 'string'),
    new OA\Property(property: 'redirect', description: '路由重定向目标', type: 'string'),
    new OA\Property(property: 'meta', description: '菜单标题、图标和显示设置', allOf: [new OA\Schema(ref: '#/components/schemas/MenuMeta')]),
    new OA\Property(property: 'children', description: '子菜单路由列表', type: 'array', items: new OA\Items(ref: '#/components/schemas/MenuRoute')),
], type: 'object')]
#[OA\Schema(schema: 'Rule', description: '菜单规则资料', properties: [
    new OA\Property(property: 'id', description: '菜单规则 ID', type: 'integer'),
    new OA\Property(property: 'pid', description: '父级规则 ID，0 表示顶级', type: 'integer'),
    new OA\Property(property: 'type', description: '规则类型，1 目录，2 菜单，3 权限', type: 'integer'),
    new OA\Property(property: 'name', description: '目录、菜单或权限规则的名称', type: 'string'),
    new OA\Property(property: 'route_name', description: '前端路由名称', type: 'string', nullable: true),
    new OA\Property(property: 'route_path', description: '前端路由路径', type: 'string', nullable: true),
    new OA\Property(property: 'component', description: '前端页面组件标识或路径', type: 'string', nullable: true),
    new OA\Property(property: 'redirect', description: '路由重定向目标', type: 'string', nullable: true),
    new OA\Property(property: 'icon', description: '菜单图标标识', type: 'string', nullable: true),
    new OA\Property(property: 'perm', description: '权限标识，例如 permission:admin:index', type: 'string', nullable: true),
    new OA\Property(property: 'always_show', description: '目录只有一个子路由时是否仍显示目录，1 是，2 否', type: 'integer'),
    new OA\Property(property: 'keep_alive', description: '是否缓存菜单对应的页面，1 是，2 否', type: 'integer'),
    new OA\Property(property: 'weight', description: '规则排序权重', type: 'integer'),
    new OA\Property(property: 'status', description: '规则状态，1 正常，2 隐藏，3 禁用', type: 'integer'),
    new OA\Property(property: 'children', description: '下级目录、菜单或权限规则列表', type: 'array', items: new OA\Items(ref: '#/components/schemas/Rule')),
], type: 'object')]
#[OA\Schema(schema: 'OperationLog', description: '操作日志详情', properties: [
    new OA\Property(property: 'id', description: '操作日志 ID', type: 'integer'),
    new OA\Property(property: 'username', description: '执行操作的管理员账号', type: 'string'),
    new OA\Property(property: 'method', description: 'HTTP 请求方法，例如 GET 或 POST', type: 'string'),
    new OA\Property(property: 'path', description: '请求的 API 路径', type: 'string'),
    new OA\Property(property: 'title', description: '操作名称', type: 'string'),
    new OA\Property(property: 'ip', description: '发起请求的客户端 IP 地址', type: 'string'),
    new OA\Property(property: 'body', description: '记录的请求内容，以字符串返回', type: 'string'),
    new OA\Property(property: 'user_agent', description: '请求的 User-Agent', type: 'string'),
    new OA\Property(property: 'created_at', description: '操作日志的记录时间', type: 'string'),
], type: 'object')]
#[OA\Schema(schema: 'Option', properties: [
    new OA\Property(property: 'label', description: '下拉选项显示名称', type: 'string'),
    new OA\Property(property: 'value', description: '对应的角色组或规则 ID', type: 'integer'),
    new OA\Property(property: 'children', description: '下级选项列表', type: 'array', items: new OA\Items(ref: '#/components/schemas/Option')),
], type: 'object')]
#[OA\Schema(schema: 'MenuRoutes', description: '当前管理员可访问的菜单路由树', type: 'array', items: new OA\Items(ref: '#/components/schemas/MenuRoute'))]
#[OA\Schema(schema: 'Options', description: '下拉选项树', type: 'array', items: new OA\Items(ref: '#/components/schemas/Option'))]
#[OA\Schema(schema: 'Rules', description: '目录、菜单和权限规则树', type: 'array', items: new OA\Items(ref: '#/components/schemas/Rule'))]
#[OA\Schema(schema: 'MenuIds', description: '角色组已分配的菜单规则 ID 列表，ID 以字符串返回', type: 'array', items: new OA\Items(type: 'string'))]
#[OA\Schema(schema: 'GroupPage', description: '角色组分页数据', properties: [
    new OA\Property(property: 'list', description: '当前页的角色组列表', type: 'array', items: new OA\Items(ref: '#/components/schemas/Group')),
    new OA\Property(property: 'page', description: '当前页码，从 1 开始', type: 'integer'),
    new OA\Property(property: 'per_page', description: '每页记录数', type: 'integer'),
    new OA\Property(property: 'total', description: '符合查询条件的记录总数', type: 'integer'),
], type: 'object')]
#[OA\Schema(schema: 'AdminPage', description: '管理员分页数据', properties: [
    new OA\Property(property: 'list', description: '当前页的管理员列表', type: 'array', items: new OA\Items(ref: '#/components/schemas/Admin')),
    new OA\Property(property: 'page', description: '当前页码，从 1 开始', type: 'integer'),
    new OA\Property(property: 'per_page', description: '每页记录数', type: 'integer'),
    new OA\Property(property: 'total', description: '符合查询条件的记录总数', type: 'integer'),
], type: 'object')]
#[OA\Schema(schema: 'OperationLogPage', description: '操作日志分页数据', properties: [
    new OA\Property(property: 'list', description: '当前页的操作日志列表', type: 'array', items: new OA\Items(ref: '#/components/schemas/OperationLog')),
    new OA\Property(property: 'page', description: '当前页码，从 1 开始', type: 'integer'),
    new OA\Property(property: 'per_page', description: '每页记录数', type: 'integer'),
    new OA\Property(property: 'total', description: '符合查询条件的记录总数', type: 'integer'),
], type: 'object')]
final class ApiDocumentation
{
    public const SIGNATURE = [['appKey' => [], 'requestSignature' => [], 'requestTimestamp' => []]];
    public const ADMIN = [['accessToken' => []] + self::SIGNATURE[0]];
}
