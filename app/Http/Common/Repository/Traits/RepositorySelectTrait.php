<?php

declare(strict_types=1);

namespace App\Http\Common\Repository\Traits;

use Hyperf\Database\Model\Builder;

/**
 * 查询字段处理
 */
trait RepositorySelectTrait
{
    protected string $selectScene = 'default';

    public function handleSelect(Builder $query): Builder
    {
        if (!$this->enableSelect()) {
            return $query;
        }

        $scene = $this->selectScene ?? 'default';
        $property = $scene . 'Filters';

        if (property_exists($this->model, $property) && is_array($this->model->{$property})) {
            $query->select($this->model->{$property});
        }

        return $query;
    }

    protected function bootRepositorySelectTrait(Builder $query): void
    {
        $this->handleSelect($query);
    }

    public function setSelectScene(string $scene): static
    {
        $this->selectScene = $scene;
        return $this;
    }

    public function getSelectScene(): string
    {
        return $this->selectScene . 'Filters';
    }

    protected function enableSelect(): bool
    {
        return true;
    }
}
