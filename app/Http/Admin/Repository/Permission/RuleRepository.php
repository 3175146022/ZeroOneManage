<?php
declare(strict_types=1);

namespace App\Http\Admin\Repository\Permission;

use App\Http\Admin\Model\AuthRule;
use App\Http\Common\Enums\Status;
use App\Http\Common\Repository\IRepository;
use Hyperf\Collection\Arr;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Collection;

/**
 * 权限-规则仓库类
 *
 * @extends IRepository<AuthRule>
 */
final class RuleRepository extends IRepository
{
    public function __construct(protected readonly AuthRule $model) {}

    /**
     * 获取菜单
     *
     * @param array $params
     * @return Collection
     */
    public function menus(array $params): Collection
    {
        return $this->getQuery()
            ->select($this->model->listFilters)
            ->where([
                ['pid', '=', 0],
                ['status', '=', Status::Normal->value],
                ['type', '!=', $this->model::TYPE_PERMISSION]
            ])
            ->with('menu')
            ->when(Arr::has($params, 'ids'), static function (Builder $query) use ($params) {
                $query->whereIn('id', Arr::get($params, 'ids'));
                $query->when(Arr::get($params, 'menu'), static function (Builder $query) use ($params) {
                    $query->withWhereHas('menu', static function ($query) use ($params) {
                        $query->whereIn('id', Arr::get($params, 'ids'));
                    });
                });
            })
            ->orderBy('weight', 'desc')
            ->get();

    }

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
            ->when(Arr::has($params, 'status'), static function (Builder $query) use ($params) {
                $query->where('status', Arr::get($params, 'status'));
            })
            ->when(Arr::get($params, 'children'), static function (Builder $query) {
                $query->with('children');
            });
    }
}