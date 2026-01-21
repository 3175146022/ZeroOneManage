<?php
declare(strict_types=1);

namespace App\Http\Admin\Repository\Permission;

use App\Http\Admin\Model\AdminOpLog;
use App\Http\Common\Repository\IRepository;
use Hyperf\Collection\Arr;
use Hyperf\Database\Model\Builder;

/**
 * 管理员操作记录仓库
 *
 * @extends IRepository<AdminOpLog>
 */
final class AdminOpLogRepository extends IRepository
{
    public function __construct(protected readonly AdminOpLog $model) {}

    /**
     * 搜索条件
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
            });
    }
}