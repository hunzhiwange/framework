<?php

declare(strict_types=1);

namespace Leevel\Server\Pool;

/**
 * 连接状态.
 */
class ConnectionStatus
{
    /**
     * 创建时间.
     */
    public int $createTime = 0;

    /**
     * 最后归还时间.
     */
    public int $pushTime = 0;

    /**
     * 最后取出时间.
     */
    public int $popTime = 0;

    public function __construct()
    {
        $this->createTime = $this->pushTime = time();
    }
}
