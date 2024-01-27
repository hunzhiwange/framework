<?php

declare(strict_types=1);

namespace Leevel\Server\Pool;

use Leevel\Support\Str\Camelize;
use Swoole\Coroutine\Channel;
use Throwable;

/**
 * 连接池.
 */
abstract class Pool
{
    /**
     * 连接通道.
     */
    protected ?Channel $pool = null;

    /**
     * 最大空闲连接池数据量.
     */
    protected int $maxIdleConnections = 60;

    /**
     * 通道写入最大超时时间设置.
     *
     * - timeout 设置超时时间，在通道已满的情况下，push 会挂起当前协程。
     *   在约定的时间内，如果没有任何消费者消费数据，将发生超时，底层会恢复当前协程，push 调用立即返回 false，写入失败
     * - 默认为 -1
     *
     * @see https://wiki.swoole.com/#/coroutine/channel
     */
    protected float $maxPushTimeout = -1;

    /**
     * 通道获取最大等待超时.
     *
     * - 在规定时间内没有生产者 push 数据，将返回 false
     * - 默认为 -1
     *
     * @see https://wiki.swoole.com/#/coroutine/channel
     */
    protected float $maxPopTimeout = -1;

    /**
     * 连接的存活时间.
     */
    protected float $keepAliveDuration = 60;

    /**
     * 是否关闭.
     */
    protected bool $closed = false;

    /**
     * 连接数量.
     */
    protected int $connectionNumber = 0;

    /**
     * 连接状态.
     */
    protected array $connectionStatus = [];

    /**
     * 构造函数.
     */
    public function __construct(array $config = [])
    {
        $props = [
            'max_idle_connections',
            'max_push_timeout',
            'max_pop_timeout',
            'keep_alive_duration',
        ];
        foreach ($props as $k) {
            if (isset($config[$k])) {
                $prop = Camelize::handle($k);
                $this->{$prop} = $config[$k];
            }
        }

        $this->validateIdleConnections();

        $this->pool = new Channel($this->maxIdleConnections);
    }

    /**
     * 初始化连接池.
     */
    public function fill(): void
    {
        while ($this->maxIdleConnections > $this->connectionNumber) {
            $this->make();
        }
    }

    /**
     * 获取连接.
     *
     * @throws \Leevel\Server\Pool\PoolException
     */
    public function get(?float $timeout = null): IConnection
    {
        if (null === $this->pool) {
            throw new \RuntimeException('Pool has been closed');
        }

        if ($this->pool->isEmpty() && $this->connectionNumber < $this->maxIdleConnections) {
            $this->make();
        }

        if (null === $timeout) {
            $timeout = $this->maxPopTimeout;
        }

        return $this->getPoolConnection($timeout);
    }

    /**
     * 归还连接.
     */
    public function put(?IConnection $connection = null, ?float $timeout = null): void
    {
        if (null === $this->pool) {
            return;
        }

        if (null !== $connection) {
            if (!$this->isHealthy($connection)) {
                $this->disconnect($connection);
                --$this->connectionNumber;
                $this->make();

                return;
            }

            if (null === $timeout) {
                $timeout = $this->maxPushTimeout;
            }

            if (!$this->pool->push($connection, $timeout)) {
                $this->disconnect($connection);
                --$this->connectionNumber;
                $this->make();

                return;
            }

            if ($connectionStatus = $this->getConnectionStatus($connection)) {
                $connectionStatus->pushTime = time();
            }
        } else {
            --$this->connectionNumber;
            $this->make();
        }
    }

    /**
     * 关闭通道.
     */
    public function close(): void
    {
        while (true) {
            if ($this->pool->isEmpty()) {
                break;
            }

            $connection = $this->pool->pop(0.005);
            if (false !== $connection) {
                $this->disconnect($connection);
            }
        }

        $this->pool->close();
        $this->pool = null;
        $this->connectionNumber = 0;
    }

    /**
     * 从通道中读取连接.
     *
     * @throws \Leevel\Server\Pool\PoolException
     */
    protected function getPoolConnection(float $timeout): IConnection
    {
        // 在规定时间内没有生产者 push 数据，将返回 false
        $connection = $this->pool->pop($timeout);
        if (!$connection) {
            switch ($this->pool->errCode) {
                case SWOOLE_CHANNEL_TIMEOUT:
                    $this->waitTimeoutNum++;
                    $errMsg = 'Connection acquisition timeout';

                    break;

                case SWOOLE_CHANNEL_CLOSED:
                    $errMsg = 'Connection pool has been closed';

                    break;

                default:
                    $errMsg = 'Connection acquisition failed';

                    break;
            }

            throw new PoolException($errMsg);
        }

        $connectionStatus = $this->getConnectionStatus($connection);
        $connectionStatus->popTime = time();

        // @phpstan-ignore-next-line
        return $connection;
    }

    /**
     * 获取连接状态.
     */
    protected function getConnectionStatus(IConnection $connection): ?ConnectionStatus
    {
        return $this->connectionStatus[$this->getConnectionId($connection)] ?? null;
    }

    protected function make(): void
    {
        ++$this->connectionNumber;

        try {
            $connection = $this->createConnection();
        } catch (\Throwable $throwable) {
            --$this->connectionNumber;

            throw $throwable;
        }

        $connection->setPool($this);
        $this->connectionStatus[$this->getConnectionId($connection)] = new ConnectionStatus();

        $this->put($connection);
    }

    abstract protected function createConnection(): IConnection;

    /**
     * 获取对象ID.
     */
    protected function getConnectionId(IConnection $connection): int
    {
        return spl_object_id($connection);
    }

    /**
     * 检查连接对象的健康情况.
     */
    protected function isHealthy(IConnection $connection): bool
    {
        if (!($connectionStatus = $this->getConnectionStatus($connection))) {
            return false;
        }

        if (time() - $connectionStatus->pushTime >= $this->keepAliveDuration) {
            return false;
        }

        return true;
    }

    /**
     *  校验空闲连接池数据量.
     *
     * @throws \InvalidArgumentException
     */
    protected function validateIdleConnections(): void
    {
        if ($this->maxIdleConnections < 1) {
            throw new \InvalidArgumentException(sprintf(
                '`max_idle_connections` `%d` of connections must greater than or equal to min `%d`.',
                $this->maxIdleConnections,
                1
            ));
        }
    }

    /**
     * 断开连接.
     */
    protected function disconnect(IConnection $connection): void
    {
        try {
            unset($this->connectionStatus[$this->getConnectionId($connection)]);
            $connection->close();
        } catch (Throwable) {
        }
    }
}
