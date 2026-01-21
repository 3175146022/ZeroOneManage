<?php

declare(strict_types=1);

namespace App\Helper\Processor;

use Hyperf\Context\Context;
use Hyperf\Coroutine\Coroutine;
use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;
use Ramsey\Uuid\Uuid;

/**
 * 日志请求ID处理器
 *
 * @package App\Helper\Processor
 */
final class UuidRequestIdProcessor implements ProcessorInterface
{
    public const REQUEST_ID = 'log.request.id';

    /**
     * 处理日志记录
     *
     * @param LogRecord $record
     * @return LogRecord
     */
    public function __invoke(LogRecord $record): LogRecord
    {
        $record->extra['request_id'] = self::getUuid();
        return $record;
    }

    /**
     * 获取请求UUID
     *
     * @return string
     */
    public static function getUuid(): string
    {
        $requestId = Context::get(self::REQUEST_ID);
        if ($requestId) {
            return $requestId;
        }
        if (Coroutine::inCoroutine()) {
            $requestId = Context::get(self::REQUEST_ID, null, Coroutine::parentId());
            if ($requestId !== null) {
                self::setUuid($requestId);
                return $requestId;
            }
        }

        return self::setUuid(Uuid::uuid4()->toString());
    }

    /**
     * 设置请求UUID
     *
     * @param string $requestId
     * @return string
     */
    public static function setUuid(string $requestId): string
    {
        return Context::set(self::REQUEST_ID, $requestId);
    }
}
