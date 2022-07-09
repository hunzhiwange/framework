<?php

declare(strict_types=1);

namespace Leevel\Level\Pool;

/**
 * 连接池连接驱动.
 */
trait Connection
{
    /**
     * 连接池.
     */
    protected IPool $pool;

    /**
     * 是否归还连接池.
     */
    protected bool $shouldRelease = false;

    /**
     * 归还连接池.
     */
    public function release(): void
    {
        if ($this->shouldRelease) {
            $this->pool->returnConnection($this);
        }
    }

    /**
     * 设置是否归还连接池.
     */
    public function setShouldRelease(bool $shouldRelease): void
    {
        $this->shouldRelease = $shouldRelease;
    }

    /**
     * 设置关联连接池.
     */
    public function setPool(IPool $pool): void
    {
        $this->pool = $pool;
    }
}
