<?php
declare(strict_types=1);

namespace App\Helper\Support;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * UUID 支持类
 *
 * 提供生成和验证 UUID 的功能
 *
 * @package App\Helper\Support
 */
final class UuidSupport
{
    /**
     * 生成 UUID v1（基于时间和节点）
     */
    public function generateV1(): string
    {
        return Uuid::uuid1()->toString();
    }

    /**
     * 生成 UUID v3（基于命名空间和名称的 MD5 哈希）
     *
     * @param string $namespace
     * @param string $name
     * @return string
     */
    public function generateV3(string $namespace, string $name): string
    {
        return Uuid::uuid3($namespace, $name)->toString();
    }

    /**
     * 生成 UUID v4（随机）
     *
     * @return string
     */
    public function generateV4(): string
    {
        return Uuid::uuid4()->toString();
    }

    /**
     * 生成 UUID v5（基于命名空间和名称的 SHA-1 哈希）
     *
     * @param string $namespace
     * @param string $name
     * @return string
     */
    public function generateV5(string $namespace, string $name): string
    {
        return Uuid::uuid5($namespace, $name)->toString();
    }

    /**
     * 生成有序 UUID（适合数据库索引）
     *
     * @return string
     */
    public function generateOrdered(): string
    {
        return Uuid::uuid6()->toString();
    }

    /**
     * 验证 UUID 格式
     *
     * @param string $uuid
     * @return bool
     */
    public function isValid(string $uuid): bool
    {
        return Uuid::isValid($uuid);
    }

    /**
     * 从字符串创建 UUID 对象
     *
     * @param string $uuid
     * @return UuidInterface
     */
    public function fromString(string $uuid): UuidInterface
    {
        return Uuid::fromString($uuid);
    }
}