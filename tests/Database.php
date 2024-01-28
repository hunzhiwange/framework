<?php

declare(strict_types=1);

namespace Tests;

use Leevel\Cache\Manager as CacheManager;
use Leevel\Cache\Redis\PhpRedis;
use Leevel\Config\Config;
use Leevel\Database\Ddd\Meta;
use Leevel\Database\Manager;
use Leevel\Database\Mysql;
use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Event\IDispatch;
use PDO;
use PDOException;

/**
 * 数据辅助方法.
 */
trait Database
{
    protected $databaseConnects = [];

    protected function createDatabaseConnectMock(array $config = [], ?string $connect = null, ?IDispatch $dispatch = null): Mysql
    {
        if ($config) {
            return $this->createDatabaseConnectMockReal($config, $connect, $dispatch);
        }

        return $this->createDatabaseConnect($dispatch, $connect);
    }

    protected function createDatabaseConnectMockReal(array $config = [], ?string $connect = null, ?IDispatch $dispatch = null): Mysql
    {
        if (null === $connect) {
            $connect = Mysql::class;
        }

        $connect = new $connect(new Container(), $config, $dispatch);
        $this->databaseConnects[] = $connect;

        return $connect;
    }

    protected function createDatabaseConnect(?IDispatch $dispatch = null, ?string $connect = null): Mysql
    {
        $connect = $this->createDatabaseConnectMockReal([
            'driver' => 'mysql',
            'separate' => false,
            'distributed' => false,
            'master' => [
                'host' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['HOST'],
                'port' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PORT'],
                'name' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['NAME'],
                'user' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['USER'],
                'password' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PASSWORD'],
                'charset' => 'utf8',
                'configs' => [
                    \PDO::ATTR_PERSISTENT => false,
                    \PDO::ATTR_CASE => \PDO::CASE_NATURAL,
                    \PDO::ATTR_ORACLE_NULLS => \PDO::NULL_NATURAL,
                    \PDO::ATTR_STRINGIFY_FETCHES => false,
                    \PDO::ATTR_EMULATE_PREPARES => false,
                    \PDO::ATTR_TIMEOUT => 30,
                ],
            ],
            'slave' => [],
        ], $connect, $dispatch);

        return $connect;
    }

    protected function createDatabaseConnectWithInvalidPdoAttrErrmode(?IDispatch $dispatch = null, ?string $connect = null): Mysql
    {
        $connect = $this->createDatabaseConnectMockReal([
            'driver' => 'mysql',
            'separate' => false,
            'distributed' => false,
            'master' => [
                'host' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['HOST'],
                'port' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PORT'],
                'name' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['NAME'],
                'user' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['USER'],
                'password' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PASSWORD'],
                'charset' => 'utf8',
                'configs' => [
                    \PDO::ATTR_PERSISTENT => false,
                    \PDO::ATTR_CASE => \PDO::CASE_NATURAL,
                    \PDO::ATTR_ORACLE_NULLS => \PDO::NULL_NATURAL,
                    \PDO::ATTR_STRINGIFY_FETCHES => false,
                    \PDO::ATTR_EMULATE_PREPARES => false,
                    \PDO::ATTR_TIMEOUT => 30,
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
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
                    [],
                    false
                ]
                eot;
            $this->assertSame(
                sprintf($sql, $table),
                $this->varJsonSql(
                    $connect
                        ->table($table)
                        ->truncate(),
                    $connect
                )
            );
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

    protected function createDatabaseManager(?Container $container = null): Manager
    {
        if (null === $container) {
            $container = new Container();
        }
        $manager = new Manager($container);

        $this->assertInstanceof(IContainer::class, $manager->container());
        $this->assertInstanceof(Container::class, $manager->container());

        $config = new Config([
            'database' => [
                'default' => 'mysql',
                'connect' => [
                    'mysql' => [
                        'driver' => 'mysql',
                        'host' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['HOST'],
                        'port' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PORT'],
                        'name' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['NAME'],
                        'user' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['USER'],
                        'password' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PASSWORD'],
                        'charset' => 'utf8',
                        'configs' => [
                            \PDO::ATTR_PERSISTENT => false,
                            \PDO::ATTR_CASE => \PDO::CASE_NATURAL,
                            \PDO::ATTR_ORACLE_NULLS => \PDO::NULL_NATURAL,
                            \PDO::ATTR_STRINGIFY_FETCHES => false,
                            \PDO::ATTR_EMULATE_PREPARES => false,
                            \PDO::ATTR_TIMEOUT => 30,
                        ],
                        'separate' => false,
                        'distributed' => false,
                        'master' => [],
                        'slave' => [],
                    ],
                    'password_right' => [
                        'driver' => 'mysql',
                        'password' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PASSWORD'],
                    ],
                    'password_not_right' => [
                        'driver' => 'mysql',
                        'password' => 'not right',
                    ],
                ],
            ],
            'cache' => [
                'default' => 'file',
                'expire' => 86400,
                'time_preset' => [],
                'connect' => [
                    'file' => [
                        'driver' => 'file',
                        'path' => __DIR__.'/databaseCacheManager',
                        'expire' => null,
                    ],
                    'redis' => [
                        'driver' => 'redis',
                        'host' => $GLOBALS['LEEVEL_ENV']['CACHE']['REDIS']['HOST'],
                        'port' => $GLOBALS['LEEVEL_ENV']['CACHE']['REDIS']['PORT'],
                        'password' => $GLOBALS['LEEVEL_ENV']['CACHE']['REDIS']['PASSWORD'],
                        'select' => 0,
                        'timeout' => 0,
                        'persistent' => false,
                        'expire' => null,
                    ],
                ],
            ],
        ]);

        $container->singleton('config', $config);
        $eventDispatch = $this->createMock(IDispatch::class);
        $this->assertNull($eventDispatch->handle('event'));
        $container->singleton(IDispatch::class, $eventDispatch);
        $cacheManager = $this->createCacheManager($container, $config, 'file');
        $container->singleton('caches', $cacheManager);
        $container->singleton('cache', $cacheManager->connect());

        $this->databaseConnects[] = $manager->connect();

        return $manager;
    }

    protected function createCacheManager(Container $container, Config $config, string $connect = 'file'): CacheManager
    {
        $manager = new CacheManager($container);

        $this->assertInstanceof(IContainer::class, $manager->container());
        $this->assertInstanceof(Container::class, $manager->container());

        if ('redis' === $connect) {
            $redis = new PhpRedis($config->get('cache\\connect.redis'));
            $container->singleton('redis', $redis);
        }

        return $manager;
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
        $select = Meta::instance($table)->select();
        $lastSql = $select->getLastSql();
        $this->databaseConnects[] = $select->databaseConnect();

        return $lastSql;
    }
}

class MysqlNeedReconnectMock extends Mysql
{
    protected function needReconnect(\PDOException $e): bool
    {
        return $this->reconnectRetry <= self::RECONNECT_MAX;
    }
}
