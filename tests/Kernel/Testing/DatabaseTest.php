<?php

declare(strict_types=1);

namespace Tests\Kernel\Testing;

use Leevel\Database\Manager;
use Leevel\Di\Container;
use Leevel\Kernel\Testing\Database;
use Tests\Database\DatabaseTestCase as TestCase;

final class DatabaseTest extends TestCase
{
    use Database;

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

    public function testTruncateDatabase(): void
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

        $result = $manager->table('guest_book', 'name,content')
            ->where('id', 1)
            ->findOne()
        ;

        self::assertSame('tom', $result->name);
        self::assertSame('I love movie.', $result->content);

        self::assertNull($this->truncateDatabase(['guest_book']));

        $result = $manager->table('guest_book', 'name,content')
            ->where('id', 1)
            ->findOne()
        ;

        self::assertTrue(!isset($result->name));
        self::assertTrue(!isset($result->content));
    }

    public function testTruncateDatabaseWithEmptyTables(): void
    {
        self::assertNull($this->truncateDatabase([]));
    }

    protected function createContainer(): Container
    {
        $container = Container::singletons();
        $container->clear();

        return $container;
    }
}
