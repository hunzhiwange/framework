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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Cache;

use Leevel\Cache\Helper;
use Leevel\Cache\ICache;
use Leevel\Di\Container;
use Tests\TestCase;

/**
 * helper test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.08.10
 *
 * @version 1.0
 */
class HelperTest extends TestCase
{
    protected function setUp(): void
    {
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        Container::singletons()->clear();
    }

    public function testCache(): void
    {
        $cache = $this->createMock(ICache::class);
        $this->assertNull($cache->set('foo', 'bar'));
        $cache->method('get')->willReturn('bar');
        $this->assertSame('bar', $cache->get('foo'));

        $container = $this->createContainer();
        $container->singleton('caches', function () use ($cache) {
            return $cache;
        });

        $this->assertInstanceof(ICache::class, f('Leevel\\Cache\\Helper\\cache'));
        $this->assertNull(f('Leevel\\Cache\\Helper\\cache_set', ['foo' => 'bar']));
        $this->assertSame('bar', f('Leevel\\Cache\\Helper\\cache_get', 'foo'));
    }

    public function testCacheHelper(): void
    {
        $cache = $this->createMock(ICache::class);
        $this->assertNull($cache->set('foo', 'bar'));
        $cache->method('get')->willReturn('bar');
        $this->assertSame('bar', $cache->get('foo'));

        $container = $this->createContainer();
        $container->singleton('caches', function () use ($cache) {
            return $cache;
        });

        $this->assertInstanceof(ICache::class, Helper::cache());
        $this->assertNull(Helper::cacheSet(['foo' => 'bar']));
        $this->assertSame('bar', Helper::cacheGet('foo'));
    }

    public function testHelperNotFound(): void
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage(
            'Call to undefined function Leevel\\Cache\\Helper\\not_found()'
        );

        $this->assertFalse(Helper::notFound());
    }

    protected function createContainer(): Container
    {
        $container = Container::singletons();
        $container->clear();

        return $container;
    }
}
