<?php

declare(strict_types=1);

namespace Leevel\Database;

use Leevel\Server\Pool\IConnection;
use Leevel\Server\Pool\Pool as BasePool;

/**
 * 数据库连接池.
 */
class Pool extends BasePool
{
    /**
     * 数据库管理.
     */
    protected Manager $manager;

    /**
     * 数据库连接.
     */
    protected string $connect;

    /**
     * 构造函数.
     */
    public function __construct(Manager $manager, string $connect, array $option = [])
    {
        $this->manager = $manager;
        $this->connect = $connect;
        parent::__construct($option);
    }

    /**
     * {@inheritDoc}
     */
    public function get(?float $timeout = null): IDatabase
    {
        // @phpstan-ignore-next-line
        return parent::get($timeout);
    }

    protected function createConnection(): IConnection
    {
        // @phpstan-ignore-next-line
        return $this->manager->connect($this->connect, true, true);
    }
}
