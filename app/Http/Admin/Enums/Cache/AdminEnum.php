<?php
declare(strict_types=1);

namespace App\Http\Admin\Enums\Cache;

use App\Http\Common\Enums\Cache;

/**
 * 管理员缓存枚举
 */
final class AdminEnum extends Cache
{
    /**
     * 枚举名称
     */
    public const ADMIN_TOKEN = 'ADMIN_TOKEN';
    public const ADMIN_REFRESH_TOKEN = 'ADMIN_REFRESH_TOKEN';
    public const ADMIN_GROUP_PERMISSIONS = 'ADMIN_GROUP_PERMISSIONS';

    /**
     * 枚举值
     */
    private const VALUES = [
        self::ADMIN_TOKEN => 'admin:auth:token:%s',
        self::ADMIN_REFRESH_TOKEN => 'admin:auth:refresh_token:%s',
        self::ADMIN_GROUP_PERMISSIONS => 'admin:group:permissions:%s',
    ];

    /**
     * 描述信息
     */
    private const DESCRIPTIONS = [
        self::ADMIN_TOKEN => '管理员信息',
        self::ADMIN_REFRESH_TOKEN => '管理员刷新令牌',
        self::ADMIN_GROUP_PERMISSIONS => '管理员组权限',
    ];

    // 默认TTL配置（秒）
    private const DEFAULT_TTL = [
        self::ADMIN_TOKEN => 60 * 60 * 2,
        self::ADMIN_REFRESH_TOKEN => 60 * 60 * 24 * 3,
        self::ADMIN_GROUP_PERMISSIONS => -1,
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
