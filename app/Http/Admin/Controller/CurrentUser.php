<?php

namespace App\Http\Admin\Controller;

use App\Http\Admin\AbstractController;
use App\Http\Admin\Dto\CurrentUserDTO;
use App\Http\Admin\Service\Permission\AdminService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * 当前登录用户
 */
final class CurrentUser extends AbstractController
{
    public function __construct(
        private readonly AdminService $service,
    ) {}

    /**
     * 当前登录账户
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function user(): ?CurrentUserDTO
    {
        return $this->service->getCurrentUser($this->getUserToken());
    }

    /**
     * 当前登录账户ID
     *
     * @return int
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function id(): int
    {
        return $this->user()?->id ?? 0;
    }
}