<?php
declare(strict_types=1);

namespace App\Http\Admin\Service\Permission;

use App\Helper\Support\CollectSupport;
use App\Http\Admin\Model\AuthRule;
use App\Http\Admin\Repository\Permission\RuleRepository;
use App\Http\Common\Enums\Status;
use App\Http\Common\Service\IService;
use Hyperf\Collection\Arr;
use Hyperf\Collection\Collection;
use Hyperf\Collection\Enumerable;
use Hyperf\Database\Model\Model;
use function Hyperf\Collection\collect;

/**
 * 权限-规则服务类
 *
 * @extends IService<AuthRule>
 */
final class RuleService extends IService
{
    public function __construct(
        protected readonly RuleRepository $repository,
        private readonly CollectSupport   $collectSupport,
    ) {}

    /**
     * 获取权限目录列表
     *
     * @param array $params
     * @return Collection
     */
    public function getList(array $params): Collection
    {
        Arr::set($params, 'pid', 0);
        Arr::set($params, 'order_by', 'weight');
        Arr::set($params, 'children', true);

        return $this->repository->setSelectScene('list')->list([
            ...$params
        ]);
    }

    /**
     * 获取所有权限目录
     *
     * @param array $params
     * @return Collection
     */
    public function getAll(array $params): Collection
    {
        return $this->repository->setSelectScene('list')->list([
            'status' => Status::Normal->value,
            ...$params
        ]);
    }

    /**
     * 获取当前用户权限路由
     *
     * @param array $params
     * @return array
     */
    public function getCurrentUserRoutes(array $params): array
    {
        $routes = $this->repository->menus($params);
        return collect($routes->toArray())
            ->map(fn($menu) => $this->transformMenuToRoute($menu))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * 获取权限下拉选项
     *
     * @return Enumerable
     */
    public function option(): Enumerable
    {
        $result = $this->getAll(['pid' => 0, 'children' => true, 'order_by' => 'weight desc,id asc']);
        return $this->collectSupport->optionCollect($result);
    }

    /**
     * 创建权限
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        if ($data['type'] == AuthRule::TYPE_DIRECTORY) {
            Arr::set($data, 'component', 'Layout');
        }
        return $this->repository->create($data);
    }

    // ====================================================================================================================
    // 私有方法
    // ====================================================================================================================

    /**
     * 菜单转路由
     *
     * @param array $menu
     * @return array|null
     */
    private function transformMenuToRoute(array $menu): ?array
    {
        $route = [
            'name' => $menu['route_name'] ?? '',
            'path' => $menu['route_path'] ?? '',
            'component' => $menu['component'] ?? '',
            'redirect' => $menu['redirect'] ?? '',
            'meta' => [
                'title' => $menu['name'],
                'icon' => $menu['icon'],
                'hidden' => $menu['status'] == Status::Hidden->value,
                'alwaysShow' => $menu['always_show'] == AuthRule::ALWAYS_SHOW_ENABLE,
                'keepAlive' => $menu['keep_alive'] == AuthRule::KEEP_ALIVE_ENABLE,
                'params' => null,
            ],
        ];

        // 递归处理 children
        if (!empty($menu['menu'])) {
            $children = collect($menu['menu'])
                ->map(fn($child) => $this->transformMenuToRoute($child))
                ->filter()
                ->values()
                ->all();

            if (!empty($children)) {
                $route['children'] = $children;
            }
        }

        return $route;
    }
}