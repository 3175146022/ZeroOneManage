<?php

declare(strict_types=1);

namespace App\Http\Admin\Subscriber\Logstash;

use App\Helper\Support\RedisCacheSupport;
use App\Http\Admin\Enums\Cache\AdminEnum;
use App\Http\Admin\Event\Logstash\UserLoginEvent;
use App\Http\Admin\Model\AuthGroup;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;

/**
 * 缓存管理员权限
 */
#[Listener]
class CacheAdminPermissionSubscriber implements ListenerInterface
{
    public function __construct(
        private readonly RedisCacheSupport $cache,
    ) {}

    public function listen(): array
    {
        return [
            UserLoginEvent::class
        ];
    }

    public function process(object $event): void
    {
        if (!$event instanceof UserLoginEvent || !$event->isLogin()) {
            return;
        }

        $admin = $event->getAdmin();
        $admin->login_failure = $event->getLoginFailure();
        $admin->login_time = $event->getLoginTime();
        $adminInfo = $admin->makeHidden(['password', 'salt', 'token', 'refresh_token'])->toArray();
        // 获取角色代码
        $code = $admin->group->pluck('code')->filter()->unique()->values()->implode(',');
        $adminInfo['code'] = $code ?? '';
        $adminInfo['roles'] = $code === AuthGroup::SUPER_ADMIN ? ['*'] : $admin->group->pluck('id');
        // 缓存
        $this->cache->set(AdminEnum::fromName(AdminEnum::ADMIN_TOKEN), $adminInfo, $event->getToken());
        $this->cache->set(
            AdminEnum::fromName(AdminEnum::ADMIN_REFRESH_TOKEN),
            ['token' => $event->getToken(), 'id' => $admin->id],
            $event->getRefreshToken()
        );
    }
}
