<?php
declare(strict_types=1);

namespace App\Http\Admin\Controller;

use App\Http\Admin\AbstractController;
use App\Http\Admin\Request\AdminRequest;
use App\Http\Admin\Service\LoginService;
use App\Http\Common\Result;
use Hyperf\Validation\Annotation\Scene;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * 登录控制器
 */
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
    public function refresh(AdminRequest $request): Result
    {
        $refreshToken = $request->validated()['refresh_token'];
        return $this->success($this->loginService->refresh($refreshToken, $request->getClientIp()));
    }
}