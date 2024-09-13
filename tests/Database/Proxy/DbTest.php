<?php

declare(strict_types=1);

namespace Tests\Database\Proxy;

use Leevel\Database\Manager;
use Leevel\Database\Proxy\Db;
use Leevel\Di\Container;
use Tests\Database\DatabaseTestCase as TestCase;

final class DbTest extends TestCase
{
    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->tearDown();
    }

    #[\Override]
    protected function tearDown(): void
    {
        parent::tearDown();
        Container::singletons()->clear();
    }

    public function testBaseUse(): void
    {
        $container = $this->createContainer();
        $manager = $this->createDatabaseManager($container);
        $container->singleton('databases', static function () use ($manager): Manager {
            return $manager;
        });

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        self::assertSame(
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

        self::assertSame('tom', $result->name);
        self::assertSame('I love movie.', $result->content);
    }

    public function testProxy(): void
    {
        $container = $this->createContainer();
        $manager = $this->createDatabaseManager($container);
        $container->singleton('databases', static function () use ($manager): Manager {
            return $manager;
        });

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        self::assertSame(
            1,
            Db::proxy()
                ->table('guest_book')
                ->insert($data)
        );

        $result = Db::proxy()
            ->table('guest_book', 'name,content')
            ->where('id', 1)
            ->findOne()
        ;

        self::assertSame('tom', $result->name);
        self::assertSame('I love movie.', $result->content);
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
