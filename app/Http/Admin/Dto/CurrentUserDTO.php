<?php

namespace App\Http\Admin\Dto;

use App\Http\Admin\Model\AuthGroup;
use App\Http\Common\Enums\OldStatus;
use App\Http\Common\Enums\Status;

/**
 * 当前用户信息
 */
final class CurrentUserDTO
{
    public function __construct(
        public readonly int    $id,
        public readonly string $username,
        public readonly string $nickname,
        public readonly string $avatar,
        public readonly string $status,
        public readonly int    $loginFailure,
        public readonly string $loginTime,
        public readonly int    $isSuperAdmin, // 是否是超管权限
        public readonly string $code,         // 超管标识代码
        public readonly array  $permissions,
    ) {}

    public function isNormal(): bool
    {
        return Status::from($this->status)->isNormal();
    }

    public function isHidden(): bool
    {
        return Status::from($this->status)->isHidden();
    }

    public function isDisabled(): bool
    {
        return Status::from($this->status)->isDisable();
    }

    public function isSuperAdmin(): bool
    {
        return $this->code === AuthGroup::SUPER_ADMIN;
    }
}