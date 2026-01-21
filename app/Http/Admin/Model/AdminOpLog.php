<?php

declare(strict_types=1);

namespace App\Http\Admin\Model;


use App\Http\Common\Model\Model;
use Hyperf\Database\Model\SoftDeletes;

/**
 * 管理员操作日志模型
 *
 * @property int    $id           ID
 * @property int    $snowflake_id 雪花ID
 * @property int    $admin_id     管理员ID
 * @property string $username     管理员用户名
 * @property string $method       请求方法
 * @property string $path         请求路由
 * @property string $title        业务名称
 * @property string $body         请求内容体
 * @property string $ip           请求 IP
 * @property string $user_agent   请求 User-Agent
 * @property string $created_at   创建时间
 * @property string $updated_at   更新时间
 * @property string $deleted_at   删除时间
 */
final class AdminOpLog extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'admin_op_log';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'snowflake_id', 'admin_id', 'username', 'method', 'path', 'title', 'body', 'ip', 'user_agent'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [];

    // 默认过滤字段
    public array $defaultFilters = ['id', 'username', 'method', 'path', 'title', 'ip', 'body', 'user_agent', 'created_at'];

    // 详情过滤字段
    public array $detailFilters = ['id', 'username', 'method', 'path', 'title', 'ip', 'body', 'user_agent', 'created_at'];
}
