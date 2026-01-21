<?php
declare(strict_types=1);

namespace App\Http\Admin\Repository\Permission;

use App\Http\Admin\Model\Admin;
use App\Http\Common\Repository\IRepository;
use Hyperf\Collection\Arr;
use Hyperf\Database\Model\Builder;

/**
 * 权限-管理员仓库
 *
 * @extends IRepository<Admin>
 */
final class AdminRepository extends IRepository
{
    public function __construct(protected readonly Admin $model) {}

    /**
     * 根据用户名查找管理员
     *
     * @param string $username
     * @return Admin
     */
    public function findByUsername(string $username): Admin
    {
        return $this->model
            ->newQuery()
            ->select($this->model->findByUserNameFilters)
            ->where('username', $username)
            ->firstOrFail();
    }

    /**
     * 处理搜索条件
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
            ->when(Arr::get($params, 'username'), static function (Builder $query, $username) {
                $query->where('username', 'like', '%' . $username . '%');
            })
            ->when(Arr::get($params, 'mobile'), static function (Builder $query, $mobile) {
                $query->where('mobile', $mobile);
            })
            ->when(Arr::get($params, 'email'), static function (Builder $query, $email) {
                $query->where('email', $email);
            })
            ->when(Arr::exists($params, 'nickname'), static function (Builder $query) use ($params) {
                $query->where('nickname', 'like', '%' . Arr::get($params, 'nickname') . '%');
            })
            ->when(Arr::get($params, 'status'), static function (Builder $query, $status) {
                $query->where('status', $status);
            })
            ->when(Arr::get($params, 'group'), static function (Builder $query) {
                $query->with('group');
            })
            ->when(Arr::get($params, 'groupName'), static function (Builder $query) {
                $query->with('group:id,name');
            });
    }
}