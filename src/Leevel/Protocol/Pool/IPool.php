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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
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
     *
     * @return bool
     */
    public function init(): bool;

    /**
     * 获取连接.
     *
     * @param int $timeout
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
     *
     * @return bool
     */
    public function returnConnection(IConnection $connection): bool;

    /**
     * 关闭通道.
     *
     * @return bool
     */
    public function close(): bool;

    /**
     * 获取连接数.
     *
     * @return int
     */
    public function getConnectionsCount(): int;

    /**
     * 设置最小空闲连接池数据量.
     *
     * @param int $mixIdleConnections
     *
     * @return \Leevel\Protocol\Pool\IPool
     */
    public function setMixIdleConnections(int $mixIdleConnections): self;

    /**
     * 设置最小空闲连接池数据量.
     *
     * @param int $maxIdleConnections
     *
     * @return \Leevel\Protocol\Pool\IPool
     */
    public function setMaxIdleConnections(int $maxIdleConnections): self;

    /**
     * 设置通道写入最大超时时间设置.
     *
     * @param int $maxPushTimeout
     *
     * @return \Leevel\Protocol\Pool\IPool
     */
    public function setMaxPushTimeout(int $maxPushTimeout): self;

    /**
     * 设置通道获取最大等待超时.
     *
     * @param int $maxPopTimeout
     *
     * @return \Leevel\Protocol\Pool\IPool
     */
    public function setMaxPopTimeout(int $maxPopTimeout): self;

    /**
     * 设置最大尝试次数.
     *
     * @param int $maxPopTimeout
     *
     * @throws \InvalidArgumentException
     *
     * @return \Leevel\Protocol\Pool\IPool
     */
    public function setRetryTimes(int $retryTimes): self;
}
