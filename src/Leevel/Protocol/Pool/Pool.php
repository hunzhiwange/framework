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

use InvalidArgumentException;
use function Leevel\Support\Str\camelize;
use Leevel\Support\Str\camelize;
use Swoole\Coroutine;
use Swoole\Coroutine\Channel;
use Throwable;

/**
 * 连接池抽象层.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.07.05
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
abstract class Pool implements IPool
{
    /**
     * 最小空闲连接池数据量.
     *
     * @var int
     */
    protected $mixIdleConnections = 1;

    /**
     * 最大空闲连接池数据量.
     *
     * @var int
     */
    protected $maxIdleConnections = 1;

    /**
     * 当前连接数.
     *
     * @var int
     */
    protected $connectionsCount = 0;

    /**
     * 通道写入最大超时时间设置.
     *
     * - timeout 设置超时时间，在通道已满的情况下，push 会挂起当前协程。
     *   在约定的时间内，如果没有任何消费者消费数据，将发生超时，底层会恢复当前协程，push 调用立即返回 false，写入失败
     * - 单位时间为毫秒，折算为 Swoole 的秒
     * - 默认为 -1000，需要除以 1000
     *
     * @var int
     *
     * @see https://wiki.swoole.com/wiki/page/843.html
     */
    protected $maxPushTimeout = -1000;

    /**
     * 通道获取最大等待超时.
     *
     * - 在规定时间内没有生产者 push 数据，将返回 false
     * - 单位时间为毫秒，折算为 Swoole 的秒
     * - 默认为 0，需要除以 1000
     *
     * @var int
     *
     * @see https://wiki.swoole.com/wiki/page/844.html
     */
    protected $maxPopTimeout = 0;

    /**
     * 连接的存活时间.
     *
     * - 单位为毫秒
     *
     * @var int
     */
    protected $keepAliveDuration = 60000;

    /**
     * 最大尝试次数.
     *
     * @var int
     */
    protected $retryTimes = 3;

    /**
     * 连接通道.
     *
     * @var \Swoole\Coroutine\Channel
     */
    protected $connections;

    /**
     * 是否初始化.
     *
     * - 初始化是一个可选项
     *
     * @var bool
     */
    protected $initialized = false;

    /**
     * 是否关闭.
     *
     * @var bool
     */
    protected $closed = false;

    /**
     * 构造函数.
     *
     * @param array $option
     */
    public function __construct(array $option = [])
    {
        $key = [
            'max_idle_connections', 'mix_idle_connections',
            'max_push_timeout', 'max_pop_timeout',
            'keep_alive_duration', 'retry_times',
        ];

        foreach ($key as $k) {
            if (isset($option[$k])) {
                $method = 'set'.ucfirst(camelize($k));
                $this->{$method}($option[$k]);
            }
        }

        $this->connections = new Channel($this->maxIdleConnections);
    }

    /**
     * 初始化连接池.
     *
     * @return bool
     */
    public function init(): bool
    {
        if ($this->initialized) {
            return false;
        }

        $this->initialized = true;

        Coroutine::create(function () {
            for ($i = 0; $i < $this->mixIdleConnections; $i++) {
                $connection = $this->createConnection();
                $this->backConnection($connection);
            }
        });

        return true;
    }

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
    public function borrowConnection(int $timeout = 3000): IConnection
    {
        // 未达到最小连接数，直接新建使用后归还
        if ($this->connectionsCount < $this->mixIdleConnections) {
            return $this->createConnection();
        }

        // 从通道中获取连接,支持重试次数
        $retryTimes = $this->retryTimes;

        while ($retryTimes) {
            $retryTimes--;

            if ($connection = $this->getConnectionFromChannel($timeout)) {
                $this->updateConnectionLastActiveTime($connection);

                return $connection;
            }
        }

        // 未达到最大连接数，直接新建使用后归还
        if ($this->connectionsCount < $this->maxIdleConnections) {
            return $this->createConnection();
        }

        // 通道为空，尝试等待其他协程调用 push 方法生产数据
        $connection = $this->connections->pop($this->maxPopTimeout / 1000);

        if (false === $connection) {
            $e = sprintf('Timeout `%f` ms of reading data from channels.', $this->maxPopTimeout);

            throw new PoolExceptin($e);
        }

        $this->updateConnectionLastActiveTime($connection);

        return $connection;
    }

    /**
     * 归还连接.
     *
     * @param \Leevel\Protocol\Pool\IConnection $connection
     *
     * @return bool
     */
    public function returnConnection(IConnection $connection): bool
    {
        $this->connectionsCount++;

        if ($this->connections->isFull()) {
            $this->disconnect($connection);

            return false;
        }

        $this->updateConnectionLastActiveTime($connection);
        $result = $this->connections->push($connection, $this->maxPushTimeout / 1000);

        if (false === $result) {
            $this->disconnect($connection);
        }

        return $result;
    }

    /**
     * 关闭通道.
     *
     * @return bool
     */
    public function close(): bool
    {
        if ($this->closed) {
            return false;
        }

        $this->closed = true;

        Coroutine::create(function () {
            while (true) {
                if ($this->connections->isEmpty()) {
                    break;
                }

                $connection = $this->connections->pop($this->maxPopTimeout / 1000);

                if (false !== $connection) {
                    $this->disconnect($connection);
                }
            }

            $this->connections->close();
            $this->connections = null;
        });

        return true;
    }

    /**
     * 获取连接数.
     *
     * @return int
     */
    public function getConnectionsCount(): int
    {
        return $this->connectionsCount;
    }

    /**
     * 设置最小空闲连接池数据量.
     *
     * @param int $mixIdleConnections
     *
     * @return \Leevel\Protocol\Pool\IPool
     */
    public function setMixIdleConnections(int $mixIdleConnections): IPool
    {
        $this->mixIdleConnections = $mixIdleConnections;
        $this->validateIdleConnections();

        return $this;
    }

    /**
     * 设置最小空闲连接池数据量.
     *
     * @param int $maxIdleConnections
     *
     * @return \Leevel\Protocol\Pool\IPool
     */
    public function setMaxIdleConnections(int $maxIdleConnections): IPool
    {
        $this->maxIdleConnections = $maxIdleConnections;
        $this->validateIdleConnections();

        return $this;
    }

    /**
     * 设置通道写入最大超时时间设置.
     *
     * @param int $maxPushTimeout
     *
     * @return \Leevel\Protocol\Pool\IPool
     */
    public function setMaxPushTimeout(int $maxPushTimeout): IPool
    {
        $this->maxPushTimeout = $maxPushTimeout;

        return $this;
    }

    /**
     * 设置通道获取最大等待超时.
     *
     * @param int $maxPopTimeout
     *
     * @return \Leevel\Protocol\Pool\IPool
     */
    public function setMaxPopTimeout(int $maxPopTimeout): IPool
    {
        $this->maxPopTimeout = $maxPopTimeout;

        return $this;
    }

    /**
     * 设置最大尝试次数.
     *
     * @param int $maxPopTimeout
     *
     * @throws \InvalidArgumentException
     *
     * @return \Leevel\Protocol\Pool\IPool
     */
    public function setRetryTimes(int $retryTimes): IPool
    {
        $this->retryTimes = $retryTimes;

        if ($this->retryTimes < 1) {
            $e = 'Retry times must greater than or equal to 1.';

            throw new InvalidArgumentException($e);
        }

        return $this;
    }

    /**
     * 创建连接.
     *
     * @param float $timeout
     *
     * @return \Leevel\Protocol\Pool\IConnection
     */
    abstract protected function createConnection(): IConnection;

    /**
     *  校验空闲连接池数据量.
     *
     * @throws \InvalidArgumentException
     */
    protected function validateIdleConnections(): void
    {
        if ($this->mixIdleConnections > $this->maxIdleConnections) {
            $e = sprintf('Max option `%d` of connections must greater than or equal to min `%d`.',
                $this->maxIdleConnections, $this->minIdleConnections);

            throw new InvalidArgumentException($e);
        }
    }

    /**
     * 从通道中读取连接.
     *
     * - 通道为空返回 null
     * - 全部超时或者过期将返回 null
     *
     * @param int $timeout
     *
     * @return null|\Leevel\Protocol\Pool\IConnection
     *
     * @see https://wiki.swoole.com/wiki/page/844.html
     */
    protected function getConnectionFromChannel(int $timeout): ?IConnection
    {
        $time = $this->normalizeMillisecond();

        while (!$this->connections->isEmpty()) {
            // 在规定时间内没有生产者 push 数据，将返回 false
            $connection = $this->connections->pop($timeout / 1000);

            if (false === $connection) {
                continue;
            }

            // 连接过期释放掉
            $lastActiveTime = $connection->{self::LAST_ACTIVE_TIME} ?? 0;

            if ($time - $lastActiveTime > $this->keepAliveDuration) {
                $this->disconnect($connection);

                continue;
            }

            $this->connectionsCount--;

            return $connection;
        }

        return null;
    }

    /**
     * 断开连接.
     *
     * @param \Leevel\Protocol\Pool\IConnection $connection
     */
    protected function disconnect(IConnection $connection): void
    {
        $this->connectionsCount--;

        Coroutine::create(function () use ($connection) {
            try {
                $connection->disconnect();
            } catch (Throwable $th) {
            }
        });
    }

    /**
     * 更新连接最后更新时间.
     *
     * @param \Leevel\Protocol\Pool\IConnection $connection
     */
    protected function updateConnectionLastActiveTime(IConnection $connection): void
    {
        $connection->{self::LAST_ACTIVE_TIME} = $this->normalizeMillisecond();
    }

    /**
     * 获取当前时间毫秒.
     *
     * @return float
     */
    protected function normalizeMillisecond(): float
    {
        list($msec, $sec) = explode(' ', microtime());
        $msectime = (float) sprintf('%.0f', ((float) $msec + (float) $sec) * 1000);

        return $msectime;
    }
}

// import fn.
class_exists(camelize::class);
