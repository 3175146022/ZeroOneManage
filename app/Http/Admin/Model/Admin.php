<?php

declare(strict_types=1);

namespace App\Http\Admin\Model;

use App\Http\Common\Enums\Status;
use App\Http\Common\Model\Model;
use Carbon\Carbon;
use Hyperf\Database\Model\Events\Deleting;
use Hyperf\Database\Model\Relations\BelongsToMany;

/**
 * 管理员模型
 *
 * @property int    $id             ID
 * @property int    $snowflake_id   雪花ID
 * @property string $username       用户名
 * @property string $nickname       昵称
 * @property string $password       密码
 * @property string $salt           密码盐
 * @property string $avatar         头像
 * @property string $email          邮箱
 * @property string $mobile         手机号
 * @property int    $login_failure  登录失败次数
 * @property int    $login_time     登录时间
 * @property string $login_ip       登录IP
 * @property int    $created_at     创建时间
 * @property int    $updated_at     更新时间
 * @property int    $deleted_at     删除时间
 * @property string $token          登录Token
 * @property string $refresh_token  刷新Token
 * @property Status $status         状态
 * @property string $is_super       超级管理权限：1-是 2-否
 */
final class Admin extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'admin';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'snowflake_id', 'username', 'nickname', 'password', 'salt', 'avatar', 'email', 'mobile',
        'login_failure', 'login_time', 'login_ip', 'token', 'refresh_token', 'status'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'status' => Status::class,
    ];

    /**
     * 默认属性
     *
     * @var array|string[]
     */
    protected array $attributes = [
        'avatar' => '/avtar.jpeg',
    ];

    // 列表过滤字段
    public array $listFilters = ['id', 'username', 'nickname', 'email', 'mobile', 'status', 'login_time'];

    // 详情筛选字段
    public array $detailFilters = ['id', 'username', 'nickname', 'avatar', 'email', 'mobile', 'status'];

    // 通过用户昵称查询用户信息字段筛选
    public array $findByUserNameFilters = [
        'id', 'username', 'nickname', 'avatar', 'password', 'salt', 'status',
        'login_failure', 'token', 'refresh_token', 'login_time', 'is_super'
    ];

    /**
     * 超级管理权限：1-是 2-否
     */
    public const IS_SUPER_ADMIN = 1;
    public const IS_NOT_SUPER_ADMIN = 2;

    /**
     * 设置登录时间属性
     *
     * @param $value
     * @return void
     */
    public function setLoginTimeAttribute($value): void
    {
        $this->attributes['login_time'] = Carbon::createFromTimestamp($value)->format('Y-m-d H:i:s');
    }

    /**
     * 删除事件
     *
     * @param Deleting $event
     */
    public function deleting(Deleting $event): void
    {
        // 删除权限组关联
        $this->group()->detach();
    }

    /**
     * 用户组
     *
     * @return BelongsToMany
     */
    public function group(): BelongsToMany
    {
        return $this->belongsToMany(
            AuthGroup::class,
            AuthGroupRelation::getModel()->getTable(),
            'admin_id',
            'group_id'
        );
    }
}
