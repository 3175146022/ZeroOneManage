<?php
declare(strict_types=1);

namespace App\Http\Common\Enums\Oauth;

use App\Http\Common\Enums\Cache;

/**
 * Oauth枚举
 */
final class OauthEnum extends Cache
{
    /**
     * 枚举名称
     */
    public const OAUTH = 'OAUTH';

    /**
     * 枚举值
     */
    private const VALUES = [
        self::OAUTH => 'oauth:certificate:%s',
    ];

    /**
     * 描述信息
     */
    private const DESCRIPTIONS = [
        self::OAUTH => '接口签名证书',
    ];

    // 默认TTL配置（秒）
    private const DEFAULT_TTL = [
        self::OAUTH => 60 * 60 * 2,
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
