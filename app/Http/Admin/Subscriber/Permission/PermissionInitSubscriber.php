<?php

declare(strict_types=1);

namespace App\Http\Admin\Subscriber\Permission;

use App\Helper\Support\RedisCacheSupport;
use App\Http\Admin\Enums\Cache\AdminEnum;
use App\Http\Admin\Event\Permission\PermissionInitEvent;
use App\Http\Admin\Model\AuthRule;
use App\Http\Admin\Repository\Permission\GroupRepository;
use App\Http\Admin\Repository\Permission\RuleRepository;
use App\Http\Common\Enums\Status;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\MainWorkerStart;

/**
 * 权限初始化
 */
#[Listener]
class PermissionInitSubscriber implements ListenerInterface
{
    public function __construct(
        private readonly StdoutLoggerInterface $logger,
        private readonly RedisCacheSupport     $redisCacheSupport,
        private readonly GroupRepository       $groupRepository,
        private readonly RuleRepository        $ruleRepository,
    ) {}

    public function listen(): array
    {
        return [
            MainWorkerStart::class,
            PermissionInitEvent::class
        ];
    }

    public function process(object $event): void
    {
        if (!($event instanceof PermissionInitEvent || $event instanceof MainWorkerStart)) {
            return;
        }

        $this->logger->info('===> 后台权限初始化开始 <===');

        // 1. 获取角色组
        $groups = $this->groupRepository->getModel()
            ->where('id', '>', 1)
            ->pluck('rules', 'id')
            ->toArray();

        if (!$groups) {
            $this->logger->info('=== 未找到角色组，权限初始化跳过 ===');
            return;
        }

        // 2. 解析角色组规则
        $groupRuleMap = [];
        $allRuleIds = [];

        foreach ($groups as $groupId => $rules) {
            if (!$rules) {
                continue;
            }

            $ruleIds = array_values(array_filter(
                array_map('intval', explode(',', $rules))
            ));

            if (!$ruleIds) {
                continue;
            }

            $groupRuleMap[$groupId] = $ruleIds;
            $allRuleIds = array_merge($allRuleIds, $ruleIds);
        }

        $allRuleIds = array_values(array_unique($allRuleIds));

        if (!$allRuleIds) {
            $this->logger->info('=== 未找到任何权限规则 ===');
            return;
        }

        // 3. 查询权限
        $permissions = $this->ruleRepository->getModel()
            ->whereIn('id', $allRuleIds)
            ->where('type', AuthRule::TYPE_PERMISSION)
            ->where('status', Status::Normal->value)
            ->pluck('perm', 'id')
            ->toArray();

        if (!$permissions) {
            $this->logger->info('=== 权限规则查询结果为空 ===');
            return;
        }

        // 4. 写入 Redis
        foreach ($groupRuleMap as $groupId => $ruleIds) {
            $perms = [];

            foreach ($ruleIds as $ruleId) {
                if (isset($permissions[$ruleId])) {
                    $perms[] = $permissions[$ruleId];
                }
            }

            $this->redisCacheSupport->set(
                AdminEnum::fromName(AdminEnum::ADMIN_GROUP_PERMISSIONS),
                array_values(array_unique($perms)),
                $groupId
            );
        }

        $this->logger->info('===> 后台权限初始化完成 <===');
    }
}
