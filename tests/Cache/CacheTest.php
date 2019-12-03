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

use Leevel\Cache\File;
use Leevel\Filesystem\Fso;
use Tests\TestCase;

/**
 * cache test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.29
 *
 * @version 1.0
 *
 * @api(
 *     title="缓存",
 *     path="component/cache",
 *     description="
 * QueryPHP 为系统提供了灵活的缓存功能，提供了多种缓存驱动。
 *
 * 内置支持的缓存类型包括 file、redis，未来可能增加其他驱动。
 *
 * ## 使用方式
 *
 * 使用助手函数
 *
 * ``` php
 * \Leevel\Cache\Helper::cache_get(string $key, $defaults = null, array $option = []);
 * \Leevel\Cache\Helper::cache_get(string $key, $defaults = null, array $option = []);
 * \Leevel\Cache\Helper::function cache(): \Leevel\Cache\ICache;
 * ```
 *
 * 使用容器 cache 服务
 *
 * ``` php
 * \App::make('cache')->set(string $name, $data, array $option = []): void;
 * \App::make('cache')->get(string $name, $defaults = false, array $option = []);
 * ```
 *
 * 依赖注入
 *
 * ``` php
 * class Demo
 * {
 *     private $cache;
 *
 *     public function __construct(\Leevel\Cache\ICache $cache)
 *     {
 *         $this->cache = $cache;
 *     }
 * }
 * ```
 *
 * 使用静态代理
 *
 * ``` php
 * \Leevel\Cache\Proxy\Cache::set(string $name, $data, array $option = []): void;
 * \Leevel\Cache\Proxy\Cache::get(string $name, $defaults = false, array $option = []);
 * ```
 *
 * ## 缓存配置
 *
 * 系统的缓存配置位于应用下面的 `option/cache.php` 文件。
 *
 * 可以定义多个缓存连接，并且支持切换，每一个连接支持驱动设置。
 *
 * ``` php
 * {[file_get_contents('option/cache.php')]}
 * ```
 *
 * 缓存参数根据不同的连接会有所区别，通用的缓存参数如下：
 *
 * |配置项|配置描述|
 * |:-|:-|
 * |expire|设置好缓存时间（小与等于 0 表示永不过期，单位时间为秒）|
 * |time_preset|缓存时间预置|
 * |serialize|是否使用 serialize 编码|
 * ",
 * )
 */
class CacheTest extends TestCase
{
    protected function tearDown(): void
    {
        $path = __DIR__.'/cache';

        if (is_dir($path)) {
            Fso::deleteDirectory($path, true);
        }
    }

    public function testBaseUse(): void
    {
        $filePath = __DIR__.'/cache/hello.php';

        $cache = new File([
            'path' => __DIR__.'/cache',
        ]);

        $cache->set('hello', 'world');

        $this->assertTrue(is_file($filePath));

        $this->assertSame('world', $cache->get('hello'));

        $cache->delete('hello');

        $this->assertFalse(is_file($filePath));

        $this->assertFalse($cache->get('hello'));
    }

    public function testPut(): void
    {
        $cache = new File([
            'path' => __DIR__.'/cache',
        ]);

        $cache->put('hello', 'world');

        $cache->put(['hello2' => 'world', 'foo' => 'bar']);

        $this->assertSame('world', $cache->get('hello'));
        $this->assertSame('world', $cache->get('hello2'));
        $this->assertSame('bar', $cache->get('foo'));

        $cache->delete('hello');
        $cache->delete('hello2');
        $cache->delete('foo');

        $this->assertFalse($cache->get('hello'));
        $this->assertFalse($cache->get('hello2'));
        $this->assertFalse($cache->get('foo'));
    }

    public function testPutWithOption(): void
    {
        $cache = new File([
            'path' => __DIR__.'/cache',
        ]);

        $filePath = __DIR__.'/cache/hello.php';

        $cache->put('hello', 'world', [
            'serialize' => true,
        ]);

        $cache->put(['hello2' => 'world', 'foo' => 'bar'], [
            'serialize' => true,
        ]);

        $this->assertSame('world', $cache->get('hello'));
        $this->assertSame('world', $cache->get('hello2'));
        $this->assertSame('bar', $cache->get('foo'));

        $this->assertTrue(is_file($filePath));
        $this->assertStringContainsString('s:5:"world"', file_get_contents($filePath));

        $cache->delete('hello');
        $cache->delete('hello2');
        $cache->delete('foo');

        $this->assertFalse($cache->get('hello'));
        $this->assertFalse($cache->get('hello2'));
        $this->assertFalse($cache->get('foo'));
    }

    public function testRemember(): void
    {
        $cache = new File([
            'path' => __DIR__.'/cache',
        ]);

        $filePath = __DIR__.'/cache/hello.php';

        $this->assertFalse(is_file($filePath));

        $this->assertSame('123456', $cache->remember('hello', '123456'));

        $this->assertTrue(is_file($filePath));

        $this->assertSame('123456', $cache->remember('hello', '123456'));

        $this->assertSame('123456', $cache->get('hello'));

        $cache->delete('hello');

        $this->assertFalse($cache->get('hello'));
        $this->assertFalse(is_file($filePath));
    }

    public function testRememberWithOption(): void
    {
        $cache = new File([
            'path' => __DIR__.'/cache',
        ]);

        $filePath = __DIR__.'/cache/hello.php';

        if (is_file($filePath)) {
            unlink($filePath);
        }

        $this->assertFalse(is_file($filePath));

        $this->assertSame('123456', $cache->remember('hello', '123456', [
            'serialize' => true,
        ]));

        $this->assertTrue(is_file($filePath));

        $this->assertSame('123456', $cache->remember('hello', '123456', [
            'serialize' => true,
        ]));

        $this->assertSame('123456', $cache->get('hello'));

        $cache->delete('hello');

        $this->assertFalse($cache->get('hello'));
        $this->assertFalse(is_file($filePath));
    }

    public function testRememberWithClosure(): void
    {
        $cache = new File([
            'path' => __DIR__.'/cache',
        ]);

        $filePath = __DIR__.'/cache/hello.php';

        $this->assertFalse(is_file($filePath));

        $this->assertSame(['hello' => 'world'], $cache->remember('hello', function (string $key) {
            return [$key => 'world'];
        }));

        $this->assertTrue(is_file($filePath));

        $this->assertSame(['hello' => 'world'], $cache->get('hello'));

        $cache->delete('hello');

        $this->assertFalse($cache->get('hello'));
        $this->assertFalse(is_file($filePath));
    }
}
