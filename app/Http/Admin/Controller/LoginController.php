<?php
declare(strict_types=1);

namespace App\Http\Admin\Controller;

use App\Http\Admin\AbstractController;
use App\Http\Admin\Request\AdminRequest;
use App\Http\Admin\Service\LoginService;
use App\Http\Common\ApiDocumentation;
use App\Http\Common\Result;
use Hyperf\Swagger\Annotation\HyperfServer;
use Hyperf\Validation\Annotation\Scene;
use OpenApi\Attributes as OA;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * 登录控制器
 */
#[HyperfServer('http')]
final class LoginController extends AbstractController
{
    public function __construct(
        private readonly LoginService $loginService,
    ) {}

    /**
     * 获取图形验证码
     *
     * @return Result
     */
    #[OA\Get(
        path: '/admin/login/captcha',
        operationId: 'getLoginCaptcha',
        summary: '获取登录验证码图片',
        security: ApiDocumentation::SIGNATURE,
        tags: ['认证'],
        responses: [
            new OA\Response(
                response: 200,
                description: '响应体包含业务状态码、消息和数据',
                content: new OA\JsonContent(oneOf: [
                    new OA\Schema(allOf: [
                        new OA\Schema(ref: '#/components/schemas/ApiResponse'),
                        new OA\Schema(properties: [
                            new OA\Property(property: 'data', ref: '#/components/schemas/Captcha'),
                        ]),
                    ]),
                    new OA\Schema(ref: '#/components/schemas/ApiError'),
                ]),
            ),
            new OA\Response(response: 500, description: '服务内部错误'),
        ],
    )]
    public function captcha(): Result
    {
        return $this->success($this->loginService->captcha());
    }

    /**
     * 登录
     *
     * @param AdminRequest $request
     * @return Result
     */
    #[Scene('login')]
    #[OA\Post(
        path: '/admin/login',
        operationId: 'login',
        summary: '管理员登录',
        security: ApiDocumentation::SIGNATURE,
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/LoginRequest')),
        tags: ['认证'],
        responses: [
            new OA\Response(
                response: 200,
                description: '响应体包含业务状态码、消息和数据',
                content: new OA\JsonContent(oneOf: [
                    new OA\Schema(allOf: [
                        new OA\Schema(ref: '#/components/schemas/ApiResponse'),
                        new OA\Schema(properties: [
                            new OA\Property(property: 'data', ref: '#/components/schemas/TokenPair'),
                        ]),
                    ]),
                    new OA\Schema(ref: '#/components/schemas/ApiError'),
                ]),
            ),
            new OA\Response(response: 500, description: '服务内部错误'),
        ],
    )]
    public function login(AdminRequest $request): Result
    {
        return $this->success($this->loginService->login($request->validated(), $request->getClientIp()));
    }

    /**
     * 登出
     *
     * @param AdminRequest $request
     * @return Result
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Scene('logout')]
    #[OA\Post(
        path: '/admin/current_user/logout',
        operationId: 'logout',
        summary: '管理员登出并撤销会话',
        security: ApiDocumentation::ADMIN,
        tags: ['认证'],
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
    public function logout(AdminRequest $request): Result
    {
        $this->loginService->logout($this->getUserToken(), $request->getClientIp());
        return $this->success();
    }

    /**
     * 刷新令牌
     *
     * @param AdminRequest $request
     * @return Result
     */
    #[Scene('refresh')]
    #[OA\Post(
        path: '/admin/current_user/refresh_token',
        operationId: 'refreshToken',
        description: '同时在 X-Access-Token 请求头中提交当前访问令牌。',
        summary: '刷新管理员访问令牌',
        security: ApiDocumentation::ADMIN,
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(required: ['refresh_token'], properties: [new OA\Property(property: 'refresh_token', type: 'string')], type: 'object')),
        tags: ['认证'],
        responses: [
            new OA\Response(
                response: 200,
                description: '响应体包含业务状态码、消息和数据',
                content: new OA\JsonContent(oneOf: [
                    new OA\Schema(allOf: [
                        new OA\Schema(ref: '#/components/schemas/ApiResponse'),
                        new OA\Schema(properties: [
                            new OA\Property(property: 'data', ref: '#/components/schemas/TokenPair'),
                        ]),
                    ]),
                    new OA\Schema(ref: '#/components/schemas/ApiError'),
                ]),
            ),
            new OA\Response(response: 500, description: '服务内部错误'),
        ],
    )]
    public function refresh(AdminRequest $request): Result
    {
        $refreshToken = $request->validated()['refresh_token'];
        return $this->success($this->loginService->refresh($refreshToken, $request->getClientIp()));
    }
}