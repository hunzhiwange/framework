<?php

declare(strict_types=1);

namespace Leevel\Debug;

/**
 * Json 渲染.
 */
class JsonRenderer
{
    /**
     * 构造函数.
     */
    public function __construct(protected Debug $debug) {}

    /**
     * 渲染数据.
     */
    public function render(): array
    {
        return $this->debug->getDebugBar()->getData();
    }
}
