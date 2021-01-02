<?php

declare(strict_types=1);

namespace Leevel\Database;

use Leevel\Di\IContainer;
use Leevel\Protocol\Pool\IConnection;
use RuntimeException;

/**
 * 数据库连接池管理器.
 */
class PoolManager
{
    /**
     * 当前协程事务服务标识.
     */
    public const TRANSACTION_SERVICE = 'transaction.service';

    /**
     * 构造函数.
     */
    public function __construct(protected IContainer $container)
    {
    }

    /**
     * 设置当前协程事务中的连接.
     */
    public function setTransactionConnection(IConnection $connection): void
    {
        $this->container->instance(self::TRANSACTION_SERVICE, $connection, IContainer::DEFAULT_COROUTINE_ID);
    }

    /**
     * 是否处于当前协程事务中.
     */
    public function inTransactionConnection(): bool
    {
        return $this->container->exists(self::TRANSACTION_SERVICE);
    }

    /**
     * 获取当前协程事务中的连接.
     *
     * @throws \RuntimeException
     */
    public function getTransactionConnection(): IConnection
    {
        $connection = $this->container->make(self::TRANSACTION_SERVICE);
        if (!is_object($connection) || !$connection instanceof IConnection) {
            $e = 'There was no active transaction.';

            throw new RuntimeException($e);
        }

        return $connection;
    }

    /**
     * 删除当前协程事务中的连接.
     */
    public function removeTransactionConnection(): void
    {
        $this->container->remove(self::TRANSACTION_SERVICE);
    }
}
