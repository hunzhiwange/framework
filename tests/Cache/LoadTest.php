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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Cache;

use Leevel\Cache\Cache;
use Leevel\Cache\File;
use Leevel\Cache\ICache;
use Leevel\Cache\Load;
use Leevel\Di\Container;
use Tests\Cache\Pieces\Test1;
use Tests\Cache\Pieces\Test2;
use Tests\Cache\Pieces\Test3;
use Tests\Cache\Pieces\Test4;
use Tests\TestCase;

/**
 * load test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.29
 *
 * @version 1.0
 */
class LoadTest extends TestCase
{
    protected function tearDown()
    {
        $files = [
            '_Tests.Cache.Pieces.Test1.php',
            '_Tests.Cache.Pieces.Test2@hello.php',
            '_Tests.Cache.Pieces.Test4.hello,world,foo,bar.php',
        ];

        foreach ($files as $val) {
            if (is_file($val = __DIR__.'/'.$val)) {
                unlink($val);
            }
        }
    }

    public function testBaseUse()
    {
        $container = new Container();

        $load = $this->createLoad($container);

        $this->assertInstanceof(ICache::class, $load->getCache());
        $this->assertInstanceof(Cache::class, $load->getCache());

        $result = $load->data(Test1::class);

        $this->assertSame(['foo' => 'bar'], $result);

        $result = $load->data(Test1::class);

        $this->assertSame(['foo' => 'bar'], $result);

        $load->refresh(Test1::class);
    }

    public function testSwitchCache()
    {
        $container = new Container();

        $load = $this->createLoad($container);

        $this->assertInstanceof(ICache::class, $load->getCache());
        $this->assertInstanceof(Cache::class, $load->getCache());

        $cache = $this->createMock(ICache::class);

        $this->assertNull($load->switchCache($cache));

        $this->assertInstanceof(ICache::class, $load->getCache());
    }

    public function testRefresh()
    {
        $container = new Container();

        $load = $this->createLoad($container);

        $result = $load->data(Test1::class);

        $this->assertSame(['foo' => 'bar'], $result);

        $result = $load->data(Test1::class);

        $this->assertSame(['foo' => 'bar'], $result);

        $file = __DIR__.'/_Tests.Cache.Pieces.Test1.php';

        $this->assertTrue(is_file($file));

        $load->refresh(Test1::class);

        $this->assertFalse(is_file($file));
    }

    public function testDataLoaded()
    {
        $container = new Container();

        $load = $this->createLoad($container);

        $result = $load->dataLoaded(Test1::class);

        $this->assertFalse($result);

        $result = $load->data(Test1::class);

        $this->assertSame(['foo' => 'bar'], $result);

        $result = $load->dataLoaded(Test1::class);

        $this->assertSame(['foo' => 'bar'], $result);
    }

    public function testDataForce()
    {
        $container = new Container();

        $load = $this->createLoad($container);

        $result = $load->data(Test1::class);

        $this->assertSame(['foo' => 'bar'], $result);

        $result = $load->data(Test1::class, [], true);

        $this->assertSame(['foo' => 'bar'], $result);
    }

    public function testWithCustom()
    {
        $container = new Container();

        $load = $this->createLoad($container);

        $result = $load->data(Test2::class.'@hello');

        $this->assertSame(['hello' => 'world'], $result);

        $result = $load->data(Test2::class.'@hello');

        $this->assertSame(['hello' => 'world'], $result);
    }

    public function testWithParams()
    {
        $container = new Container();

        $load = $this->createLoad($container);

        $result = $load->data(Test4::class.':hello,world,foo,bar');

        $this->assertSame(['hello', 'world', 'foo', 'bar'], $result);
    }

    public function testCacheNotFound()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Cache Tests\Cache\Pieces\TestNotFound is not valid.'
        );

        $container = new Container();

        $load = $this->createLoad($container);

        $result = $load->data('Tests\Cache\Pieces\TestNotFound');
    }

    public function testCustomMethodNotFound()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Cache Tests\Cache\Pieces\Test3@notFound is not a callable.'
        );

        $container = new Container();

        $load = $this->createLoad($container);

        $result = $load->data(Test3::class.'@notFound');
    }

    protected function createLoad(Container $container): Load
    {
        $cache = new Cache(new File([
            'path' => __DIR__,
        ]));

        return new Load($container, $cache);
    }
}
