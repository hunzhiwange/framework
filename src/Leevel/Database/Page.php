<?php

declare(strict_types=1);

namespace Leevel\Database;

use Leevel\Page\Page as BasePage;

/**
 * 数据库分页查询.
 */
class Page extends BasePage
{
    /**
     * 查询数据.
     */
    protected mixed $data = null;

    /**
     * 设置数据.
     */
    public function setData(mixed $data): void
    {
        $this->data = $data;
    }

    /**
     * 获取数据.
     */
    public function getData(): mixed
    {
        return $this->data;
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return [
            'page' => parent::toArray(),
            'data' => $this->data,
        ];
    }
}
