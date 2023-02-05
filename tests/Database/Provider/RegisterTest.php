<?php

declare(strict_types=1);

namespace Tests\Database\Provider;

use Leevel\Database\Ddd\Meta;
use Leevel\Database\Manager;
use Leevel\Database\Mysql;
use Leevel\Database\Provider\Register;
use Leevel\Di\Container;
use Leevel\Event\IDispatch;
use Leevel\Option\Option;
use Tests\Database\DatabaseTestCase as TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class RegisterTest extends TestCase
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
        static::assertSame(
            1,
            $manager
                ->table('guest_book')
                ->insert($data)
        );
        $result = $manager
            ->table('guest_book', 'name,content')
            ->where('id', 1)
            ->findOne()
        ;
        static::assertSame('tom', $result->name);
        static::assertSame('I love movie.', $result->content);
        $manager->close();

        // database
        $mysql = $container->make('database');
        $this->assertInstanceof(Mysql::class, $mysql);
        $result = $mysql
            ->table('guest_book', 'name,content')
            ->where('id', 1)
            ->findOne()
        ;
        static::assertSame('tom', $result->name);
        static::assertSame('I love movie.', $result->content);
        $mysql->close();

        // meta
        $database = Meta::resolvedDatabase();
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
        static::assertSame(
            1,
            $manager
                ->table('guest_book')
                ->insert($data)
        );
        $result = $manager
            ->table('guest_book', 'name,content')
            ->where('id', 1)
            ->findOne()
        ;
        static::assertSame('tom', $result->name);
        static::assertSame('I love movie.', $result->content);
        $manager->close();
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
                        'driver' => 'mysql',
                        'host' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['HOST'],
                        'port' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PORT'],
                        'name' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['NAME'],
                        'user' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['USER'],
                        'password' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PASSWORD'],
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
                        'distributed' => false,
                        'master' => [],
                        'slave' => [],
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

        $container->singleton('option', $option);
        $eventDispatch = $this->createMock(IDispatch::class);
        static::assertNull($eventDispatch->handle('event'));
        $container->singleton(IDispatch::class, $eventDispatch);
        $cacheManager = $this->createCacheManager($container, $option, 'file');
        $container->singleton('caches', $cacheManager);
        $container->singleton('cache', $cacheManager->connect());

        return $container;
    }
}
