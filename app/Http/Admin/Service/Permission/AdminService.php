<?php
declare(strict_types=1);

namespace App\Http\Admin\Service\Permission;

use App\Helper\Support\EncryptionSupport;
use App\Helper\Support\RedisCacheSupport;
use App\Http\Admin\Dto\CurrentUserDTO;
use App\Http\Admin\Enums\Cache\AdminEnum;
use App\Http\Admin\Model\Admin;
use App\Http\Admin\Model\AuthGroup;
use App\Http\Admin\Repository\Permission\AdminRepository;
use App\Http\Common\Service\IService;
use Hyperf\Collection\Arr;
use Hyperf\Database\Model\Model;
use Hyperf\DbConnection\Db;
use Hyperf\Stringable\Str;
use function Hyperf\Collection\collect;

/**
 * 权限-管理员服务
 *
 * @extends IService<Admin>
 */
final class AdminService extends IService
{
    public function __construct(
        protected readonly AdminRepository $repository,
        private readonly EncryptionSupport $encryptionSupport,
        private readonly RedisCacheSupport $redisCacheSupport,
    ) {}

    /**
     * 分页列表
     *
     * @param array $params
     * @param int   $page
     * @param int   $perPage
     * @return array
     */
    public function page(array $params, int $page = 1, int $perPage = 10): array
    {
        $params = Arr::add($params, 'groupName', true);
        return $this->repository->setSelectScene('list')->page($params, $page, $perPage);
    }

    /**
     * 详情
     *
     * @param int $id
     * @return Model
     */
    public function detail(int $id): Model
    {
        $params = [];
        $params = Arr::add($params, 'id', $id);
        $params = Arr::add($params, 'groupName', true);
        return $this->repository->setSelectScene('detail')->findByFilter($params);
    }

    /**
     * 创建
     *
     * @param array $data
     * @return mixed
     */
    public function createAdmin(array $data): mixed
    {
        return Db::transaction(function () use ($data) {
            $data = $this->paramsFormat($data);
            $this->repository->create($data)->group()->sync($data['group_ids']);
        });
    }

    /**
     * 修改
     *
     * @param array $data
     * @return mixed
     */
    public function modify(array $data): mixed
    {
        return Db::transaction(function () use ($data) {
            $data = $this->paramsFormat($data);
            $this->repository->saveById($data['id'], $data)->group()->sync($data['group_ids']);
        });
    }


    /**
     * 获取当前用户
     *
     * @param string $token
     * @return CurrentUserDTO|null
     */
    public function getCurrentUser(string $token): ?CurrentUserDTO
    {
        if (!$token) {
            return null;
        }

        $user = $this->redisCacheSupport->get(AdminEnum::fromName(AdminEnum::ADMIN_TOKEN), $token) ?: [];
        if (empty($user)) {
            return null;
        }

        // 获取权限
        $user['permissions'] = ['*'];
        if ($user['code'] !== AuthGroup::SUPER_ADMIN) {
            $permissions = [];
            foreach ($user['roles'] as $roleId) {
                $permissions[] = $this->redisCacheSupport->get(AdminEnum::fromName(AdminEnum::ADMIN_GROUP_PERMISSIONS), $roleId) ?: [];
            }
            $user['permissions'] = array_keys(array_flip(array_merge(...$permissions)));
        }

        return new CurrentUserDTO(
            id: (int)$user['id'],
            username: (string)$user['username'],
            nickname: (string)$user['nickname'],
            avatar: (string)$user['avatar'],
            status: (string)$user['status'],
            loginFailure: (int)$user['login_failure'],
            loginTime: (string)$user['login_time'],
            isSuperAdmin: (int)$user['is_super'],
            code: (string)$user['code'],
            permissions: $user['permissions'],
        );
    }

    /**
     * 获取管理员拥有的菜单
     *
     * @param int $adminId
     * @return mixed
     */
    public function getAdminMenuIds(int $adminId): mixed
    {
        $admin = $this->repository->setSelectScene('detail')->findById($adminId);
        return $admin->group()
            ->pluck('rules')
            ->flatMap(fn($rules) => explode(',', $rules))
            ->unique()
            ->values()
            ->all();
    }

    /**
     * 参数格式化
     *
     * @param array $params
     * @return array
     */
    private function paramsFormat(array $params): array
    {
        // 处理权限组 ID
        $groupIds = collect(Arr::get($params, 'group_ids', []))
            ->filter(fn($id) => is_numeric($id))
            ->map(fn($id) => (int)$id)
            ->unique()
            ->values()
            ->all();
        Arr::set($params, 'group_ids', $groupIds);

        // 处理密码
        if (Arr::get($params, 'password')) {
            $salt = Str::random(6);
            $password = $this->encryptionSupport->encryptPassword($params['password'], $salt);
            $params = Arr::add($params, 'salt', $salt);
            $params = Arr::set($params, 'password', $password);
        } else {
            Arr::forget($params, 'password');
        }

        return $params;
    }
}