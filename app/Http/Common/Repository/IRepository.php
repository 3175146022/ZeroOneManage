<?php

declare(strict_types=1);

namespace App\Http\Common\Repository;

use App\Http\Common\Repository\Traits\BootTrait;
use App\Http\Common\Repository\Traits\RepositoryOrderByTrait;
use App\Http\Common\Repository\Traits\RepositorySelectTrait;
use Exception;
use Hyperf\Collection\Collection;
use Hyperf\Contract\LengthAwarePaginatorInterface;
use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Model\Model;
use Hyperf\DbConnection\Traits\HasContainer;

/**
 * 抽象模型操作基类
 *
 * @template T of Model
 * @property Model $model
 */
abstract class IRepository
{
    use BootTrait;
    use HasContainer;
    use RepositoryOrderByTrait;
    use RepositorySelectTrait;

    public const PER_PAGE_PARAM_NAME = 'per_page';

    /**
     * 查询条件处理
     *
     * @param Builder $query
     * @param array   $params
     * @return Builder
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        return $query;
    }

    /**
     * 处理结果集
     *
     * @param Collection $items
     * @return Collection
     */
    public function handleItems(Collection $items): Collection
    {
        return $items;
    }

    /**
     * 处理分页结果集
     *
     * @param LengthAwarePaginatorInterface $paginator
     * @return array
     */
    public function handlePage(LengthAwarePaginatorInterface $paginator): array
    {
        $items = $this->handleItems($paginator->getCollection());
        return [
            'list' => $items->toArray(),
            'page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
        ];
    }

    /**
     * 列表查询
     *
     * @param array $params
     * @return Collection
     */
    public function list(array $params = []): Collection
    {
        return $this->handleItems($this->perQuery($this->getQuery(), $params)->get());
    }

    /**
     * 统计
     *
     * @param array $params
     * @return int
     */
    public function count(array $params = []): int
    {
        return $this->perQuery($this->getQuery(), $params)->count();
    }

    /**
     * 分页查询
     *
     * @param array    $params
     * @param int|null $page
     * @param int|null $perPage
     * @return array
     */
    public function page(array $params = [], ?int $page = null, ?int $perPage = null): array
    {
        $result = $this->perQuery($this->getQuery(), $params)->paginate(
            perPage: $perPage,
            pageName: static::PER_PAGE_PARAM_NAME,
            page: $page,
        );
        return $this->handlePage($result);
    }

    /**
     * 创建
     *
     * @param array $data
     * @return T
     */
    public function create(array $data): \Hyperf\Database\Model\Model
    {
        return $this->getQuery()->create($data);
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
        return $this->getQuery()->whereKey($id)->firstOrFail()->update($data);
    }

    /**
     * 保存-根据 ID 保存
     *
     * @param mixed $id
     * @param array $data
     * @return T
     */
    public function saveById(mixed $id, array $data): \Hyperf\Database\Model\Model
    {
        $model = $this->getQuery()->whereKey($id)->firstOrFail();
        $model->fill($data)->save();
        return $model;
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
        return $this->model::findOrFail($id)->delete();
    }

    /**
     * 批量删除-根据 ID 删除
     *
     * @param mixed $ids
     * @return int
     */
    public function deleteByIds(mixed $ids): int
    {
        return $this->model::destroy($ids);
    }

    /**
     * 强制删除-根据 ID 删除
     *
     * @param mixed $id
     * @return bool|null
     */
    public function forceDeleteById(mixed $id): ?bool
    {
        return $this->getQuery()->whereKey($id)->firstOrFail()->forceDelete();
    }

    /**
     * 根据 ID 查询
     *
     * @param mixed $id
     * @return T
     */
    public function findById(mixed $id): \Hyperf\Database\Model\Model
    {
        return $this->getQuery()->select($this->model->{$this->getSelectScene()} ?? '*')->whereKey($id)->firstOrFail();
    }

    /**
     * 根据 ID 查询某个字段值
     *
     * @param mixed  $id
     * @param string $field
     * @return mixed
     */
    public function findByField(mixed $id, string $field): mixed
    {
        return $this->getQuery()->whereKey($id)->value($field);
    }

    /**
     * 根据过滤条件查询单条记录
     *
     * @param array $params
     * @return T
     */
    public function findByFilter(array $params): \Hyperf\Database\Model\Model
    {
        return $this->perQuery($this->getQuery(), $params)->firstOrFail();
    }

    /**
     * 根据 ID 判断记录是否存在
     *
     * @param mixed $id
     * @return bool
     */
    public function existsById(mixed $id): bool
    {
        return $this->getQuery()->whereKey($id)->exists();
    }

    /**
     * 根据过滤条件构建查询
     *
     * @param Builder $query
     * @param array   $params
     * @return Builder
     */
    public function perQuery(Builder $query, array $params): Builder
    {
        $this->startBoot($query, $params);
        return $this->handleSearch($query, $params);
    }

    /**
     * 获取查询构造器
     *
     * @return Builder
     */
    public function getQuery(): Builder
    {
        return $this->model->newQuery();
    }

    /**
     * 获取模型实例
     *
     * @return Model
     */
    public function getModel(): Model
    {
        return $this->model;
    }
}
