<?php

declare(strict_types=1);

namespace App\Http\Common\Model;

use App\Http\Common\Repository\Traits\HasSnowflakeTrait;
use Hyperf\DbConnection\Model\Model as BaseModel;

/**
 * 统一抽象模型基类
 */
abstract class Model extends BaseModel
{
    /**
     * 雪花ID
     */
    use HasSnowflakeTrait;
}
