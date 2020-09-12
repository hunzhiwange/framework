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

use Leevel\Cache\Load;
use Leevel\Cache\Proxy\Load as ProxyLoad;
use Leevel\Di\Container;
use Leevel\Filesystem\Helper;
use Tests\Cache\Pieces\Test1;
use Tests\TestCase;

class LoadTest extends TestCase
{
    protected function setUp(): void
    {
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        Helper::deleteDirectory(dirname(__DIR__).'/Pieces/cacheLoad');
        Container::singletons()->clear();
    }

    public function testBaseUse(): void
    {
        $container = $this->createContainer();
        $load = $this->createLoad($container);
        $container->singleton('cache.load', function () use ($load) {
            return $load;
        });

        $result = $load->data([Test1::class]);
        $this->assertSame(['foo' => 'bar'], $result);
        $result = $load->data([Test1::class]);
        $this->assertSame(['foo' => 'bar'], $result);
        $load->refresh([Test1::class]);
    }

    public function testProxy(): void
    {
        $container = $this->createContainer();
        $load = $this->createLoad($container);
        $container->singleton('cache.load', function () use ($load) {
            return $load;
        });

        $result = ProxyLoad::data([Test1::class]);
        $this->assertSame(['foo' => 'bar'], $result);
        $result = ProxyLoad::data([Test1::class]);
        $this->assertSame(['foo' => 'bar'], $result);
        ProxyLoad::refresh([Test1::class]);
    }

    protected function createContainer(): Container
    {
        $container = Container::singletons();
        $container->clear();

        return $container;
    }

    protected function createLoad(Container $container): Load
    {
        return new Load($container);
    }
}
