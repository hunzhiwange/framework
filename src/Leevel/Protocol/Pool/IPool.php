<?php

declare(strict_types=1);

/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Protocol\Pool;

/**
 * 连接池接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.07.05
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
interface IPool
{
    /**
     * 连接最后活跃时间.
     *
     * @var string
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
     * @return \Leevel\Protocol\Pool\IConnection
     *
     * @see https://wiki.swoole.com/wiki/page/846.html
     */
    public function borrowConnection(int $timeout = 3000): IConnection;

    /**
     * 归还连接.
     *
     * @param \Leevel\Protocol\Pool\IConnection $connection
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
     *
     * @return \Leevel\Protocol\Pool\IPool
     */
    public function setMinIdleConnections(int $minIdleConnections): self;

    /**
     * 设置最小空闲连接池数据量.
     *
     * @return \Leevel\Protocol\Pool\IPool
     */
    public function setMaxIdleConnections(int $maxIdleConnections): self;

    /**
     * 设置通道写入最大超时时间设置.
     *
     * @return \Leevel\Protocol\Pool\IPool
     */
    public function setMaxPushTimeout(int $maxPushTimeout): self;

    /**
     * 设置通道获取最大等待超时.
     *
     * @return \Leevel\Protocol\Pool\IPool
     */
    public function setMaxPopTimeout(int $maxPopTimeout): self;

    /**
     * 设置连接的存活时间.
     *
     * @return \Leevel\Protocol\Pool\IPool
     */
    public function setKeepAliveDuration(int $keepAliveDuration): self;

    /**
     * 设置最大尝试次数.
     *
     * @throws \InvalidArgumentException
     *
     * @return \Leevel\Protocol\Pool\IPool
     */
    public function setRetryTimes(int $retryTimes): self;
}
