<?php

declare(strict_types=1);

namespace Tests\Database;

use Leevel\Kernel\Utils\Api;
use Tests\Database\DatabaseTestCase as TestCase;

#[Api([
    'zh-CN:title' => '数据库配置',
    'path' => 'database/config',
    'zh-CN:description' => <<<'EOT'
我们可以在 `config/database.php` 文件中定义数据库连接。
EOT,
])]
final class ManagerTest extends TestCase
{
    #[Api([
        'zh-CN:title' => '基本配置',
        'zh-CN:description' => <<<'EOT'
数据库配置基本定义功能展示。

`数据库配置`

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Database::class, 'createDatabaseManager')]}
```

请使用这样的格式来定义连接，系统会自动帮你访问数据库。
系统底层实质上会使用 `\Leevel\Config\Config` 来管理配置信息。
EOT,
    ])]
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

    public function test1(): void
    {
        $manager = $this->createDatabaseManager();
        $connect = $manager->reconnect();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        static::assertSame(
            1,
            $connect
                ->table('guest_book')
                ->insert($data)
        );

        $result = $connect->table('guest_book', 'name,content')
            ->where('id', 1)
            ->findOne()
        ;

        static::assertSame('tom', $result->name);
        static::assertSame('I love movie.', $result->content);
    }

    #[Api([
        'zh-CN:title' => '数据库主从设置',
        'zh-CN:description' => <<<'EOT'
框架允许用户一个主数据库作为写入、更新以及删除,外加多个附属从数据库作为只读数据库来共同提供数据库服务。
需要系统配置需要开启 `separate`,主要用于读写分离。
`master` 为主数据库负责写，`slave` 为附属从数据库设置负责读。
EOT,
    ])]
    public function testParseDatabaseConfigSeparateIsTrue(): void
    {
        $manager = $this->createDatabaseManager();

        $config = [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => 3306,
            'name' => 'test',
            'user' => 'root',
            'password' => '123456',
            'charset' => 'utf8',
            'configs' => [
                \PDO::ATTR_PERSISTENT => false,
                \PDO::ATTR_CASE => \PDO::CASE_NATURAL,
                \PDO::ATTR_ORACLE_NULLS => \PDO::NULL_NATURAL,
                \PDO::ATTR_STRINGIFY_FETCHES => false,
                \PDO::ATTR_EMULATE_PREPARES => false,
                \PDO::ATTR_TIMEOUT => 30,
            ],
            'separate' => true,
            'master' => [],
            'slave' => ['host' => '127.0.0.1'],
        ];

        $configNew = $this->invokeTestMethod($manager, 'normalizeDatabaseConfig', [$config]);

        $data = <<<'eot'
            {
                "driver": "mysql",
                "separate": true,
                "master": {
                    "host": "127.0.0.1",
                    "port": 3306,
                    "name": "test",
                    "user": "root",
                    "password": "123456",
                    "charset": "utf8",
                    "configs": {
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
                        "configs": {
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
            $this->varJson($configNew)
        );
    }

    #[Api([
        'zh-CN:title' => '附属从数据库支持二维数组',
        'zh-CN:description' => <<<'EOT'
从数据库支持多个，支持二维数组
EOT,
    ])]
    public function testParseDatabaseConfigSeparateIsTrueWithTwoDimensionalArray(): void
    {
        $manager = $this->createDatabaseManager();

        $config = [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => 3306,
            'name' => 'test',
            'user' => 'root',
            'password' => '123456',
            'charset' => 'utf8',
            'configs' => [
                \PDO::ATTR_PERSISTENT => false,
                \PDO::ATTR_CASE => \PDO::CASE_NATURAL,
                \PDO::ATTR_ORACLE_NULLS => \PDO::NULL_NATURAL,
                \PDO::ATTR_STRINGIFY_FETCHES => false,
                \PDO::ATTR_EMULATE_PREPARES => false,
                \PDO::ATTR_TIMEOUT => 30,
            ],
            'separate' => true,
            'master' => [],
            'slave' => [
                ['host' => '127.0.0.1'],
                ['password' => '123456'],
            ],
        ];

        $configNew = $this->invokeTestMethod($manager, 'normalizeDatabaseConfig', [$config]);

        $data = <<<'eot'
            {
                "driver": "mysql",
                "separate": true,
                "master": {
                    "host": "127.0.0.1",
                    "port": 3306,
                    "name": "test",
                    "user": "root",
                    "password": "123456",
                    "charset": "utf8",
                    "configs": {
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
                        "configs": {
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
                        "configs": {
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
            $this->varJson($configNew)
        );
    }

    #[Api([
        'zh-CN:title' => '数据库设置只支持数组',
        'zh-CN:description' => <<<'EOT'
数据库主从连接只支持数组。
EOT,
    ])]
    public function testParseDatabaseConfigMasterAndSlaveMustBeAnArray(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Database config `slave` must be an array.'
        );

        $manager = $this->createDatabaseManager();

        $config = [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => 3306,
            'name' => 'test',
            'user' => 'root',
            'password' => '123456',
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
            'master' => [],
            'slave' => 'notarray',
        ];

        $this->invokeTestMethod($manager, 'normalizeDatabaseConfig', [$config]);
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
