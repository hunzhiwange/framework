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

use Leevel\Cache\Load;
use Leevel\Di\Container;
use Tests\Cache\Pieces\Test1;
use Tests\Cache\Pieces\Test2;
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
    protected function tearDown(): void
    {
        $files = [
            'test1.php',
            'test4.php',
            'test2.php',
        ];

        foreach ($files as $val) {
            if (is_file($val = __DIR__.'/Pieces/cacheLoad/'.$val)) {
                unlink($val);
            }
        }

        $path = __DIR__.'/Pieces/cacheLoad';

        if (is_dir($path)) {
            rmdir($path);
        }
    }

    public function testBaseUse()
    {
        $container = new Container();
        $load = $this->createLoad($container);

        $result = $load->data([Test1::class]);
        $this->assertSame(['foo' => 'bar'], $result);

        $result = $load->data([Test1::class]);
        $this->assertSame(['foo' => 'bar'], $result);

        $load->refresh([Test1::class]);
    }

    public function testRefresh()
    {
        $container = new Container();
        $load = $this->createLoad($container);

        $result = $load->data([Test1::class]);
        $this->assertSame(['foo' => 'bar'], $result);

        $result = $load->data([Test1::class]);
        $this->assertSame(['foo' => 'bar'], $result);

        $file = __DIR__.'/Pieces/cacheLoad/test1.php';
        $this->assertTrue(is_file($file));

        $load->refresh([Test1::class]);
        $this->assertFalse(is_file($file));
    }

    public function testDataForce()
    {
        $container = new Container();
        $load = $this->createLoad($container);

        $result = $load->data([Test1::class]);
        $this->assertSame(['foo' => 'bar'], $result);

        $result = $load->data([Test1::class], [], true);
        $this->assertSame(['foo' => 'bar'], $result);
    }

    public function testCacheBlockType()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Cache `Tests\\Cache\\Pieces\\Test2` must implements `Leevel\\Cache\\IBlock`.'
        );

        $container = new Container();
        $load = $this->createLoad($container);

        $load->data([Test2::class]);
    }

    public function testWithParams()
    {
        $container = new Container();
        $load = $this->createLoad($container);

        $result = $load->data([Test4::class.':hello,world,foo,bar']);
        $this->assertSame(['hello', 'world', 'foo', 'bar'], $result);
    }

    public function testCacheNotFound()
    {
        $this->expectException(\ReflectionException::class);
        $this->expectExceptionMessage(
            'Class Tests\\Cache\\Pieces\\TestNotFound does not exist'
        );

        $container = new Container();
        $load = $this->createLoad($container);

        $load->data(['Tests\Cache\Pieces\TestNotFound']);
    }

    protected function createLoad(Container $container): Load
    {
        return new Load($container);
    }
}
