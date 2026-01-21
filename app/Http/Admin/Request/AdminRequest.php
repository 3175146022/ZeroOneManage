<?php

declare(strict_types=1);

namespace App\Http\Admin\Request;

use App\Http\Admin\Model\Admin;
use App\Http\Admin\Model\AuthGroup;
use App\Http\Common\Enums\OldStatus;
use App\Http\Common\Enums\Status;
use App\Http\Common\Request\ClientIpTrait;
use App\Http\Common\Request\NoAuthorizeTrait;
use Hyperf\Validation\Request\FormRequest;
use Hyperf\Validation\Rule;
use function Hyperf\Translation\__;

/**
 * 管理员 请求验证
 */
class AdminRequest extends FormRequest
{
    use NoAuthorizeTrait;
    use ClientIpTrait;

    /**
     * 规则
     */
    public function rules(): array
    {
        $rules = [
            'id' => 'required|integer|gt:1',
            'group_ids' => 'required|array',
            'group_ids.*' => [
                'required', 'integer', 'gt:0',
                $this->validateAllExistInModel(AuthGroup::getModel())
            ],
            'username' => [
                'required', 'string', 'regex:/^\w{3,30}$/',
                Rule::unique(Admin::getModel()->getTable(), 'username')->ignore($this->post('id'))
            ],
            'nickname' => 'required|string',
            'mobile' => [
                'nullable', 'string', 'regex:/^1[3-9]\d{9}$/',
                Rule::unique(Admin::getModel()->getTable(), 'mobile')->ignore($this->post('id'))
            ],
            'email' => [
                'nullable', 'email',
                Rule::unique(Admin::getModel()->getTable(), 'email')->ignore($this->post('id'))
            ],
            'password' => 'required|string',
            'status' => 'required|integer|in:' . implode(',', [Status::Normal->value, Status::Hidden->value]),
            'captcha' => 'required|string',
            'captcha_key' => 'required|string',
            'refresh_token' => 'required|string',
        ];

        switch ($this->getScene()) {
            case 'update':
                $rules['password'] = 'nullable|string';
                break;
            case 'login':
                $rules['username'] = 'required|string';
                break;
            default:
                break;
        }

        return $rules;
    }

    /**
     * 自定义错误消息
     */
    public function messages(): array
    {
        return [
            'id.gt' => __('permission.admin.idGt'),
            'group_ids.required' => __('permission.admin.groupIdsRequired'),
            'username.required' => __('permission.admin.usernameRequired'),
            'username.regex' => __('permission.admin.usernameRegex'),
            'username.unique' => __('permission.admin.usernameUnique'),
            'nickname.required' => __('permission.admin.nicknameRequired'),
            'mobile.regex' => __('permission.admin.mobileRegex'),
            'mobile.unique' => __('permission.admin.mobileUnique'),
            'email.required' => __('permission.admin.emailRequired'),
            'email.email' => __('permission.admin.emailEmail'),
            'email.unique' => __('permission.admin.emailUnique'),
            'password.required' => __('permission.admin.passwordRequired'),
            'status.required' => __('permission.admin.statusRequired'),
        ];
    }

    /**
     * 场景验证规则
     *
     * @var array
     */
    protected array $scenes = [
        'create' => ['group_ids', 'group_ids.*', 'username', 'nickname', 'mobile', 'email', 'password', 'status'],
        'update' => ['id', 'group_id', 'group_ids.*', 'username', 'nickname', 'mobile', 'email', 'password', 'status'],
        'delete' => ['id'],
        'login' => ['username', 'password', 'captcha', 'captcha_key'],
        'refresh' => ['refresh_token'],
        'detail' => ['id'],
        'logout' => [],
    ];
}
