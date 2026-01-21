<?php

declare(strict_types=1);

namespace App\Http\Admin\Event\Logstash;

/**
 * 管理员请求操作事件
 */
final class RequestOperationEvent
{
    public function __construct(
        private readonly int    $adminId,
        private readonly string $username,
        private readonly string $path,
        private readonly string $title,
        private readonly string $body,
        private readonly string $ip,
        private readonly string $userAgent = '',
        private readonly string $method = 'GET',
    ) {}

    public function getAdminId(): int
    {
        return $this->adminId;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    public function getMethod(): string
    {
        return $this->method;
    }
}
