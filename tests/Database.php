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

namespace Tests;

use Leevel\Database\Ddd\Meta;
use Leevel\Database\Manager;
use Leevel\Database\Mysql;
use Leevel\Database\Mysql\MysqlPool as MysqlPools;
use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Event\IDispatch;
use Leevel\Option\Option;
use Leevel\Protocol\Coroutine;
use Leevel\Protocol\Pool\IConnection;
use PDO;
use PDOException;

/**
 * 数据辅助方法.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.10
 *
 * @version 1.0
 */
trait Database
{
    protected $databaseConnects = [];

    protected function createDatabaseConnectMock(array $option = [], ?string $connect = null, ?IDispatch $dispatch = null): Mysql
    {
        if (null === $connect) {
            $connect = Mysql::class;
        }

        $connect = new $connect($option, $dispatch);
        $this->databaseConnects[] = $connect;

        return $connect;
    }

    protected function createDatabaseConnect(?IDispatch $dispatch = null, ?string $connect = null): Mysql
    {
        $connect = $this->createDatabaseConnectMock([
            'driver'             => 'mysql',
            'separate'           => false,
            'distributed'        => false,
            'master'             => [
                'host'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['HOST'],
                'port'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PORT'],
                'name'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['NAME'],
                'user'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['USER'],
                'password' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PASSWORD'],
                'charset'  => 'utf8',
                'options'  => [
                    PDO::ATTR_PERSISTENT        => false,
                    PDO::ATTR_CASE              => PDO::CASE_NATURAL,
                    PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_ORACLE_NULLS      => PDO::NULL_NATURAL,
                    PDO::ATTR_STRINGIFY_FETCHES => false,
                    PDO::ATTR_EMULATE_PREPARES  => false,
                ],
            ],
            'slave' => [],
        ], $connect, $dispatch);

        return $connect;
    }

    protected function truncateDatabase(array $tables): void
    {
        if (!$tables) {
            return;
        }

        if (isset($this->databaseConnects[0])) {
            $connect = $this->databaseConnects[0];
        } else {
            $connect = $this->createDatabaseConnect();
        }

        foreach ($tables as $table) {
            $sql = <<<'eot'
                [
                    "TRUNCATE TABLE `%s`",
                    []
                ]
                eot;
            $this->assertSame(
                sprintf($sql, $table),
                $this->varJson(
                    $connect
                        ->sql()
                        ->table($table)
                        ->truncate()
                )
            );

            $connect
                ->table($table)
                ->truncate();
        }
    }

    protected function metaWithoutDatabase(): void
    {
        Meta::setDatabaseResolver(null);
    }

    protected function metaWithDatabase(): void
    {
        Meta::setDatabaseResolver(function () {
            return $this->createDatabaseManager();
        });
    }

    protected function createDatabaseManager(): Manager
    {
        $container = new Container();
        $manager = new Manager($container);

        $this->assertInstanceof(IContainer::class, $manager->container());
        $this->assertInstanceof(Container::class, $manager->container());

        $option = new Option([
            'database' => [
                'default' => 'mysql',
                'connect' => [
                    'mysql' => [
                        'driver'   => 'mysql',
                        'host'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['HOST'],
                        'port'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PORT'],
                        'name'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['NAME'],
                        'user'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['USER'],
                        'password' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PASSWORD'],
                        'charset'  => 'utf8',
                        'options'  => [
                            PDO::ATTR_PERSISTENT        => false,
                            PDO::ATTR_CASE              => PDO::CASE_NATURAL,
                            PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION,
                            PDO::ATTR_ORACLE_NULLS      => PDO::NULL_NATURAL,
                            PDO::ATTR_STRINGIFY_FETCHES => false,
                            PDO::ATTR_EMULATE_PREPARES  => false,
                        ],
                        'separate'           => false,
                        'distributed'        => false,
                        'master'             => [],
                        'slave'              => [],
                    ],
                ],
            ],
        ]);

        $container->singleton('option', $option);
        $eventDispatch = $this->createMock(IDispatch::class);
        $this->assertNull($eventDispatch->handle('event'));
        $container->singleton(IDispatch::class, $eventDispatch);

        return $manager;
    }

    protected function createDatabaseManagerForMysqlPool(bool $inSwoole = true): Manager
    {
        $container = new Container();
        $manager = new Manager($container);

        $this->assertInstanceof(IContainer::class, $manager->container());
        $this->assertInstanceof(Container::class, $manager->container());

        $option = new Option([
            'database' => [
                'default' => 'mysqlPool',
                'connect' => [
                    'mysql' => [
                        'driver'   => 'mysql',
                        'host'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['HOST'],
                        'port'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PORT'],
                        'name'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['NAME'],
                        'user'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['USER'],
                        'password' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PASSWORD'],
                        'charset'  => 'utf8',
                        'options'  => [
                            PDO::ATTR_PERSISTENT        => false,
                            PDO::ATTR_CASE              => PDO::CASE_NATURAL,
                            PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION,
                            PDO::ATTR_ORACLE_NULLS      => PDO::NULL_NATURAL,
                            PDO::ATTR_STRINGIFY_FETCHES => false,
                            PDO::ATTR_EMULATE_PREPARES  => false,
                        ],
                        'separate'           => false,
                        'distributed'        => false,
                        'master'             => [],
                        'slave'              => [],
                    ],
                    'mysqlPool' => [
                        'driver'               => 'mysqlPool',
                        'mysql_connect'        => 'mysql',
                        'max_idle_connections' => 30,
                        'min_idle_connections' => 10,
                        'max_push_timeout'     => -1000,
                        'max_pop_timeout'      => 0,
                        'keep_alive_duration'  => 60000,
                        'retry_times'          => 3,
                    ],
                ],
            ],
        ]);

        $container->singleton('option', $option);
        $eventDispatch = $this->createMock(IDispatch::class);
        $this->assertNull($eventDispatch->handle('event'));
        $container->singleton(IDispatch::class, $eventDispatch);

        if (true === $inSwoole) {
            $coroutine = new Coroutine();
            $container->instance('coroutine', $coroutine);
            $container->setCoroutine($coroutine);
            $mysqlPool = $this->createMysqlPool($container, $manager);
            $container->instance('mysql.pool', $mysqlPool);
        }

        return $manager;
    }

    protected function createMysqlPool(IContainer $container, Manager $manager): MysqlPoolMock
    {
        $options = $container
            ->make('option')
            ->get('database\\connect.mysqlPool');

        return new MysqlPoolMock($manager, $options['mysql_connect'], $options);
    }

    protected function freeDatabaseConnects(): void
    {
        if (!$this->databaseConnects) {
            return;
        }

        // 释放数据库连接，否则会出现 MySQL 连接数过多
        // PDOException: PDO::__construct(): MySQL server has gone away
        foreach ($this->databaseConnects as $k => $connect) {
            unset($this->databaseConnects[$k]);
            $connect->close();
        }
    }

    protected function getLastSql(string $table): string
    {
        return Meta::instance($table)
            ->select()
            ->getLastSql();
    }
}

class MysqlPoolMock extends MysqlPools
{
    public function returnConnection(IConnection $connection): bool
    {
        return true;
    }
}

class MysqlNeedReconnectMock extends Mysql
{
    protected function needReconnect(PDOException $e): bool
    {
        return $this->reconnectRetry <= self::RECONNECT_MAX;
    }
}
