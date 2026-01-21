<?php
declare(strict_types=1);

namespace App\Http\Admin\Service\Permission;

use App\Helper\Support\CollectSupport;
use App\Http\Admin\Model\AuthGroup;
use App\Http\Admin\Repository\Permission\GroupRepository;
use App\Http\Common\Service\IService;
use Hyperf\Collection\Arr;
use Hyperf\Collection\Enumerable;
use function Hyperf\Collection\collect;

/**
 * 权限-角色组服务
 *
 * @extends IService<AuthGroup>
 */
final class GroupService extends IService
{
    public function __construct(
        protected readonly GroupRepository $repository,
        private readonly CollectSupport    $collectSupport,
    ) {}

    /**
     * 分页列表
     *
     * @param array $params
     * @param int   $page
     * @param int   $perPage
     * @return array
     */
    public function page(array $params, int $page = 1, int $perPage = 10): array
    {
        Arr::set($params, 'pid', 1);
        Arr::set($params, 'children', true);
        return $this->repository->setSelectScene('list')->page($params, $page, $perPage);
    }

    /**
     * 是否是超级管理员
     *
     * @param int $id
     * @return bool
     */
    public function isSuperAdmin(int $id): bool
    {
        return $this->repository->getModel()->where(['id' => $id, 'code' => AuthGroup::SUPER_ADMIN])->exists();
    }

    /**
     * 获取角色权限分组下拉选项
     *
     * @return Enumerable
     */
    public function option(): Enumerable
    {
        $result = $this->repository->setSelectScene('option')->list(['children' => true, 'pid' => 0, 'order_by' => 'weight desc,id asc']);
        return $this->collectSupport->optionCollect($result);
    }

    /**
     * 获取角色权限分组菜单ID
     *
     * @param int $id
     * @return array
     */
    public function getGroupMenuIds(int $id): array
    {
        $rules = $this->repository->findByField($id, 'rules');
        return collect(explode(',', (string)$rules))
            ->map(fn($item) => trim($item))
            ->values()
            ->all();
    }

    /**
     * 参数格式化
     *
     * @param array $params
     * @return array
     */
    public function paramsFormat(array $params): array
    {
        $params['rules'] = implode(',', array_unique(explode(',', $params['rules'])));
        return $params;
    }
}