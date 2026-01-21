<?php

namespace App\Http\Common\Request;

use App\Http\Common\Model\Model;
use Closure;
use function Hyperf\Translation\__;

/**
 * 通用请求验证
 */
trait BasicRequestTrait
{
    /**
     * 验证所有数据都存在
     *
     * @param Model  $model
     * @param string $compareField
     * @return Closure
     */
    public function validateAllExistInModel(Model $model, string $compareField = 'id'): Closure
    {
        return function ($attribute, $value, $fail) use ($model, $compareField) {
            // 统一转为数组
            $ids = match (true) {
                is_string($value) => explode(',', $value),
                is_int($value) => [$value],
                is_array($value) => $value,
                default => [],
            };

            // 允许空（交给 required 规则处理）
            if ($ids === []) {
                return;
            }

            // 查询存在的 ID 数量
            $existCount = $model::query()->whereIn($compareField, $ids)->count($compareField);

            if ($existCount !== count($ids)) {
                $fail(__('system.invalidDataExists'));
            }
        };
    }
}