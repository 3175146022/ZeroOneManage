<?php

declare(strict_types=1);

namespace App\Http\Common\Repository\Traits;

use Hyperf\Database\Model\Builder;

/**
 * 排序特征
 */
trait RepositoryOrderByTrait
{
    protected function bootRepositoryOrderByTrait(Builder $query, array $params): void
    {
        $this->handleOrderBy($query, $params);
    }

    public function handleOrderBy(Builder $query, $params): Builder
    {
        if (!$this->enablePageOrderBy()) {
            return $query;
        }

        $orders = $this->parseOrderBy($query, $params);

        foreach ($orders as [$field, $direction]) {
            $query->orderBy($field, $direction);
        }

        return $query;
    }

    /**
     * 解析排序参数
     *
     * @param Builder $query
     * @param array   $params 请求参数
     * @return array
     */
    protected function parseOrderBy(Builder $query, array $params): array
    {
        $result = [];

        $orderByParam = $params[$this->getOrderByParamName()] ?? null;
        $defaultDirection = strtolower($params[$this->getOrderByDirectionParamName()] ?? 'desc');

        if ($orderByParam) {
            $items = explode(',', $orderByParam);

            foreach ($items as $item) {
                $item = trim($item);

                if ($item === '') {
                    continue;
                }

                // 支持三种写法：id asc | id:asc | id
                if (str_contains($item, ':')) {
                    [$field, $direction] = explode(':', $item, 2);
                } else if (preg_match('/\s+/', $item)) {
                    [$field, $direction] = preg_split('/\s+/', $item, 2);
                } else {
                    $field = $item;
                    $direction = $defaultDirection;
                }

                $field = trim($field);
                $direction = strtolower(trim($direction));

                if ($this->isValidOrderBy($query, $field, $direction)) {
                    $result[] = [$field, $direction];
                }
            }
        }

        // 默认排序
        if (empty($result)) {
            $result[] = [$query->getModel()->getKeyName(), $defaultDirection];
        }

        return $result;
    }

    /**
     * 判断字段是否有效
     *
     * @param Builder $query
     * @param string  $field     字段
     * @param string  $direction 排序方向
     * @return bool
     */
    protected function isValidOrderBy(Builder $query, string $field, string $direction): bool
    {
        if (!in_array($direction, ['asc', 'desc'], true)) {
            return false;
        }

        // 防止 SQL 注入，仅允许表字段
        return in_array($field, $query->getModel()->getFillable() ?: [$query->getModel()->getKeyName()], true);
    }

    protected function getOrderByParamName(): string
    {
        return 'order_by';
    }

    protected function getOrderByDirectionParamName(): string
    {
        return 'order_by_direction';
    }

    protected function enablePageOrderBy(): bool
    {
        return true;
    }
}
