<?php

declare(strict_types=1);

namespace Leevel\Database;

use Leevel\Di\IContainer;

/**
 * 数据库连接池事务管理器.
 */
class PoolTransaction
{
    /**
     * 当前协程事务服务标识.
     */
    public const TRANSACTION_SERVICE = 'transaction.service';

    /**
     * 构造函数.
     */
    public function __construct(protected IContainer $container, protected string $connect)
    {
        $this->container->addContextKeys($this->getKey());
    }

    /**
     * 设置当前协程事务中的连接.
     */
    public function set(IDatabase $database): void
    {
        $this->container->instance($this->getKey(), $database);
    }

    /**
     * 是否处于当前协程事务中.
     */
    public function in(): bool
    {
        return $this->container->exists($this->getKey());
    }

    /**
     * 获取当前协程事务中的连接.
     *
     * @throws \RuntimeException
     */
    public function get(): ?IDatabase
    {
        $database = $this->container->make($this->getKey(), throw: false);
        if (!$database instanceof IDatabase) {
            throw new \RuntimeException('There was no active transaction.');
        }

        return $database;
    }

    /**
     * 删除当前协程事务中的连接.
     */
    public function remove(): void
    {
        $this->container->remove($this->getKey());
    }

    /**
     * 获取事务存储键值.
     */
    protected function getKey(): string
    {
        return $this->connect.':'.self::TRANSACTION_SERVICE;
    }
}
