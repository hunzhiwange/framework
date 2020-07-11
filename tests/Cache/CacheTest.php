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
use Leevel\Filesystem\Helper;
use Tests\TestCase;

/**
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
 * 使用容器 caches 服务
 *
 * ``` php
 * \App::make('caches')->set(string $name, $data, ?int $expire = null): void;
 * \App::make('caches')->get(string $name, $defaults = false, ?int $expire = null);
 * ```
 *
 * 依赖注入
 *
 * ``` php
 * class Demo
 * {
 *     private \Leevel\Cache\Manager $cache;
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
 * \Leevel\Cache\Proxy\Cache::set(string $name, $data, ?int $expire = null): void;
 * \Leevel\Cache\Proxy\Cache::get(string $name, $defaults = false, ?int $expire = null);
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
 * ",
 * )
 */
class CacheTest extends TestCase
{
    protected function tearDown(): void
    {
        $path = __DIR__.'/cache';
        if (is_dir($path)) {
            Helper::deleteDirectory($path);
        }
    }

    /**
     * @api(
     *     title="缓存基本使用",
     *     description="
     * ### 设置缓存
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Leevel\Cache\ICache::class, 'set', 'define')]}
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
     * |path|缓存路径|
     *
     * **redis 驱动**
     *
     * |配置项|配置描述|
     * |:-|:-|
     * |expire|设置好缓存时间（小与等于 0 表示永不过期，单位时间为秒）|
     * |time_preset|缓存时间预置|
     *
     * ### 获取缓存
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Leevel\Cache\ICache::class, 'get', 'define')]}
     * ```
     *
     * 缓存不存在或者过期返回 `false`，可以根据这个判断缓存是否可用。
     *
     * ### 删除缓存
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Leevel\Cache\ICache::class, 'delete', 'define')]}
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
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Leevel\Cache\ICache::class, 'put', 'define')]}
     * ```
     *
     * ::: tip
     * 缓存配置 `$expire` 和 `set` 的用法一致。
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
     *     title="set 值 false 不允许作为缓存值",
     *     description="
     * 因为 `false` 会作为判断缓存是否存在的一个依据，所以 `false` 不能够作为缓存，否则会引起缓存穿透。
     * ",
     *     note="",
     * )
     */
    public function testSetNotAllowedFalse(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Data `false` not allowed to avoid cache penetration.'
        );

        $cache = new File([
            'path' => __DIR__.'/cache',
        ]);

