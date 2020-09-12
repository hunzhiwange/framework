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

namespace Tests\Cache\Proxy;

use Leevel\Cache\Manager;
use Leevel\Cache\Proxy\Cache;
use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Filesystem\Helper;
use Leevel\Option\Option;
use Tests\TestCase;

class CacheTest extends TestCase
{
    protected function setUp(): void
    {
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        Helper::deleteDirectory(__DIR__.'/cacheManager');
        Container::singletons()->clear();
    }

    public function testBaseUse(): void
    {
        $container = $this->createContainer();
        $manager = $this->createManager($container);
        $container->singleton('caches', function () use ($manager) {
            return $manager;
        });

        $manager->set('manager-foo', 'bar');
        $this->assertSame('bar', $manager->get('manager-foo'));
        $manager->delete('manager-foo');
        $this->assertFalse($manager->get('manager-foo'));
    }

    public function testProxy(): void
    {
        $container = $this->createContainer();
        $manager = $this->createManager($container);
        $container->singleton('caches', function () use ($manager) {
            return $manager;
        });

        Cache::set('manager-foo', 'bar');
        $this->assertSame('bar', Cache::get('manager-foo'));
        Cache::delete('manager-foo');
        $this->assertFalse(Cache::get('manager-foo'));
    }

    protected function createManager(Container $container): Manager
    {
        $manager = new Manager($container);

        $this->assertInstanceof(IContainer::class, $manager->container());
        $this->assertInstanceof(Container::class, $manager->container());

        $option = new Option([
            'cache' => [
                'default'     => 'file',
                'expire'      => 86400,
                'time_preset' => [],
                'connect'     => [
                    'file' => [
                        'driver'    => 'file',
                        'path'      => __DIR__.'/cacheManager',
                        'expire'    => null,
                    ],
                    'redis' => [
                        'driver'     => 'redis',
                        'host'       => $GLOBALS['LEEVEL_ENV']['CACHE']['REDIS']['HOST'],
                        'port'       => $GLOBALS['LEEVEL_ENV']['CACHE']['REDIS']['PORT'],
                        'password'   => $GLOBALS['LEEVEL_ENV']['CACHE']['REDIS']['PASSWORD'],
                        'select'     => 0,
                        'timeout'    => 0,
                        'persistent' => false,
                        'expire'     => null,
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
