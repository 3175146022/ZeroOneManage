<?php

namespace App\Helper\Support;

use Hyperf\Collection\Collection;
use Hyperf\Collection\Enumerable;
use function Hyperf\Collection\collect;

/**
 * 资源数据集合处理
 */
final class CollectSupport
{
    /**
     * 获取树形结构(下拉选择统一输出格式)
     *
     * @param Collection $data
     * @return Enumerable
     */
    public function optionCollect(Collection $data): Enumerable
    {
        return $data->map(fn($item) => $this->formatNode($item))->values();
    }

    /**
     * 递归格式化数据
     *
     * @param $item
     * @return array
     */
    protected function formatNode($item): array
    {
        $children = collect($item['children'] ?? [])
            ->map(fn($child) => $this->formatNode($child))
            ->values();

        return array_filter([
            'label' => $item['name'],
            'value' => $item['id'],
            'children' => $children->isNotEmpty() ? $children : null,
        ]);
    }
}