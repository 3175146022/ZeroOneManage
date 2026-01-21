<?php

declare(strict_types=1);

namespace App\Listener;

use App\Helper\Traits\Debugging;
use Hyperf\Collection\Arr;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Database\Events\QueryExecuted;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Logger\LoggerFactory;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;

/**
 * 数据库查询执行监听器
 *
 * @package App\Listener
 */
#[Listener]
class DbQueryExecutedListener implements ListenerInterface
{
    use Debugging;

    private LoggerInterface $logger;

    public function __construct(
        private readonly LoggerFactory         $loggerFactory,
        private readonly StdoutLoggerInterface $stdoutLogger
    )
    {
        $this->logger = $loggerFactory->get(group: 'sql');
    }

    public function listen(): array
    {
        return [
            QueryExecuted::class,
        ];
    }

    /**
     * @param object $event
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function process(object $event): void
    {
        if ($event instanceof QueryExecuted) {
            $sql = $event->sql;
            if (!Arr::isAssoc($event->bindings)) {
                $position = 0;
                foreach ($event->bindings as $value) {
                    $position = mb_strpos($sql, '?', $position);
                    if ($position === false) {
                        break;
                    }
                    $value = "'$value'";
                    $before = mb_substr($sql, 0, $position);
                    $after = mb_substr($sql, $position + 1, mb_strlen($sql) - $position);
                    $sql = $before . $value . $after;
                    $position += mb_strlen($value);
                }
            }

            if ($this->isDebug()) {
                // Debug 模式记录所有 SQL 查询
                $this->log(
                    message: sprintf('[%s:%s] %s', $event->connectionName, $event->time, $sql),
                    level: 'info'
                );
            } else {
                // 正式服只记录慢查询警告（1秒以上）
                if ($event->time > 1000) {
                    $this->log(
                        message: sprintf('Slow SQL Query: [%s:%s] %s', $event->connectionName, $event->time, $sql),
                        level: 'warning'
                    );
                }
            }
        }
    }
}
