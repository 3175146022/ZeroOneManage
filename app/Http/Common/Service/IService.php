<?php

declare(strict_types=1);

namespace App\Http\Common\Service;

use App\Http\Common\Repository\IRepository;
use Exception;
use Hyperf\Collection\Collection;
use Hyperf\DbConnection\Model\Model;
use Hyperf\DbConnection\Traits\HasContainer;

/**
 * 抽象服务类
 *
 * @template T of Model
 * @property IRepository<T> $repository
 */
abstract class IService
{
    use HasContainer;

    /**
     * 统计
     *
     * @param array $params
     * @return int
     */
    public function count(array $params): int
    {
        return $this->repository->count($params);
    }

    /**
     * 分页
     *
     * @param array $params
     * @param int   $page
     * @param int   $perPage
     * @return array
     */
    public function page(array $params, int $page = 1, int $perPage = 10): array
    {
        return $this->repository->page($params, $page, $perPage);
    }

    /**
     * 列表
     *
     * @param array $params
     * @return Collection
     */
    public function getList(array $params): Collection
    {
        return $this->repository->list($params);
    }

    /**
     * 创建
     *
     * @param array $data
     * @return T
     */
    public function create(array $data): \Hyperf\Database\Model\Model
    {
        return $this->repository->create($data);
    }

    /**
     * 更新-根据 ID 更新
     *
     * @param mixed $id
     * @param array $data
     * @return bool
     */
    public function updateById(mixed $id, array $data): bool
    {
        return $this->repository->updateById($id, $data);
    }

    /**
     * 删除-根据 ID 删除
     *
     * @param mixed $id
     * @return bool|null
     * @throws Exception
     */
    public function deleteById(mixed $id): ?bool
    {
        return $this->repository->deleteById($id);
    }

    /**
     * 根据 ID 查询
     *
     * @param mixed  $id
     * @param string $filterFieldsScene 过滤字段场景
     * @return T
     */
    public function findById(mixed $id, string $filterFieldsScene = 'detail'): \Hyperf\Database\Model\Model
    {
        return $this->repository->setSelectScene($filterFieldsScene)->findById($id);
    }

    /**
     * 是否存在-根据 ID 查询
     *
     * @param mixed $id
     * @return bool
     */
    public function existsById(mixed $id): bool
    {
        return $this->repository->existsById($id);
    }
}
