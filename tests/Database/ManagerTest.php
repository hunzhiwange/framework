<?php

declare(strict_types=1);

namespace Tests\Database;

use Tests\Database\DatabaseTestCase as TestCase;

/**
 * @api(
 *     zh-CN:title="数据库配置",
 *     path="database/config",
 *     zh-CN:description="我们可以在 `option/database.php` 文件中定义数据库连接。",
 * )
 *
 * @internal
 */
final class ManagerTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="基本配置",
     *     zh-CN:description="
     * 数据库配置基本定义功能展示。
     *
     * `数据库配置`
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Database::class, 'createDatabaseManager')]}
     * ```
     *
     * 请使用这样的格式来定义连接，系统会自动帮你访问数据库。
     * 系统底层实质上会使用 `\Leevel\Option\Option` 来管理配置信息。
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $manager = $this->createDatabaseManager();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        static::assertSame(
            1,
            $manager
                ->table('guest_book')
                ->insert($data)
        );

        $result = $manager->table('guest_book', 'name,content')
            ->where('id', 1)
            ->findOne()
        ;

        static::assertSame('tom', $result->name);
        static::assertSame('I love movie.', $result->content);
    }

    /**
     * @api(
     *     zh-CN:title="数据库主从设置",
     *     zh-CN:description="
     * QueryPHP 允许用户一个主数据库作为写入、更新以及删除,外加多个附属从数据库作为只读数据库来共同提供数据库服务。
     * 多个数据库需要需要开启 `distributed`，而 `separate` 主要用于读写分离。
     * `master` 为主数据库，`slave` 为附属从数据库设置。
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testParseDatabaseOptionDistributedIsTrue(): void
    {
        $manager = $this->createDatabaseManager();

        $option = [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => 3306,
            'name' => 'test',
            'user' => 'root',
            'password' => '123456',
            'charset' => 'utf8',
            'options' => [
                \PDO::ATTR_PERSISTENT => false,
                \PDO::ATTR_CASE => \PDO::CASE_NATURAL,
                \PDO::ATTR_ORACLE_NULLS => \PDO::NULL_NATURAL,
                \PDO::ATTR_STRINGIFY_FETCHES => false,
                \PDO::ATTR_EMULATE_PREPARES => false,
                \PDO::ATTR_TIMEOUT => 30,
            ],
            'separate' => false,
            'distributed' => true,
            'master' => [],
            'slave' => ['host' => '127.0.0.1'],
        ];

        $optionNew = $this->invokeTestMethod($manager, 'normalizeDatabaseOption', [$option]);

        $data = <<<'eot'
            {
                "driver": "mysql",
                "separate": false,
                "distributed": true,
                "master": {
                    "host": "127.0.0.1",
                    "port": 3306,
                    "name": "test",
                    "user": "root",
                    "password": "123456",
                    "charset": "utf8",
                    "options": {
                        "12": false,
                        "8": 0,
                        "11": 0,
                        "17": false,
                        "20": false,
                        "2": 30
                    }
                },
                "slave": [
                    {
                        "host": "127.0.0.1",
                        "port": 3306,
                        "name": "test",
                        "user": "root",
                        "password": "123456",
                        "charset": "utf8",
                        "options": {
                            "12": false,
                            "8": 0,
                            "11": 0,
                            "17": false,
                            "20": false,
                            "2": 30
                        }
                    }
                ]
            }
            eot;

        static::assertSame(
            $data,
            $this->varJson($optionNew)
        );
    }

    /**
     * @api(
     *     zh-CN:title="附属从数据库支持二维数组",
     *     zh-CN:description="从数据库支持多个，支持二维数组",
     *     zh-CN:note="",
     * )
     */
    public function testParseDatabaseOptionDistributedIsTrueWithTwoDimensionalArray(): void
    {
        $manager = $this->createDatabaseManager();

        $option = [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => 3306,
            'name' => 'test',
            'user' => 'root',
            'password' => '123456',
            'charset' => 'utf8',
            'options' => [
                \PDO::ATTR_PERSISTENT => false,
                \PDO::ATTR_CASE => \PDO::CASE_NATURAL,
                \PDO::ATTR_ORACLE_NULLS => \PDO::NULL_NATURAL,
                \PDO::ATTR_STRINGIFY_FETCHES => false,
                \PDO::ATTR_EMULATE_PREPARES => false,
                \PDO::ATTR_TIMEOUT => 30,
            ],
            'separate' => false,
            'distributed' => true,
            'master' => [],
            'slave' => [
                ['host' => '127.0.0.1'],
                ['password' => '123456'],
            ],
        ];

        $optionNew = $this->invokeTestMethod($manager, 'normalizeDatabaseOption', [$option]);

        $data = <<<'eot'
            {
                "driver": "mysql",
                "separate": false,
                "distributed": true,
                "master": {
                    "host": "127.0.0.1",
                    "port": 3306,
                    "name": "test",
                    "user": "root",
                    "password": "123456",
                    "charset": "utf8",
                    "options": {
                        "12": false,
                        "8": 0,
                        "11": 0,
                        "17": false,
                        "20": false,
                        "2": 30
                    }
                },
                "slave": [
                    {
                        "host": "127.0.0.1",
                        "port": 3306,
                        "name": "test",
                        "user": "root",
                        "password": "123456",
                        "charset": "utf8",
                        "options": {
                            "12": false,
                            "8": 0,
                            "11": 0,
                            "17": false,
                            "20": false,
                            "2": 30
                        }
                    },
                    {
                        "password": "123456",
                        "host": "127.0.0.1",
                        "port": 3306,
                        "name": "test",
                        "user": "root",
                        "charset": "utf8",
                        "options": {
                            "12": false,
                            "8": 0,
                            "11": 0,
                            "17": false,
                            "20": false,
                            "2": 30
                        }
                    }
                ]
            }
            eot;

        static::assertSame(
            $data,
            $this->varJson($optionNew)
        );
    }

    /**
     * @api(
     *     zh-CN:title="数据库设置只支持数组",
     *     zh-CN:description="数据库主从连接只支持数组。",
     *     zh-CN:note="",
     * )
     */
    public function testParseDatabaseOptionMasterAndSlaveMustBeAnArray(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Database option `slave` must be an array.'
        );

        $manager = $this->createDatabaseManager();

        $option = [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => 3306,
            'name' => 'test',
            'user' => 'root',
            'password' => '123456',
            'charset' => 'utf8',
            'options' => [
                \PDO::ATTR_PERSISTENT => false,
                \PDO::ATTR_CASE => \PDO::CASE_NATURAL,
                \PDO::ATTR_ORACLE_NULLS => \PDO::NULL_NATURAL,
                \PDO::ATTR_STRINGIFY_FETCHES => false,
                \PDO::ATTR_EMULATE_PREPARES => false,
                \PDO::ATTR_TIMEOUT => 30,
            ],
            'separate' => false,
            'distributed' => true,
            'master' => [],
            'slave' => 'notarray',
        ];

        $this->invokeTestMethod($manager, 'normalizeDatabaseOption', [$option]);
    }

    public function testPDOQueryPropertyAttrErrmodeCannotBeSet(): void
    {
        $this->expectException(\Leevel\Database\ConnectionException::class);
        $this->expectExceptionMessage(
            'PDO query property \\PDO::ATTR_ERRMODE cannot be set,it is always \\PDO::ERRMODE_EXCEPTION.'
        );

        $manager = $this->createDatabaseConnectWithInvalidPdoAttrErrmode();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        static::assertSame(
            1,
            $manager
                ->table('guest_book')
                ->insert($data)
        );
    }

    protected function getDatabaseTable(): array
    {
        return ['guest_book'];
    }
}
