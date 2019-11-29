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

namespace Tests\Database\Provider;

use Leevel\Database\Ddd\Meta;
use Leevel\Database\Manager;
use Leevel\Database\Mysql;
use Leevel\Database\Mysql\MysqlPool;
use Leevel\Database\Provider\Register;
use Leevel\Di\Container;
use Leevel\Event\IDispatch;
use Leevel\Option\Option;
use Leevel\Protocol\Coroutine;
use PDO;
use Tests\Database\DatabaseTestCase as TestCase;

/**
 * register test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.10.10
 *
 * @version 1.0
 */
class RegisterTest extends TestCase
{
    public function testBaseUse(): void
    {
        $test = new Register($container = $this->createContainer());
        $test->register();
        $container->alias($test->providers());
        $test->bootstrap($this->createMock(IDispatch::class));

        // databases
        $manager = $container->make('databases');
        $data = ['name' => 'tom', 'content' => 'I love movie.'];
        $this->assertSame(
            1,
            $manager
                ->table('guest_book')
                ->insert($data));
        $result = $manager
            ->table('guest_book', 'name,content')
            ->where('id', 1)
            ->findOne();
        $this->assertSame('tom', $result->name);
        $this->assertSame('I love movie.', $result->content);
        $manager->close();

        // database
        $mysql = $container->make('database');
        $this->assertInstanceof(Mysql::class, $mysql);
        $result = $mysql
            ->table('guest_book', 'name,content')
            ->where('id', 1)
            ->findOne();
        $this->assertSame('tom', $result->name);
        $this->assertSame('I love movie.', $result->content);
        $mysql->close();

        // meta
        $database = Meta::resolveDatabase();
        $this->assertInstanceof(Manager::class, $database);
        Meta::setDatabaseResolver(null);
    }

    public function testUseAlias(): void
    {
        $test = new Register($container = $this->createContainer());
        $test->register();
        $container->alias($test->providers());

        $test->bootstrap($this->createMock(IDispatch::class));
        $manager = $container->make('Leevel\\Database\\Manager');
        $data = ['name' => 'tom', 'content' => 'I love movie.'];
        $this->assertSame(
            1,
            $manager
                ->table('guest_book')
                ->insert($data));
        $result = $manager
            ->table('guest_book', 'name,content')
            ->where('id', 1)
            ->findOne();
        $this->assertSame('tom', $result->name);
        $this->assertSame('I love movie.', $result->content);
        $manager->close();
    }

    public function testMysqlPool(): void
    {
        $test = new Register($container = $this->createContainerWithMysqlPool());
        $test->register();
        $container->alias($test->providers());
        $test->bootstrap($this->createMock(IDispatch::class));

        // databases
        $manager = $container->make('databases');
        $this->assertInstanceof(Manager::class, $manager);
        $mysqlPool = $container->make('mysql.pool');
        $this->assertInstanceof(MysqlPool::class, $mysqlPool);

        // meta
        $database = Meta::resolveDatabase();
        $this->assertInstanceof(Manager::class, $database);
        Meta::setDatabaseResolver(null);
    }

    protected function getDatabaseTable(): array
    {
        return ['guest_book'];
    }

    protected function createContainer(): Container
    {
        $container = new Container();

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

        return $container;
    }

    protected function createContainerWithMysqlPool(): Container
    {
        $container = new Container();

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

        $coroutine = new Coroutine();
        $container->instance('coroutine', $coroutine);
        $container->setCoroutine($coroutine);

        return $container;
    }
}
