<?php

declare(strict_types=1);

namespace Leevel\Database\Mysql;

use Leevel\Database\Manager;
use Leevel\Database\MysqlPoolConnection;
use Leevel\Database\PoolManager;
use Leevel\Protocol\Pool\IPool;
use Leevel\Protocol\Pool\Pool;

/**
 * MySQL 连接池.
 *
 * @codeCoverageIgnore
 */
class MysqlPool extends Pool implements IPool
{
    /**
     * 数据库连接池管理.
     */
    protected PoolManager $poolManager;

    /**
     * 数据库管理.
     */
    protected Manager $manager;

    /**
     * MySQL 连接.
    */
    protected string $mysqlConnect;

    /**
     * 构造函数.
     */
    public function __construct(Manager $manager, string $mysqlConnect, array $option = [])
    {
        $this->manager = $manager;
        $this->poolManager = $manager->createPoolManager();
        $this->mysqlConnect = $mysqlConnect;
        parent::__construct($option);
    }

    /**
     * {@inheritDoc}
     */
    protected function createConnection(): MysqlPoolConnection
    {
        if ($this->poolManager->inTransactionConnection()) {
            return $this->poolManager->getTransactionConnection();
        }

        $this->manager->extend('mysqlPoolConnection', function (Manager $manager): MysqlPoolConnection {
            return $manager->createMysqlPoolConnection($this->mysqlConnect); 
        });

        /** @var \Leevel\Database\MysqlPoolConnection $mysql */
        $mysqlPoolConnection = $this->manager->connect('mysqlPoolConnection', true);
        $mysqlPoolConnection->setShouldRelease(true);

        return $mysqlPoolConnection;
    }
}
