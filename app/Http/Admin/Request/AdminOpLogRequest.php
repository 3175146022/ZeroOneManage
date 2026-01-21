<?php

declare(strict_types=1);

namespace App\Http\Admin\Request;

use App\Http\Common\Request\NoAuthorizeTrait;
use Hyperf\Validation\Request\FormRequest;

/**
 * 管理员日志请求
 */
class AdminOpLogRequest extends FormRequest
{
    use NoAuthorizeTrait;

    /**
     * 规则
     */
    public function rules(): array
    {
        return [
            'id' => 'required|integer|gt:0',
        ];
    }

    /**
     * 自定义错误消息
     */
    public function messages(): array
    {
        return [];
    }

    /**
     * 场景验证规则
     *
     * @var array
     */
    protected array $scenes = [
        'detail' => ['id'],
    ];
}
