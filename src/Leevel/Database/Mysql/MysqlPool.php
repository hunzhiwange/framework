<?php

declare(strict_types=1);

namespace Leevel\Database\Mysql;

use Leevel\Database\Manager;
use Leevel\Protocol\Pool\IConnection;
use Leevel\Protocol\Pool\Pool;

/**
 * MySQL 连接池.
 *
 * @codeCoverageIgnore
 */
class MysqlPool extends Pool
{
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
        parent::__construct($option);

        $this->manager = $manager;
        $this->mysqlConnect = $mysqlConnect;
    }

    /**
     * {@inheritDoc}
     */
    protected function createConnection(): IConnection
    {
        if ($this->manager->inTransactionConnection()) {
            return $this->manager->getTransactionConnection();
        }

        /** @var \Leevel\Protocol\Pool\IConnection $mysql */
        $mysql = $this->manager->connect($this->mysqlConnect, true);
        $mysql->setRelease(true);

        return $mysql;
    }
}
