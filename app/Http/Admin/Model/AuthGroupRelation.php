<?php

declare(strict_types=1);

namespace App\Http\Admin\Model;


use App\Http\Common\Model\Model;

/**
 * 权限组关联
 *
 * @property int $admin_id
 * @property int $group_id
 */
final class AuthGroupRelation extends Model
{
    protected bool $enableSnowflake = false;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'auth_group_relation';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['admin_id', 'group_id'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['admin_id' => 'integer', 'group_id' => 'integer'];
}
