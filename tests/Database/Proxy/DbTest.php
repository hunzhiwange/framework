<?php

declare(strict_types=1);

namespace Tests\Database\Proxy;

use Leevel\Database\Manager;
use Leevel\Database\Proxy\Db;
use Leevel\Di\Container;
use Tests\Database\DatabaseTestCase as TestCase;

final class DbTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Container::singletons()->clear();
    }

    public function testBaseUse(): void
    {
        $container = $this->createContainer();
        $manager = $this->createDatabaseManager($container);
        $container->singleton('databases', function () use ($manager): Manager {
            return $manager;
        });

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

    public function testProxy(): void
    {
        $container = $this->createContainer();
        $manager = $this->createDatabaseManager($container);
        $container->singleton('databases', function () use ($manager): Manager {
            return $manager;
        });

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        static::assertSame(
            1,
            Db::table('guest_book')
                ->insert($data)
        );

        $result = Db::table('guest_book', 'name,content')
            ->where('id', 1)
            ->findOne()
        ;

        static::assertSame('tom', $result->name);
        static::assertSame('I love movie.', $result->content);
    }

    protected function createContainer(): Container
    {
        $container = Container::singletons();
        $container->clear();

        return $container;
    }

    protected function getDatabaseTable(): array
    {
        return ['guest_book'];
    }
}
