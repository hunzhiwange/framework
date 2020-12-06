<?php

declare(strict_types=1);

namespace Tests\Kernel\Testing;

use Leevel\Database\Manager;
use Leevel\Di\Container;
use Leevel\Kernel\Testing\Database;
use Tests\Database\DatabaseTestCase as TestCase;

class DatabaseTest extends TestCase
{
    use Database;

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

    public function testTruncateDatabase(): void
    {
        $container = $this->createContainer();
        $manager = $this->createDatabaseManager($container);
        $container->singleton('databases', function () use ($manager): Manager {
            return $manager;
        });

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        $this->assertSame(
            1,
            $manager
                ->table('guest_book')
                ->insert($data)
        );

        $result = $manager->table('guest_book', 'name,content')
            ->where('id', 1)
            ->findOne();

        $this->assertSame('tom', $result->name);
        $this->assertSame('I love movie.', $result->content);

        $this->assertNull($this->truncateDatabase(['guest_book']));

        $result = $manager->table('guest_book', 'name,content')
            ->where('id', 1)
            ->findOne();

        $this->assertTrue(!isset($result->name));
        $this->assertTrue(!isset($result->content));
    }

    public function testTruncateDatabaseWithEmptyTables(): void
    {
        $this->assertNull($this->truncateDatabase([]));
    }

    protected function createContainer(): Container
    {
        $container = Container::singletons();
        $container->clear();

        return $container;
    }
}
