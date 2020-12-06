<?php

declare(strict_types=1);

namespace Leevel\Protocol\Pool;

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
    protected bool $release = false;

    /**
     * 归还连接池.
     */
    public function release(): void
    {
        if ($this->release) {
            $this->release = false;
            $this->pool->returnConnection($this);
        }
    }

    /**
     * 设置是否归还连接池.
     */
    public function setRelease(bool $release): void
    {
        $this->release = $release;
    }

    /**
     * 设置关联连接池.
     */
    public function setPool(IPool $pool): void
    {
        $this->pool = $pool;
    }
}
