<?php
declare(strict_types=1);

namespace App\Helper\Enums;

use App\Http\Common\Enums\Cache;

/**
 * 验证码缓存键名枚举
 */
final class CaptchaEnum extends Cache
{
    /**
     * 枚举名称
     */
    public const LOGIN = 'LOGIN';
    public const REGISTER = 'REGISTER';
    public const RESET_PASSWORD = 'RESET_PASSWORD';
    public const BIND_PHONE = 'BIND_PHONE';
    public const BIND_EMAIL = 'BIND_EMAIL';
    public const CAPTCHA = 'CAPTCHA';

    /**
     * 枚举值
     */
    private const VALUES = [
        self::LOGIN => 'captcha:login:%s',
        self::REGISTER => 'captcha:register:%s',
        self::RESET_PASSWORD => 'captcha:reset_password:%s',
        self::BIND_PHONE => 'captcha:bind_phone:%s',
        self::BIND_EMAIL => 'captcha:bind_email:%s',
        self::CAPTCHA => 'captcha:verification:%s',
    ];

    /**
     * 描述信息
     */
    private const DESCRIPTIONS = [
        self::LOGIN => '登录验证码',
        self::REGISTER => '注册验证码',
        self::RESET_PASSWORD => '重置密码验证码',
        self::BIND_PHONE => '绑定手机验证码',
        self::BIND_EMAIL => '绑定邮箱验证码',
        self::CAPTCHA => '图文验证码',
    ];

    // 默认TTL配置（秒）
    private const DEFAULT_TTL = [
        self::CAPTCHA => 300, // 图形验证码，默认5分钟
    ];

    private function __construct(protected readonly string $name) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return self::VALUES[$this->name] ?? '';
    }

    public function getDescription(): string
    {
        return self::DESCRIPTIONS[$this->name] ?? '';
    }

    public function getDefaultTTL(): int
    {
        return self::DEFAULT_TTL[$this->name] ?? 300;
    }

    protected static function getAllValues(): array
    {
        return array_map(fn($name) => new self($name), array_keys(self::VALUES));
    }
}