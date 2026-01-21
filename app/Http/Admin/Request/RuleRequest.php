<?php

declare(strict_types=1);

namespace App\Http\Admin\Request;

use App\Http\Admin\Model\AuthRule;
use App\Http\Common\Enums\Status;
use App\Http\Common\Request\NoAuthorizeTrait;
use Hyperf\Validation\Request\FormRequest;
use Hyperf\Validation\Rule;
use function Hyperf\Translation\__;

/**
 * 权限 - 规则请求验证
 */
class RuleRequest extends FormRequest
{
    use NoAuthorizeTrait;

    /**
     * 规则
     */
    public function rules(): array
    {

        return [
            'id' => 'required|integer',
            'pid' => 'required',
            'type' => 'required|in:' . implode(',', AuthRule::$typeMap),
            'name' => [
                'required',
                'max:100',
                Rule::unique(AuthRule::getModel()->getTable(), 'name')
                    ->ignore($this->post('id'))
                    ->where(fn($query) => $query->whereNull('deleted_at')),
            ],
            'route_name' => [
                'required_if:type,' . AuthRule::TYPE_MENU,
                'exclude_unless:type,' . AuthRule::TYPE_MENU,
                'string',
                'max:255',
            ],
            'route_path' => [
                'required_unless:type,' . AuthRule::TYPE_PERMISSION,
                'exclude_if:type,' . AuthRule::TYPE_PERMISSION,
                'string',
                'max:255',
            ],
            'component' => [
                'required_if:type,' . AuthRule::TYPE_MENU,
                'exclude_unless:type,' . AuthRule::TYPE_MENU,
                'string',
                'max:255',
            ],
            'icon' => [
                'required_unless:type,' . AuthRule::TYPE_PERMISSION,
                'max:50'
            ],
            'perm' => [
                'required_if:type,' . AuthRule::TYPE_PERMISSION,
                'exclude_unless:type,' . AuthRule::TYPE_PERMISSION,
                'string',
                'max:255',
            ],
            'redirect' => [
                'exclude_unless:type,' . AuthRule::TYPE_DIRECTORY,
                'nullable',
                'string',
                'max:255',
            ],
            'remark' => 'nullable|string|max:255',
            'weight' => 'required',
            'status' => 'required|integer|in:' . implode(',', [Status::Normal->value, Status::Hidden->value]),
            'always_show' => [
                'nullable', 'in:' . implode(',', AuthRule::$alwaysShowMap),
                'exclude_if:type,' . AuthRule::TYPE_PERMISSION,
            ],
            'keep_alive' => [
                'nullable', 'in:' . implode(',', AuthRule::$keepAliveMap),
                'exclude_unless:type,' . AuthRule::TYPE_MENU,
            ],
        ];
    }

    /**
     * 自定义错误消息
     */
    public function messages(): array
    {
        return [
            'id.required' => __('permission.rule.idRequired'),
            'pid.required' => __('permission.rule.pidRequired'),
            'type.required' => __('permission.rule.typeRequired'),
            'type.in' => __('permission.rule.typeIn'),
            'name.required' => __('permission.rule.nameRequired'),
            'name.unique' => __('permission.rule.nameUnique'),
            'route_name.required_if' => __('permission.rule.routeNameRequiredIf'),
            'route_path.required_unless' => __('permission.rule.routePathRequiredUnless'),
            'component.required_if' => __('permission.rule.componentRequiredIf'),
            'perm.required_if' => __('permission.rule.permRequiredIf'),
            'icon.required_unless' => __('permission.rule.iconRequiredUnless'),
            'weight.required' => __('permission.rule.weightRequired'),
            'status.required' => __('permission.rule.statusRequired'),
        ];
    }

    /**
     * 场景验证规则
     *
     * @var array
     */
    protected array $scenes = [
        'create' => ['pid', 'type', 'name', 'route_name', 'route_path', 'component', 'icon', 'perm', 'always_show', 'keep_alive', 'redirect', 'remark', 'weight', 'status'],
        'update' => ['id', 'pid', 'type', 'name', 'route_name', 'route_path', 'component', 'icon', 'perm', 'always_show', 'keep_alive', 'redirect', 'remark', 'weight', 'status'],
        'delete' => ['id'],
        'detail' => ['id'],
    ];
}
