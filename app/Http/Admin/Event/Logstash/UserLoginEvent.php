<?php

namespace App\Http\Admin\Event\Logstash;

use App\Http\Admin\Model\Admin;

/**
 * 用户登录事件
 */
final class UserLoginEvent
{
    public function __construct(
        private readonly Admin  $admin,
        private readonly string $ip,
        private readonly int    $loginFailure,
        private readonly ?int   $loginTime = null,
        private readonly string $token = '',
        private readonly string $oldToken = '',
        private readonly string $refreshToken = '',
        private readonly string $oldRefreshToken = '',
        private readonly bool   $isLogin = true,
    ) {}

    public function getAdmin(): Admin
    {
        return $this->admin;
    }

    public function isLogin(): bool
    {
        return $this->isLogin;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function getLoginTime(): ?int
    {
        return $this->loginTime;
    }

    public function getLoginFailure(): int
    {
        return $this->loginFailure;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getOldToken(): string
    {
        return $this->oldToken;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function getOldRefreshToken(): string
    {
        return $this->oldRefreshToken;
    }
}