<?php

declare(strict_types=1);

namespace App\Http\Common;

use OpenApi\Attributes as OA;

#[OA\OpenApi(openapi: '3.0.0')]
#[OA\Info(version: '1.0.0', description: '管理后台及认证接口。HTTP 200 的响应体中，code=200 表示成功，0 表示业务失败，403 表示未登录或权限不足，404 表示资源不存在，422 表示参数校验失败。APP_DEBUG=true 时跳过管理端签名校验。', title: 'ZeroOne API')]
#[OA\Tag(name: '认证', description: 'API 证书与管理员登录会话')]
#[OA\Tag(name: '当前用户', description: '当前管理员信息、权限和会话操作')]
#[OA\Tag(name: '权限管理', description: '菜单、规则、管理员、角色组和操作日志')]
#[OA\SecurityScheme(securityScheme: 'accessToken', type: 'apiKey', description: '管理员登录或刷新凭证接口返回的 access_token', name: 'X-Access-Token', in: 'header')]
#[OA\SecurityScheme(securityScheme: 'appKey', type: 'apiKey', description: '通过 GET /oauth/certificate 获取的 app_key', name: 'app_key', in: 'header')]
#[OA\SecurityScheme(securityScheme: 'requestSignature', type: 'apiKey', description: '对请求参数排序后使用 app_secret 执行 HMAC-SHA256 得到的十六进制摘要', name: 'signature', in: 'query')]
#[OA\SecurityScheme(securityScheme: 'requestTimestamp', type: 'apiKey', description: 'Unix 秒级时间戳，服务器时间偏差需在 300 秒以内', name: 'timestamp', in: 'query')]
#[OA\Schema(schema: 'ApiResponse', required: ['code', 'message', 'data'], properties: [
    new OA\Property(property: 'code', type: 'integer', enum: [200], example: 200),
    new OA\Property(property: 'message', type: 'string', example: 'success'),
    new OA\Property(property: 'data', description: '按接口定义返回的对象、列表或空数组', oneOf: [
        new OA\Schema(type: 'object', additionalProperties: true),
        new OA\Schema(type: 'array', items: new OA\Items()),
    ]),
], type: 'object')]
#[OA\Schema(schema: 'TokenPair', required: ['access_token', 'refresh_token'], properties: [
    new OA\Property(property: 'access_token', description: '用于 X-Access-Token 请求头', type: 'string'),
    new OA\Property(property: 'refresh_token', description: '用于刷新会话', type: 'string'),
], type: 'object')]
#[OA\Schema(schema: 'Certificate', required: ['app_key', 'app_secret'], properties: [
    new OA\Property(property: 'app_key', type: 'string'),
    new OA\Property(property: 'app_secret', description: '仅签名时使用，请妥善保管', type: 'string'),
], type: 'object')]
#[OA\Schema(schema: 'Captcha', required: ['key', 'image'], properties: [
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
    new OA\Property(property: 'code', type: 'integer', enum: [0, 403, 404, 422]),
    new OA\Property(property: 'message', type: 'string'),
    new OA\Property(property: 'data', type: 'array', items: new OA\Items(type: 'string'), maxItems: 0),
], type: 'object')]
#[OA\Schema(schema: 'RuleIdRequest', required: ['id'], properties: [
    new OA\Property(property: 'id', type: 'integer'),
], type: 'object')]
#[OA\Schema(schema: 'EmptyData', type: 'array', items: new OA\Items(type: 'object'), maxItems: 0)]
#[OA\Schema(schema: 'CurrentUser', properties: [
    new OA\Property(property: 'id', type: 'integer'),
    new OA\Property(property: 'username', type: 'string'),
    new OA\Property(property: 'nickname', type: 'string'),
    new OA\Property(property: 'avatar', type: 'string'),
    new OA\Property(property: 'status', type: 'string'),
    new OA\Property(property: 'loginFailure', type: 'integer'),
    new OA\Property(property: 'loginTime', type: 'string'),
    new OA\Property(property: 'isSuperAdmin', type: 'integer'),
    new OA\Property(property: 'code', type: 'string'),
    new OA\Property(property: 'permissions', type: 'array', items: new OA\Items(type: 'string')),
], type: 'object')]
#[OA\Schema(schema: 'Admin', properties: [
    new OA\Property(property: 'id', type: 'integer'),
    new OA\Property(property: 'username', type: 'string'),
    new OA\Property(property: 'nickname', type: 'string'),
    new OA\Property(property: 'avatar', type: 'string'),
    new OA\Property(property: 'email', type: 'string', nullable: true),
    new OA\Property(property: 'mobile', type: 'string', nullable: true),
    new OA\Property(property: 'status', type: 'integer'),
    new OA\Property(property: 'login_time', type: 'string', nullable: true),
    new OA\Property(property: 'group', type: 'array', items: new OA\Items(ref: '#/components/schemas/Group')),
], type: 'object')]
#[OA\Schema(schema: 'Group', properties: [
    new OA\Property(property: 'id', type: 'integer'),
    new OA\Property(property: 'pid', type: 'integer'),
    new OA\Property(property: 'name', type: 'string'),
    new OA\Property(property: 'status', type: 'integer'),
    new OA\Property(property: 'children', type: 'array', items: new OA\Items(ref: '#/components/schemas/Group')),
], type: 'object')]
#[OA\Schema(schema: 'MenuMeta', properties: [
    new OA\Property(property: 'title', type: 'string'),
    new OA\Property(property: 'icon', type: 'string', nullable: true),
    new OA\Property(property: 'hidden', type: 'boolean'),
    new OA\Property(property: 'alwaysShow', type: 'boolean'),
    new OA\Property(property: 'keepAlive', type: 'boolean'),
    new OA\Property(property: 'params', type: 'object', nullable: true),
], type: 'object')]
#[OA\Schema(schema: 'MenuRoute', properties: [
    new OA\Property(property: 'name', type: 'string'),
    new OA\Property(property: 'path', type: 'string'),
    new OA\Property(property: 'component', type: 'string'),
    new OA\Property(property: 'redirect', type: 'string'),
    new OA\Property(property: 'meta', ref: '#/components/schemas/MenuMeta'),
    new OA\Property(property: 'children', type: 'array', items: new OA\Items(ref: '#/components/schemas/MenuRoute')),
], type: 'object')]
#[OA\Schema(schema: 'Rule', properties: [
    new OA\Property(property: 'id', type: 'integer'),
    new OA\Property(property: 'pid', type: 'integer'),
    new OA\Property(property: 'type', type: 'integer'),
    new OA\Property(property: 'name', type: 'string'),
    new OA\Property(property: 'route_name', type: 'string', nullable: true),
    new OA\Property(property: 'route_path', type: 'string', nullable: true),
    new OA\Property(property: 'component', type: 'string', nullable: true),
    new OA\Property(property: 'redirect', type: 'string', nullable: true),
    new OA\Property(property: 'icon', type: 'string', nullable: true),
    new OA\Property(property: 'perm', type: 'string', nullable: true),
    new OA\Property(property: 'always_show', type: 'integer'),
    new OA\Property(property: 'keep_alive', type: 'integer'),
    new OA\Property(property: 'weight', type: 'integer'),
    new OA\Property(property: 'status', type: 'integer'),
    new OA\Property(property: 'children', type: 'array', items: new OA\Items(ref: '#/components/schemas/Rule')),
], type: 'object')]
#[OA\Schema(schema: 'OperationLog', properties: [
    new OA\Property(property: 'id', type: 'integer'),
    new OA\Property(property: 'username', type: 'string'),
    new OA\Property(property: 'method', type: 'string'),
    new OA\Property(property: 'path', type: 'string'),
    new OA\Property(property: 'title', type: 'string'),
    new OA\Property(property: 'ip', type: 'string'),
    new OA\Property(property: 'body', type: 'string'),
    new OA\Property(property: 'user_agent', type: 'string'),
    new OA\Property(property: 'created_at', type: 'string'),
], type: 'object')]
#[OA\Schema(schema: 'Option', properties: [
    new OA\Property(property: 'label', type: 'string'),
    new OA\Property(property: 'value', type: 'integer'),
    new OA\Property(property: 'children', type: 'array', items: new OA\Items(ref: '#/components/schemas/Option')),
], type: 'object')]
#[OA\Schema(schema: 'MenuRoutes', type: 'array', items: new OA\Items(ref: '#/components/schemas/MenuRoute'))]
#[OA\Schema(schema: 'Options', type: 'array', items: new OA\Items(ref: '#/components/schemas/Option'))]
#[OA\Schema(schema: 'Rules', type: 'array', items: new OA\Items(ref: '#/components/schemas/Rule'))]
#[OA\Schema(schema: 'MenuIds', type: 'array', items: new OA\Items(type: 'string'))]
#[OA\Schema(schema: 'GroupPage', properties: [
    new OA\Property(property: 'list', type: 'array', items: new OA\Items(ref: '#/components/schemas/Group')),
    new OA\Property(property: 'page', type: 'integer'),
    new OA\Property(property: 'per_page', type: 'integer'),
    new OA\Property(property: 'total', type: 'integer'),
], type: 'object')]
#[OA\Schema(schema: 'AdminPage', properties: [
    new OA\Property(property: 'list', type: 'array', items: new OA\Items(ref: '#/components/schemas/Admin')),
    new OA\Property(property: 'page', type: 'integer'),
    new OA\Property(property: 'per_page', type: 'integer'),
    new OA\Property(property: 'total', type: 'integer'),
], type: 'object')]
#[OA\Schema(schema: 'OperationLogPage', properties: [
    new OA\Property(property: 'list', type: 'array', items: new OA\Items(ref: '#/components/schemas/OperationLog')),
    new OA\Property(property: 'page', type: 'integer'),
    new OA\Property(property: 'per_page', type: 'integer'),
    new OA\Property(property: 'total', type: 'integer'),
], type: 'object')]
final class ApiDocumentation
{
    public const SIGNATURE = [['appKey' => [], 'requestSignature' => [], 'requestTimestamp' => []]];
    public const ADMIN = [['accessToken' => []] + self::SIGNATURE[0]];
}
