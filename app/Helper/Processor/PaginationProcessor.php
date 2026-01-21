<?php
declare(strict_types=1);

namespace App\Helper\Processor;

use Hyperf\Collection\Collection;

/**
 * 分页处理器
 *
 * @package App\Helper\Processor
 */
final class PaginationProcessor
{
    // 数据集合
    private Collection $items;
    // 当前页码
    private int $page = 1;
    // 每页记录数
    private int $perPage = 10;
    // 总记录数
    private int $total = 0;
    // 是否需要在内存中做分页
    private bool $needSlice = false;

    /**
     * 内存分页(array | collection 全量数据)
     *
     * @param array|Collection $items
     * @return self
     */
    public static function fromMemory(array|Collection $items): self
    {
        $self = new self();
        $self->items = $items instanceof Collection ? $items : Collection::make($items);
        $self->total = $self->items->count();
        $self->needSlice = true;

        return $self;
    }

    /**
     * 已分页好的数据(ES | RPC | DB 等)
     *
     * @param array|Collection $items   数据集合
     * @param int              $page    当前页码
     * @param int              $perPage 每页记录数
     * @param int              $total   总记录数
     * @return self
     */
    public static function fromReady(array|Collection $items, int $page, int $perPage, int $total): self
    {
        $self = new self();
        $self->items = $items instanceof Collection ? $items : Collection::make($items);
        $self->page = $page;
        $self->perPage = $perPage;
        $self->total = $total;
        $self->needSlice = false;

        return $self;
    }

    /**
     * 设置当前页码
     *
     * @param int $page
     * @return $this
     */
    public function page(int $page): self
    {
        $this->page = max(1, $page);
        return $this;
    }

    /**
     * 设置每页记录数
     *
     * @param int $perPage
     * @return $this
     */
    public function perPage(int $perPage): self
    {
        $this->perPage = max(1, $perPage);
        return $this;
    }

    /**
     * 转换为数组
     *
     * @return array
     */
    public function toArray(): array
    {
        $items = $this->needSlice
            ? $this->items->slice(($this->page - 1) * $this->perPage, $this->perPage)->values()
            : $this->items->values();

        return [
            'list' => $items->toArray(),
            'page' => $this->page,
            'per_page' => $this->perPage,
            'total' => $this->total,
        ];
    }
}