<?php

declare(strict_types=1);

namespace App\Http\Admin\Subscriber\Logstash;

use App\Helper\Traits\Debugging;
use App\Http\Admin\Event\Logstash\RequestOperationEvent;
use App\Http\Admin\Service\Permission\AdminOpLogService;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Psr\Log\LoggerInterface;
use Swoole\Coroutine;
use Throwable;

/**
 * 管理员操作日志订阅者
 */
#[Listener]
class AdminOperationSubscriber implements ListenerInterface
{
    use Debugging;

    public function __construct(
        private readonly AdminOpLogService     $adminOpLogService,
        private readonly LoggerInterface       $logger,
        private readonly StdoutLoggerInterface $stdoutLogger
    ) {}

    public function listen(): array
    {
        return [
            RequestOperationEvent::class
        ];
    }

    /**
     *
     * @param object $event
     * @return void
     */
    public function process(object $event): void
    {
        if (!$event instanceof RequestOperationEvent) {
            return;
        }

        $data = [
            'admin_id' => $event->getAdminId(),
            'username' => $event->getUsername(),
            'method' => $event->getMethod(),
            'path' => $event->getPath(),
            'title' => $event->getTitle(),
            'body' => $event->getBody(),
            'ip' => $event->getIp(),
            'user_agent' => $event->getUserAgent(),
        ];

        Coroutine::create(function () use ($data) {
            try {
                $this->adminOpLogService->create($data);
            } catch (Throwable $e) {
                $this->log(message: sprintf('Admin Operation Log Subscriber Error: %s', $e->getMessage()), context: $data);
            }
        });
    }
}
