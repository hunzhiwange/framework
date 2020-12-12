<?php

declare(strict_types=1);

namespace Tests\Filesystem\Proxy;

use League\Flysystem\Filesystem as LeagueFilesystem;
use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Filesystem\Manager;
use Leevel\Filesystem\Proxy\Filesystem;
use Leevel\Option\Option;
use Tests\TestCase;

class FilesystemTest extends TestCase
{
    protected function setUp(): void
    {
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        Container::singletons()->clear();
    }

    public function testBaseUse(): void
    {
        $container = $this->createContainer();
        $manager = $this->createManager($container);
        $container->singleton('filesystems', function () use ($manager): Manager {
            return $manager;
        });

        $path = __DIR__.'/forManager';
        $this->assertInstanceof(LeagueFilesystem::class, $manager->getFilesystem());
        $manager->write('hellomanager.txt', 'manager');
        $file = $path.'/hellomanager.txt';

        $this->assertTrue(is_file($file));
        $this->assertSame('manager', file_get_contents($file));

        unlink($file);
        rmdir($path);
    }

    public function testProxy(): void
    {
        $container = $this->createContainer();
        $manager = $this->createManager($container);
        $container->singleton('filesystems', function () use ($manager): Manager {
            return $manager;
        });

        $path = __DIR__.'/forManager';
        $this->assertInstanceof(LeagueFilesystem::class, Filesystem::getFilesystem());
        Filesystem::write('hellomanager.txt', 'manager');
        $file = $path.'/hellomanager.txt';

        $this->assertTrue(is_file($file));
        $this->assertSame('manager', file_get_contents($file));

        unlink($file);
        rmdir($path);
    }

    protected function createManager(Container $container): Manager
    {
        $manager = new Manager($container);

        $this->assertInstanceof(IContainer::class, $manager->container());
        $this->assertInstanceof(Container::class, $manager->container());

        $option = new Option([
            'filesystem' => [
                'default' => 'local',
                'connect' => [
                    'local' => [
                        'driver'  => 'local',
                        'path'    => __DIR__.'/forManager',
                    ],
                ],
            ],
        ]);

        $container->singleton('option', $option);

        return $manager;
    }

    protected function createContainer(): Container
    {
        $container = Container::singletons();
        $container->clear();

        return $container;
    }
}