        $cache->set('hello', false);
    }

    /**
     * @api(
     *     title="put 批量设置缓存支持过期时间",
     *     description="",
     *     note="",
     * )
     */
    public function testPutWithExpire(): void
    {
        $cache = new File([
            'path' => __DIR__.'/cache',
        ]);

        $filePath = __DIR__.'/cache/hello.php';

        $cache->put('hello', 'world', 33);
        $cache->put(['hello2' => 'world', 'foo' => 'bar'], 22);

        $this->assertSame('world', $cache->get('hello'));
        $this->assertSame('world', $cache->get('hello2'));
        $this->assertSame('bar', $cache->get('foo'));
        $this->assertTrue(is_file($filePath));
        $this->assertStringContainsString('[33,', file_get_contents($filePath));

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
     * 缓存值为闭包返回，闭包的参数为缓存的 `key`。
     *
     * 函数签名
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Leevel\Cache\ICache::class, 'remember', 'define')]}
     * ```
     *
     * ::: tip
     * 缓存配置 `$expire` 和 `set` 的用法一致。
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
     *     title="remember 缓存存在读取否则重新设置支持过期时间",
     *     description="",
     *     note="",
     * )
     */
    public function testRememberWithExpire(): void
    {
        $cache = new File([
            'path' => __DIR__.'/cache',
        ]);

        $filePath = __DIR__.'/cache/hello.php';
        if (is_file($filePath)) {
            unlink($filePath);
        }

        $this->assertFalse(is_file($filePath));
        $this->assertSame('123456', $cache->remember('hello', function (string $key) {
            return '123456';
        }, 33));

        $this->assertTrue(is_file($filePath));
        $this->assertSame('123456', $cache->remember('hello', function (string $key) {
            return '123456';
        }, 4));
        $this->assertSame('123456', $cache->get('hello'));

        $cache->delete('hello');

        $this->assertFalse($cache->get('hello'));
        $this->assertFalse(is_file($filePath));
    }

    /**
     * @api(
     *     title="has 缓存是否存在",
     *     description="",
     *     note="",
     * )
     */
    public function testHas(): void
    {
        $cache = new File([
            'path' => __DIR__.'/cache',
        ]);
        $filePath = __DIR__.'/cache/has.php';

        $this->assertFalse($cache->has('has'));
        $cache->set('has', 'world');
        $this->assertTrue(is_file($filePath));
        $this->assertTrue($cache->has('has'));
    }

    /**
     * @api(
     *     title="increase 自增",
     *     description="",
     *     note="",
     * )
     */
    public function testIncrease(): void
    {
        $cache = new File([
            'path' => __DIR__.'/cache',
        ]);
        $filePath = __DIR__.'/cache/increase.php';

        $this->assertSame(1, $cache->increase('increase'));
        $this->assertTrue(is_file($filePath));
        $this->assertSame(101, $cache->increase('increase', 100));
    }

    /**
     * @api(
     *     title="decrease 自减",
     *     description="",
     *     note="",
     * )
     */
    public function testDecrease(): void
    {
        $cache = new File([
            'path' => __DIR__.'/cache',
        ]);
        $filePath = __DIR__.'/cache/decrease.php';

        $this->assertSame(-1, $cache->decrease('decrease'));
        $this->assertTrue(is_file($filePath));
        $this->assertSame(-101, $cache->decrease('decrease', 100));
    }

    /**
     * @api(
     *     title="ttl 获取缓存剩余时间",
     *     description="
     * 剩余时间存在 3 种情况。
     *
     *  * 不存在的 key:-2
     *  * key 存在，但没有设置剩余生存时间:-1
     *  * 有剩余生存时间的 key:剩余时间
     * ",
     *     note="",
     * )
     */
    public function testTtl(): void
    {
        $cache = new File([
            'path' => __DIR__.'/cache',
        ]);
        $filePath = __DIR__.'/cache/ttl.php';

        $this->assertFalse($cache->has('ttl'));
        $this->assertSame(-2, $cache->ttl('ttl'));
        $cache->set('ttl', 'world');
        $this->assertTrue(is_file($filePath));
        $this->assertSame(86400, $cache->ttl('ttl'));
        $cache->set('ttl', 'world', 1);
        $this->assertSame(1, $cache->ttl('ttl'));
        $cache->set('ttl', 'world', 0);
        $this->assertSame(-1, $cache->ttl('ttl'));
    }

    /**
     * @api(
     *     title="缓存时间预置",
     *     description="
     * 不同场景下面的缓存可能支持不同的时间，我们可以在配置中预设时间而不是在使用时通过第三个参数传递 `expire` 过期时间，这种做法非常灵活。
     *
     * 缓存时间预设支持 `*` 通配符，可以灵活控制一类缓存时间。
     * ",
     *     note="缓存时间预设小与等于 0 表示永不过期，单位时间为秒。",
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
        $this->assertSame('hello', $file->get('bar'));
        $this->assertSame('helloworld1', $file->get('hello123456world'));
        $this->assertSame('helloworld2', $file->get('hello789world'));
        $this->assertSame('foobar1', $file->get('foo123456bar'));
        $this->assertSame('foobar2', $file->get('foo789bar'));
        $this->assertSame('what about others?', $file->get('haha'));

        $file->delete('foo');
        $file->delete('bar');
        $file->delete('hello123456world');
        $file->delete('hello789world');
        $file->delete('foo123456bar');
        $file->delete('foo789bar');
        $file->delete('haha');
    }

    /**
     * @api(
     *     title="键值命名规范",
     *     description="
     * 缓存键值默认支持正则 `/^[A-Za-z0-9\-\_:.]+$/`，可以通过 `setKeyRegex` 修改。
     * ",
     *     note="",
     * )
     */
    public function testInvalidCacheKey(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cache key must be `/^[A-Za-z0-9\-\_:.]+$/`.');

        $cache = new File([
            'path' => __DIR__.'/cache',
        ]);
        $cache->set('hello+world', 1);
    }

    /**
     * @api(
     *     title="setKeyRegex 设置缓存键值正则",
     *     description="
     * 缓存键值默认支持正则 `/^[A-Za-z0-9\-\_:.]+$/`，可以通过 `setKeyRegex` 修改。
     * ",
     *     note="",
     * )
     */
    public function testSetKeyRegex(): void
    {
        $cache = new File([
            'path' => __DIR__.'/cache',
        ]);
        $cache->setKeyRegex('/^[a-z+]+$/');
        $cache->set('hello+world', 1);
        $this->assertSame(1, $cache->get('hello+world'));
    }
}
