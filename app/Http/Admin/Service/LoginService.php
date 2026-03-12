<?php

namespace App\Http\Admin\Service;

use App\Exception\BusinessException;
use App\Helper\Component\Captcha;
use App\Helper\Enums\CaptchaEnum;
use App\Helper\Support\EncryptionSupport;
use App\Helper\Support\RedisCacheSupport;
use App\Helper\Support\UuidSupport;
use App\Http\Admin\Enums\Cache\AdminEnum;
use App\Http\Admin\Event\Logstash\UserLoginEvent;
use App\Http\Admin\Repository\Permission\AdminRepository;
use App\Http\Common\Service\IService;
use Carbon\Carbon;
use Hyperf\Database\Model\ModelNotFoundException;
use Hyperf\Validation\UnauthorizedException;
use Psr\EventDispatcher\EventDispatcherInterface;
use function Hyperf\Translation\__;

/**
 * 登录服务
 */
final class LoginService extends IService
{
    public function __construct(
        private readonly UuidSupport              $uuidSupport,
        private readonly Captcha                  $captcha,
        private readonly EncryptionSupport        $encryptionSupport,
        private readonly EventDispatcherInterface $dispatcher,
        private readonly RedisCacheSupport        $redisCacheSupport,
        protected readonly AdminRepository        $repository,
    ) {}

    /**
     * 获取图形验证码
     *
     * @return array
     */
    public function captcha(): array
    {
        $uuid = $this->uuidSupport->generateV1();
        $captchaCacheKey = CaptchaEnum::fromName(CaptchaEnum::CAPTCHA);
        return $this->captcha->generate(key: $captchaCacheKey, keyParams: $uuid);
    }

    /**
     * 登录
     *
     * @param array  $params 参数
     * @param string $ip     ip
     * @return array
     */
    public function login(array $params, string $ip): array
    {
        try {
            // 验证码校验
            $captchaCacheKey = CaptchaEnum::fromName(CaptchaEnum::CAPTCHA);
            $this->captcha->validate($captchaCacheKey, $params['captcha'] ?? '', $params['captcha_key'] ?? '');

            // 账号校验
            $admin = $this->repository->findByUsername($params['username']);
            if ($admin->status->isHidden()) {
                throw new BusinessException(message: __('permission.login.accountDisable'));
            }

            // 密码校验
            $nowTime = Carbon::now()->timestamp;
            $password = $this->encryptionSupport->encryptPassword($params['password'], $admin->salt);
            if ($password !== $admin->password) {
                $loginFailureTimes = $admin->login_failure + 1;
                $this->dispatcher->dispatch(new UserLoginEvent(
                    admin: $admin,
                    ip: $ip,
                    loginFailure: $loginFailureTimes,
                    oldToken: $admin->token,
                    oldRefreshToken: $admin->refresh_token,
                    isLogin: false
                ));
                throw new BusinessException(message: __('permission.login.passwordError'));
            }

            // 登录成功
            $token = $this->uuidSupport->generateV4();
            $refreshToken = $this->uuidSupport->generateV4();
            $this->dispatcher->dispatch(new UserLoginEvent(
                admin: $admin,
                ip: $ip,
                loginFailure: 0,
                loginTime: $nowTime,
                token: $token,
                oldToken: $admin->token,
                refreshToken: $refreshToken,
                oldRefreshToken: $admin->refresh_token,
                isLogin: true
            ));

            return [
                'access_token' => $token,
                'refresh_token' => $refreshToken
            ];
        } catch (ModelNotFoundException) {
            throw new BusinessException(message: __('permission.login.accountNotExists'));
        }
    }

    /**
     * 登出
     *
     * @param string $token
     * @param string $ip
     * @return void
     */
    public function logout(string $token, string $ip): void
    {
        // 获取当前管理员信息
        $adminCache = $this->redisCacheSupport->get(AdminEnum::fromName(AdminEnum::ADMIN_TOKEN), $token);
        if (!$adminCache) {
            throw new UnauthorizedException();
        }

        $admin = $this->repository->setSelectScene('findByUserName')->findById($adminCache['id']);
        $this->dispatcher->dispatch(new UserLoginEvent(
            admin: $admin,
            ip: $ip,
            loginFailure: 0,
            oldToken: $admin->token,
            oldRefreshToken: $admin->refresh_token,
            isLogin: false
        ));
    }

    /**
     * 刷新令牌
     *
     * @param string $refreshToken 刷新令牌
     * @param string $ip           ip
     * @return array
     */
    public function refresh(string $refreshToken, string $ip): array
    {
        $refreshInfo = $this->redisCacheSupport->get(AdminEnum::fromName(AdminEnum::ADMIN_REFRESH_TOKEN), $refreshToken);
        if (!$refreshInfo) {
            throw new UnauthorizedException();
        }
        $admin = $this->repository->setSelectScene('findByUserName')->findById($refreshInfo['id']);
        $newToken = $this->uuidSupport->generateV4();
        $newRefreshToken = $this->uuidSupport->generateV4();
        $this->dispatcher->dispatch(new UserLoginEvent(
            admin: $admin,
            ip: $ip,
            loginFailure: 0,
            loginTime: $admin->login_time ? Carbon::parse($admin->login_time)->timestamp : null,
            token: $newToken,
            oldToken: $refreshInfo['token'],
            refreshToken: $newRefreshToken,
            oldRefreshToken: $refreshToken,
        ));

        return ['access_token' => $newToken, 'refresh_token' => $newRefreshToken];
    }
}