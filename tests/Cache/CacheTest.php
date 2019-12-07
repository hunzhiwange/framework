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
 * \Leevel\Cache\Helper::function cache(): \Leevel\Cache\Manager;
 * ```
 *
 * 使用容器 caches 服务
 *
 * ``` php
 * \App::make('caches')->set(string $name, $data, array $option = []): void;
 * \App::make('caches')->get(string $name, $defaults = false, array $option = []);
 * ```
 *
 * 依赖注入
 *
 * ``` php
 * class Demo
 * {
 *     private $cache;
 *
 *     public function __construct(\Leevel\Cache\Manager $cache)
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

    /**
     * @api(
     *     title="缓存基本使用",
     *     description="
     * ### 设置缓存
     *
     * ``` php
     * set(string $name, $data, array $option = []): void;
     * ```
     *
     * 缓存配置 `$option` 根据不同缓存驱动支持不同的一些配置。
     *
     * **file 驱动**
     *
     * |配置项|配置描述|
     * |:-|:-|
     * |expire|设置好缓存时间（小与等于 0 表示永不过期，单位时间为秒）|
     * |time_preset|缓存时间预置|
     * |serialize|是否使用 serialize 编码|
     * |path|缓存路径|
     *
     * **redis 驱动**
     *
     * |配置项|配置描述|
     * |:-|:-|
     * |expire|设置好缓存时间（小与等于 0 表示永不过期，单位时间为秒）|
     * |time_preset|缓存时间预置|
     * |serialize|是否使用 serialize 编码|
     *
     * ### 获取缓存
     *
     * ``` php
     * get(string $name, $defaults = false, array $option = []);
     * ```
     *
     * 缓存不存在或者过期返回 `false`，可以根据这个判断缓存是否可用。
     *
     * ### 删除缓存
     *
     * ``` php
     * delete(string $name): void;
     * ```
     *
     * 直接指定缓存 `key` 即可，无返回。
     * ",
     *     note="",
     * )
     */
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

    /**
     * @api(
     *     title="put 批量设置缓存",
     *     description="
     * 函数签名
     *
     * ``` php
     * put($keys, $value = null, array $option = []): void;
     * ```
     *
     * ::: tip
     * 缓存配置 `$option` 和 `set` 的用法一致。
     * :::
     * ",
     *     note="",
     * )
     */
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

    /**
     * @api(
     *     title="put 批量设置缓存支持配置",
     *     description="",
     *     note="",
     * )
     */
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

    /**
     * @api(
     *     title="remember 缓存存在读取否则重新设置",
     *     description="
     * 函数签名
     *
     * ``` php
     * remember(string $name, $data, array $option = []);
     * ```
     *
     * ::: tip
     * 缓存配置 `$option` 和 `set` 的用法一致。
     * :::
     * ",
     *     note="",
     * )
     */
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

    /**
     * @api(
     *     title="remember 缓存存在读取否则重新设置支持配置",
     *     description="",
     *     note="",
     * )
     */
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

    /**
     * @api(
     *     title="remember 缓存存在读取否则重新设置支持闭包用法",
     *     description="闭包的参数为缓存的 `key`，返回值为缓存的值。",
     *     note="",
     * )
     */
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

    /**
     * @api(
     *     title="缓存时间预置",
     *     description="
     * 不同场景下面的缓存可能支持不同的时间，我们可以在配置中预设时间而不是在使用时通过第三个参数传递 `expire` 过期时间，这种做法非常灵活。
     *
     * 缓存时间预设支持 `*` 通配符，可以灵活控制一类缓存时间。
     * ",
     *     note="",
     * )
     */
    public function testCacheTime(): void
    {
        $file = new File([
            'time_preset' => [
                'foo'         => 500,
                'bar'         => -10,
                'hello*world' => 10,
                'foo*bar'     => -10,
            ],
            'path' => __DIR__.'/cache',
        ]);

        $file->set('foo', 'bar');
        $file->set('bar', 'hello');
        $file->set('hello123456world', 'helloworld1');
        $file->set('hello789world', 'helloworld2');
        $file->set('foo123456bar', 'foobar1');
        $file->set('foo789bar', 'foobar2');
        $file->set('haha', 'what about others?');

        $this->assertSame('bar', $file->get('foo'));
        $this->assertFalse($file->get('bar'));
        $this->assertSame('helloworld1', $file->get('hello123456world'));
        $this->assertSame('helloworld2', $file->get('hello789world'));
        $this->assertFalse($file->get('foo123456bar'));
        $this->assertFalse($file->get('foo789bar'));
        $this->assertSame('what about others?', $file->get('haha'));

        $file->delete('foo');
        $file->delete('bar');
        $file->delete('hello123456world');
        $file->delete('hello789world');
        $file->delete('foo123456bar');
        $file->delete('foo789bar');
        $file->delete('haha');
    }
}
