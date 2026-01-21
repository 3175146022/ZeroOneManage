<?php

declare(strict_types=1);

namespace App\Http\Admin\Model;

use App\Http\Common\Enums\Status;
use App\Http\Common\Model\Model;
use Hyperf\Database\Model\Relations\HasMany;
use Hyperf\Database\Model\SoftDeletes;

/**
 * 用户权限角色分组模型
 *
 * @property int    $id           ID
 * @property int    $pid          父级ID
 * @property int    $snowflake_id 雪花ID
 * @property string $name         用户组名称
 * @property string $code         角色代码
 * @property string $rules        用户组拥有的规则
 * @property Status $status       状态: 1-正常, 2-隐藏, 3-禁用
 * @property int    $created_at   创建时间
 * @property int    $updated_at   更新时间
 * @property int    $deleted_at   删除时间
 */
final class AuthGroup extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'auth_group';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['pid', 'snowflake_id', 'name', 'rules', 'status'];

    /**
     * The attributes that should be hidden for arrays.
     */
    protected array $hidden = ['pivot'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'status' => Status::class
    ];

    // 列表筛选项
    public array $listFilters = ['id', 'pid', 'name', 'status'];

    // select下拉选项字段筛选
    public array $optionFilters = ['id', 'pid', 'name'];

    // 详情字段筛选
    public array $detailFilters = ['id', 'pid', 'name', 'status'];

    /**
     * 超级管理员
     */
    public const SUPER_ADMIN = 'SuperAdmin';

    /**
     * 获取子分组
     *
     * @return HasMany
     */
    public function children(): HasMany
    {
        return $this
            ->hasMany(self::class, 'pid', 'id')
            ->select(['id', 'pid', 'name', 'status'])
            ->where('status', Status::Normal->value)
            ->with('children');
    }
}
