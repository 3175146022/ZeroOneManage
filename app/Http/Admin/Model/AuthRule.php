<?php

declare(strict_types=1);

namespace App\Http\Admin\Model;

use App\Http\Common\Enums\Status;
use App\Http\Common\Model\Model;
use Hyperf\Database\Model\Relations\HasMany;
use Hyperf\Database\Model\SoftDeletes;

/**
 * 权限 - 规则模型
 *
 * @property int    $id             ID
 * @property int    $pid            父级ID
 * @property int    $snowflake_id   雪花ID
 * @property int    $type           类型: 1 - 目录 2 - 菜单 3 - 权限
 * @property string $name           菜单名称
 * @property string $route_name     路由名称
 * @property string $route_path     路由路径
 * @property string $component      组件路径
 * @property string $redirect       路由跳转地址
 * @property string $icon           图标
 * @property string $perm           权限标识
 * @property string $always_show    【目录】只有一个子路由是否始终显示（1-是 2-否）
 * @property string $keep_alive     【菜单】是否开启页面缓存（1-是 2-否）
 * @property string $remark         备注
 * @property int    $weight         权重
 * @property Status $status         状态: 1-正常, 2-隐藏, 3-禁用
 * @property string $created_at     创建时间
 * @property string $updated_at     修改时间
 * @property string $deleted_at     删除时间
 */
final class AuthRule extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'auth_rule';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'pid', 'snowflake_id', 'type', 'name', 'route_name', 'route_path', 'component',
        'redirect', 'icon', 'perm', 'always_show', 'keep_alive', 'remark', 'weight', 'status',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'status' => Status::class
    ];

    /**
     * 列表筛选字段
     *
     * @var array|string[]
     */
    public array $listFilters = [
        'id', 'pid', 'type', 'name', 'route_name', 'route_path', 'component', 'always_show', 'keep_alive',
        'redirect', 'perm', 'icon', 'weight', 'status'
    ];

    // 默认筛选字段
    public array $detailFilters = [
        'id', 'pid', 'type', 'name', 'route_name', 'route_path', 'component', 'always_show', 'keep_alive',
        'redirect', 'perm', 'icon', 'weight', 'status'
    ];

    /**
     * 类型: 1 - 目录 2 - 菜单 3 - 权限
     */
    const TYPE_DIRECTORY = 1;
    const TYPE_MENU = 2;
    const TYPE_PERMISSION = 3;
    public static array $typeMap = [
        self::TYPE_DIRECTORY,
        self::TYPE_MENU,
        self::TYPE_PERMISSION,
    ];

    /**
     * 【目录】只有一个子路由是否始终显示（1-是 2-否）
     */
    const ALWAYS_SHOW_ENABLE = 1;
    const ALWAYS_SHOW_DISABLE = 2;
    public static array $alwaysShowMap = [
        self::ALWAYS_SHOW_ENABLE,
        self::ALWAYS_SHOW_DISABLE,
    ];

    /**
     * 【菜单】是否开启页面缓存（1-是 2-否）
     */
    const KEEP_ALIVE_ENABLE = 1;
    const KEEP_ALIVE_DISABLE = 2;
    public static array $keepAliveMap = [
        self::KEEP_ALIVE_ENABLE,
        self::KEEP_ALIVE_DISABLE,
    ];

    /**
     * 递归子级
     *
     * @return HasMany
     */
    public function children(): HasMany
    {
        return $this
            ->hasMany(self::class, 'pid', 'id')
            ->select(['id', 'pid', 'type', 'name', 'route_name', 'route_path', 'component', 'perm', 'always_show', 'keep_alive', 'redirect', 'icon', 'weight', 'status'])
            ->with('children')
            ->orderBy('weight', 'desc');
    }

    /**
     * 菜单(递归)
     *
     * @return HasMany
     */
    public function menu(): HasMany
    {
        return $this
            ->hasMany(self::class, 'pid', 'id')
            ->select(['id', 'pid', 'type', 'name', 'route_name', 'route_path', 'component', 'perm', 'always_show', 'keep_alive', 'redirect', 'icon', 'weight', 'status'])
            ->where('status', Status::Normal->value)
            ->where('type', '!=', self::TYPE_PERMISSION)
            ->with('menu')
            ->orderBy('weight', 'desc');
    }
}
