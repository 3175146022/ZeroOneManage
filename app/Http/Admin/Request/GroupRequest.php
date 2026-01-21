<?php

declare(strict_types=1);

namespace App\Http\Admin\Request;

use App\Http\Admin\Model\AuthGroup;
use App\Http\Admin\Model\AuthRule;
use App\Http\Common\Enums\Status;
use App\Http\Common\Request\NoAuthorizeTrait;
use Hyperf\Validation\Request\FormRequest;
use Hyperf\Validation\Rule;
use function Hyperf\Translation\__;

/**
 * 权限 - 分组请求验证
 */
class GroupRequest extends FormRequest
{
    use NoAuthorizeTrait;

    /**
     * 规则
     */
    public function rules(): array
    {
        return [
            'id' => 'required|integer|gt:1',
            'pid' => [
                'required',
                'integer',
                Rule::exists(AuthGroup::getModel()->getTable(), 'id')
                    ->where(fn($query) => $query->whereNull('deleted_at'))
            ],
            'name' => 'required|string',
            'rules' => [
                'required',
                'string',
                'regex:/^\d+(,\d+)*$/',
                $this->validateAllExistInModel(AuthRule::getModel())
            ],
            'status' => 'required|integer|in:' . implode(',', [Status::Normal->value, Status::Hidden->value]),
        ];
    }

    /**
     * 自定义错误消息
     */
    public function messages(): array
    {
        return [
            'id.gt' => __('permission.group.idGt'),
            'pid.required' => __('permission.group.pidRequired'),
            'pid.exists' => __('permission.group.pidExists'),
            'name.required' => __('permission.group.nameRequired'),
            'rules.required' => __('permission.group.rulesRequired'),
            'rules.regex' => __('permission.group.rulesRegex'),
            'status.required' => __('permission.group.statusRequired'),
        ];
    }

    /**
     * 场景验证规则
     *
     * @var array
     */
    protected array $scenes = [
        'create' => ['pid', 'name', 'rules', 'status'],
        'update' => ['id', 'pid', 'name', 'rules', 'status'],
        'delete' => ['id'],
        'detail' => ['id'],
        'getAdminMenuIds' => ['id'],
    ];
}
