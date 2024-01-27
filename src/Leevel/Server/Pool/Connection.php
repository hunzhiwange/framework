<?php

declare(strict_types=1);

namespace Leevel\Server\Pool;

/**
 * 连接池连接.
 */
trait Connection
{
    /**
     * 连接池.
     */
    protected ?Pool $pool = null;

    /**
     * 归还连接池.
     */
    public function release(): void
    {
        $this->pool->put($this);
    }

    /**
     * 设置关联连接池.
     */
    public function setPool(Pool $pool): void
    {
        $this->pool = $pool;
    }
}
