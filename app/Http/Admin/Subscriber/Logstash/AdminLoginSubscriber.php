<?php

declare(strict_types=1);

namespace App\Http\Admin\Subscriber\Logstash;

use App\Helper\Support\RedisCacheSupport;
use App\Helper\Traits\Debugging;
use App\Http\Admin\Enums\Cache\AdminEnum;
use App\Http\Admin\Event\Logstash\UserLoginEvent;
use App\Http\Admin\Service\Permission\AdminService;
use Hyperf\Collection\Arr;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Psr\Log\LoggerInterface;
use Swoole\Coroutine;
use Throwable;

/**
 * 登录监听
 */
#[Listener]
class AdminLoginSubscriber implements ListenerInterface
{
    use Debugging;

    public function __construct(
        private readonly AdminService          $service,
        private readonly RedisCacheSupport     $cache,
        private readonly LoggerInterface       $logger,
        private readonly StdoutLoggerInterface $stdoutLogger
    ) {}

    public function listen(): array
    {
        return [
            UserLoginEvent::class
        ];
    }

    /**
     * @param object $event
     * @return void
     */
    public function process(object $event): void
    {
        if (!$event instanceof UserLoginEvent) {
            return;
        }

        $admin = $event->getAdmin();
        // 删除旧登录记录缓存
        if ($event->getOldToken() !== '') {
            $this->cache->del(AdminEnum::fromName(AdminEnum::ADMIN_TOKEN), $event->getOldToken());
        }
        if ($event->getOldRefreshToken() !== '') {
            $this->cache->del(AdminEnum::fromName(AdminEnum::ADMIN_REFRESH_TOKEN), $event->getOldRefreshToken());
        }
        // 更新登录记录信息
        $data = [
            'token' => $event->getToken(),
            'refresh_token' => $event->getRefreshToken(),
            'login_ip' => $event->getIp(),
            'login_failure' => $event->getLoginFailure(),
        ];
        // 登录时间(只有在登录登录成功或失败的时候记录)
        if ($event->getLoginFailure() > 0 || $event->getLoginTime()) {
            Arr::set($data, 'login_time', $event->getLoginTime());
        }

        // 更新数据库
        Coroutine::create(function () use ($data, $admin) {
            try {
                $this->service->updateById($admin->id, $data);
            } catch (Throwable $e) {
                $this->log(
                    message: sprintf('Admin Login Subscriber Error: %s', $e->getMessage()),
                    context: ['id' => $admin->id, 'data' => $data]
                );
            }
        });
    }
}
