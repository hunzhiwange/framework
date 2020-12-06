<?php

declare(strict_types=1);

namespace Tests\Cache;

use Leevel\Cache\Load;
use Leevel\Di\Container;
use Leevel\Filesystem\Helper;
use Tests\Cache\Pieces\Test1;
use Tests\Cache\Pieces\Test2;
use Tests\Cache\Pieces\Test4;
use Tests\Cache\Pieces\Test5;
use Tests\Cache\Pieces\Test6;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="缓存块载入",
 *     path="component/cache/load",
 *     zh-CN:description="
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
 * 缓存块需要实现 `\Leevel\Cache\IBlock` 接口，即可统一进行管理。
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
 * \Leevel\Cache\Proxy\Load::data(array $names, ?int $expire = null, bool $force = false): array;
 * \Leevel\Cache\Proxy\Load::refresh(array $names): void;
 * ```
 * ",
 * )
 */
class LoadTest extends TestCase
{
    protected function tearDown(): void
    {
        $path = __DIR__.'/Pieces/cacheLoad';
        if (is_dir($path)) {
            Helper::deleteDirectory($path);
        }
    }

    /**
     * @api(
     *     zh-CN:title="data 载入缓存块数据",
     *     zh-CN:description="
     * 通过 `data` 即可载入缓存块数据，缓存直接传递缓存块的类名字即可。
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Leevel\Cache\ILoad::class, 'data', 'define')]}
     * ```
     *
     * 配置 `$expire` 和缓存功能中的 `set` 的用法一致。
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $container = new Container();
        $load = $this->createLoad($container);

        $result = $load->data([Test1::class]);
        $this->assertSame(['foo' => 'bar'], $result);

        $this->assertSame(
            [Test1::class => ['foo' => 'bar']],
            $this->getTestProperty($load, 'cacheLoaded'),
        );

        $result = $load->data([Test1::class]);
        $this->assertSame(['foo' => 'bar'], $result);

        $load->refresh([Test1::class]);

        $this->assertSame(
            [],
            $this->getTestProperty($load, 'cacheLoaded'),
        );
    }

    /**
     * @api(
     *     zh-CN:title="refresh 刷新缓存块数据",
     *     zh-CN:description="
     * 通过 `refresh` 即可刷新缓存块数据，缓存直接传递缓存块的类名字即可。
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Leevel\Cache\ILoad::class, 'refresh', 'define')]}
     * ```
     *
     * 刷新缓存块本质是删除缓存块数据，下次请求自动生成。
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testRefresh(): void
    {
        $container = new Container();
        $load = $this->createLoad($container);

        $result = $load->data([Test1::class]);
        $this->assertSame(['foo' => 'bar'], $result);

        $this->assertSame(
            [Test1::class => ['foo' => 'bar']],
            $this->getTestProperty($load, 'cacheLoaded'),
        );

        $result = $load->data([Test1::class]);
        $this->assertSame(['foo' => 'bar'], $result);

        $file = __DIR__.'/Pieces/cacheLoad/test1.php';
        $this->assertTrue(is_file($file));

        $this->assertSame(
            [Test1::class => ['foo' => 'bar']],
            $this->getTestProperty($load, 'cacheLoaded'),
        );

        $load->refresh([Test1::class]);
        $this->assertFalse(is_file($file));

        $this->assertSame(
            [],
            $this->getTestProperty($load, 'cacheLoaded'),
        );
    }

    /**
     * @api(
     *     zh-CN:title="data 强制载入缓存块数据",
     *     zh-CN:description="`data` 方法支持强制获取缓存数据。",
     *     zh-CN:note="`$force` 参数强制从原始数据源获取缓存，并且会刷新缓存数据。",
     * )
     */
    public function testDataForce(): void
    {
        $container = new Container();
        $load = $this->createLoad($container);

        $result = $load->data([Test1::class]);
        $this->assertSame(['foo' => 'bar'], $result);

        $result = $load->data([Test1::class], null, true);
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
     *     zh-CN:title="data 载入缓存块数据支持参数",
     *     zh-CN:description="
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
     *     zh-CN:note="",
     * )
     */
    public function testWithParams(): void
    {
        $container = new Container();
        $load = $this->createLoad($container);

        $result = $load->data([Test4::class.':hello,world,foo,bar']);
        $this->assertSame(['hello', 'world', 'foo', 'bar'], $result);
    }

    /**
     * @api(
     *     zh-CN:title="clearCacheLoaded 清理已载入的缓存数据",
     *     zh-CN:description="
     * 如果缓存原始数据可以动态变化，比如在一个常驻脚本中，后台修改了系统配置，然后使用 `refresh` 更新了缓存。
     * 此时你需要在每次循环调用业务代码前清理掉已载入的缓存数据，而不是通过 `refresh` 清理原始缓存数据，那么这个方法将会变得非常有用。
     *
     * **例子 \Tests\Cache\Pieces\Test5**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Cache\Pieces\Test5::class)]}
     * ```
     *
     * 例子中的缓存原始数据变化，我们通过 `$GLOBALS['cache_data]` 来模拟实现。
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testClearCacheLoaded(): void
    {
        $container = new Container();
        $load = $this->createLoad($container);

        if (isset($GLOBALS['cache_data'])) {
            unset($GLOBALS['cache_data']);
        }

        $GLOBALS['cache_data'] = 'data1';
        $result = $load->data([Test5::class]);
        $this->assertSame(['data' => 'data1'], $result);

        $GLOBALS['cache_data'] = 'data2';
        $result = $load->data([Test5::class]);
        $this->assertSame(['data' => 'data1'], $result);

        // 清理原始缓存，模拟修改系统配置
        $newLoad = $this->createLoad($container);
        $newLoad->refresh([Test5::class]);

        $result = $load->data([Test5::class]);
        $this->assertSame(['data' => 'data1'], $result);

        $load->clearCacheLoaded([Test5::class]);
        $result = $load->data([Test5::class]);
        $this->assertSame(['data' => 'data2'], $result);

        // 清理原始缓存，模拟修改系统配置
        $newLoad = $this->createLoad($container);
        $newLoad->refresh([Test5::class]);
        $GLOBALS['cache_data'] = 'data3';
        $load->clearCacheLoaded();
        $result = $load->data([Test5::class]);
        $this->assertSame(['data' => 'data3'], $result);

        if (isset($GLOBALS['cache_data'])) {
            unset($GLOBALS['cache_data']);
        }
    }

    public function testCacheDataWasString(): void
    {
        $container = new Container();
        $load = $this->createLoad($container);

        $result = $load->data([Test6::class]);
        $this->assertSame('hello world for test6', $result);
    }

    public function testCacheNotFound(): void
    {
        $this->expectException(\ReflectionException::class);
        $this->expectExceptionMessage(
            'Class "Tests\\Cache\\Pieces\\TestNotFound" does not exist'
        );

        $container = new Container();
        $load = $this->createLoad($container);
        $load->data(['Tests\\Cache\\Pieces\\TestNotFound']);
    }

    protected function createLoad(Container $container): Load
    {
        return new Load($container);
    }
}
