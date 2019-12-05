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
 *
 * @api(
 *     title="缓存块载入",
 *     path="component/cache/load",
 *     description="
 * QueryPHP 提供了缓存块自动载入功能，缓存块类似于缓存的 `remember` 功能，一个类代表一个缓存块。
 *
 * ### 定义缓存块
 *
 * **例子 \Tests\Cache\Pieces\Test1**
 *
 * ``` php
 * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Cache\Pieces\Test1::class)]}
 * ```
 *
 * 缓冲块需要实现 `\Leevel\Cache\IBlock` 接口，即可统一进行管理。
 *
 * **接口 \Leevel\Cache\IBlock**
 *
 * ``` php
 * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Leevel\Cache\IBlock::class)]}
 * ```
 *
 * 缓存块实现非常灵活，可以非常轻松地使用。
 *
 * ### 载入缓存片段
 *
 * 缓存载入服务 `cache.load` 被系统注册到服务容器中了，可以使用代理 `proxy` 来调用。
 *
 * ``` php
 * \Leevel\Cache\Proxy\Load::data(array $names, array $option = [], bool $force = false): array;
 * \Leevel\Cache\Proxy\Load::refresh(array $names): void;
 * ```
 * ",
 * )
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

    /**
     * @api(
     *     title="data 载入缓存块数据",
     *     description="
     * 通过 `data` 即可载入缓存块数据，缓存直接传递缓存块的类名字即可。
     *
     * ``` php
     * data(array $names, array $option = [], bool $force = false): array;
     * ```
     *
     * 配置 `$option` 和 缓存功能中的 `set` 的用法一致。
     * ",
     *     note="",
     * )
     */
    public function testBaseUse(): void
    {
        $container = new Container();
        $load = $this->createLoad($container);

        $result = $load->data([Test1::class]);
        $this->assertSame(['foo' => 'bar'], $result);

        $result = $load->data([Test1::class]);
        $this->assertSame(['foo' => 'bar'], $result);

        $load->refresh([Test1::class]);
    }

    /**
     * @api(
     *     title="refresh 刷新缓存块数据",
     *     description="
     * 通过 `refresh` 即可刷新缓存块数据，缓存直接传递缓存块的类名字即可。
     *
     * ``` php
     * refresh(array $names): void;
     * ```
     *
     * 刷新缓存块本质是删除缓存块数据，下次请求自动生成。
     * ",
     *     note="",
     * )
     */
    public function testRefresh(): void
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

    /**
     * @api(
     *     title="data 强制载入缓存块数据",
     *     description="`data` 方法支持强制获取缓存数据。",
     *     note="`$force` 参数强制从原始数据源获取缓存，并且会刷新缓存数据。",
     * )
     */
    public function testDataForce(): void
    {
        $container = new Container();
        $load = $this->createLoad($container);

        $result = $load->data([Test1::class]);
        $this->assertSame(['foo' => 'bar'], $result);

        $result = $load->data([Test1::class], [], true);
        $this->assertSame(['foo' => 'bar'], $result);
    }

    public function testCacheBlockType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Cache `Tests\\Cache\\Pieces\\Test2` must implements `Leevel\\Cache\\IBlock`.'
        );

        $container = new Container();
        $load = $this->createLoad($container);

        $load->data([Test2::class]);
    }

    /**
     * @api(
     *     title="data 载入缓存块数据支持参数",
     *     description="
     * `data` 方法支持传递一些参数到缓存块，可以生成不同的缓存数据。
     *
     * **例子 \Tests\Cache\Pieces\Test1**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Cache\Pieces\Test4::class)]}
     * ```
     *
     * 参数通过 `:` 冒号进行分割，冒号后边是自定义参数。
     * ",
     *     note="",
     * )
     */
    public function testWithParams(): void
    {
        $container = new Container();
        $load = $this->createLoad($container);

        $result = $load->data([Test4::class.':hello,world,foo,bar']);
        $this->assertSame(['hello', 'world', 'foo', 'bar'], $result);
    }

    public function testCacheNotFound(): void
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
