<?php
declare(strict_types=1);

namespace App\Http\Admin\Repository\Permission;

use App\Http\Admin\Model\AuthGroup;
use App\Http\Common\Repository\IRepository;
use Hyperf\Collection\Arr;
use Hyperf\Database\Model\Builder;

/**
 * 权限-角色组仓库
 *
 * @extends IRepository<AuthGroup>
 */
final class GroupRepository extends IRepository
{
    public function __construct(protected readonly AuthGroup $model) {}

    /**
     * 查询
     *
     * @param Builder $query
     * @param array   $params
     * @return Builder
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        return $query
            ->when(Arr::get($params, 'id'), static function (Builder $query, $id) {
                $query->whereKey($id);
            })
            ->when(Arr::has($params, 'pid'), static function (Builder $query) use ($params) {
                $query->where('pid', Arr::get($params, 'pid'));
            })
            ->when(Arr::get($params, 'name'), static function (Builder $query, $name) {
                $query->where('name', 'like', "%{$name}%");
            })
            ->when(Arr::get($params, 'status'), static function (Builder $query, $status) {
                $query->where('status', $status);
            })
            ->when(Arr::get($params, 'children'), static function (Builder $query) {
                $query->with('children');
            });
    }
}