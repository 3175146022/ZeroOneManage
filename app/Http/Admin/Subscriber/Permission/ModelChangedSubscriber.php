<?php

declare(strict_types=1);

namespace App\Http\Admin\Subscriber\Permission;

use App\Http\Admin\Event\Permission\PermissionInitEvent;
use App\Http\Admin\Model\AuthGroup;
use App\Http\Admin\Model\AuthRule;
use Hyperf\Context\ApplicationContext;
use Hyperf\Database\Model\Events\Created;
use Hyperf\Database\Model\Events\Deleted;
use Hyperf\Database\Model\Events\Updated;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * 模型监听-权限初始化
 */
#[Listener]
class ModelChangedSubscriber implements ListenerInterface
{
    public function listen(): array
    {
        return [
            Created::class,
            Updated::class,
            Deleted::class,
        ];
    }

    /**
     * 监听处理
     *
     * @param object $event
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function process(object $event): void
    {
        // 获取模型
        if (!method_exists($event, 'getModel')) {
            return;
        }

        // 只监听指定模型
        $model = $event->getModel();
        if (!($model instanceof AuthGroup || $model instanceof AuthRule)) {
            return;
        }

        // 触发业务事件
        $dispatcher = ApplicationContext::getContainer()->get(EventDispatcherInterface::class);
        $dispatcher->dispatch(new PermissionInitEvent());
    }
}
