<?php

declare(strict_types=1);

namespace App\Helper\Traits;

use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * 调试辅助 Trait
 *
 * @package App\Helper\Traits
 */
trait Debugging
{
    /**
     * 日志记录器
     *
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function isDebug(): bool
    {
        return (bool)ApplicationContext::getContainer()->get(ConfigInterface::class)->get('debug');
    }

    /**
     * 日志记录
     *
     * @param        $message
     * @param array  $context
     * @param string $level
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function log($message, array $context = [], string $level = 'error'): void
    {
        $this->isDebug() ? $this->stdoutLogger->{$level}($message, $context) : $this->logger->{$level}($message, $context);
    }
}
