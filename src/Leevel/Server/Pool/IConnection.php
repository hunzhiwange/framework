<?php

declare(strict_types=1);

namespace Leevel\Server\Pool;

/**
 * 连接池连接接口.
 */
interface IConnection
{
    /**
     * 归还连接池.
     */
    public function release(): void;

    /**
     * 设置关联连接池.
     */
    public function setPool(Pool $pool): void;

    // 关闭连接.
    // public function close(): void;
}
