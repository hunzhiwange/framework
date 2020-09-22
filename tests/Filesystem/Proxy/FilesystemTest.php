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
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
        $manager->put('hellomanager.txt', 'manager');
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
        Filesystem::put('hellomanager.txt', 'manager');
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
