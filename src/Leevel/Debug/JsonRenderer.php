<?php

declare(strict_types=1);

namespace Leevel\Debug;

/**
 * Json 渲染.
 */
class JsonRenderer
{
    /**
     * debug 管理.
     */
    protected Debug $debugBar;

    /**
     * 构造函数.
     */
    public function __construct(Debug $debugBar)
    {
        $this->debugBar = $debugBar;
    }

    /**
     * 渲染数据.
     */
    public function render(): array
    {
        return $this->debugBar->getData();
    }
}
