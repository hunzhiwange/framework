<?php

declare(strict_types=1);

namespace Leevel\Protocol\Pool;

/**
 * 连接池接口.
 */
interface IPool
{
    /**
     * 连接最后活跃时间.
    */
    const LAST_ACTIVE_TIME = 'LAST_ACTIVE_TIME';

    /**
     * 初始化连接池.
     */
    public function init(): bool;

    /**
     * 获取连接.
     *
     * @throws \Leevel\Protocol\Pool\PoolException
     *
     * @see https://wiki.swoole.com/wiki/page/846.html
     */
    public function borrowConnection(int $timeout = 3000): IConnection;

    /**
     * 归还连接.
     */
    public function returnConnection(IConnection $connection): bool;

    /**
     * 关闭通道.
     */
    public function close(): bool;

    /**
     * 获取连接数.
     */
    public function getConnectionsCount(): int;

    /**
     * 设置最小空闲连接池数据量.
     */
    public function setMinIdleConnections(int $minIdleConnections): self;

    /**
     * 设置最小空闲连接池数据量.
     */
    public function setMaxIdleConnections(int $maxIdleConnections): self;

    /**
     * 设置通道写入最大超时时间设置.
     */
    public function setMaxPushTimeout(int $maxPushTimeout): self;

    /**
     * 设置通道获取最大等待超时.
     */
    public function setMaxPopTimeout(int $maxPopTimeout): self;

    /**
     * 设置连接的存活时间.
     */
    public function setKeepAliveDuration(int $keepAliveDuration): self;

    /**
     * 设置最大尝试次数.
     *
     * @throws \InvalidArgumentException
     */
    public function setRetryTimes(int $retryTimes): self;
}
