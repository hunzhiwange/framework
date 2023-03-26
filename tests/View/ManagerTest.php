<?php

declare(strict_types=1);

namespace Tests\View;

use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Filesystem\Helper;
use Leevel\Kernel\App;
use Leevel\Option\Option;
use Leevel\View\Manager;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="View",
 *     path="component/view",
 *     zh-CN:description="
 * 视图统一由视图组件完成，通常我们使用代理 `\Leevel\View\Proxy\View` 类进行静态调用。
 *
 * 内置支持的视图驱动类型包括 html、phpui，未来可能增加其他驱动。
 *
 * ## 使用方式
 *
 * 使用容器 view 服务
 *
 * ``` php
 * \App::make('views')->setVar(array|string $name, mixed $value = null): void;
 * ```
 *
 * 依赖注入
 *
 * ``` php
 * class Demo
 * {
 *     private \Leevel\View\Manager $view;
 *
 *     public function __construct(\Leevel\View\Manager $view)
 *     {
 *         $this->view = $view;
 *     }
 * }
 * ```
 *
 * 使用静态代理
 *
 * ``` php
 * \Leevel\Router\Proxy\View::setVar(array|string $name, mixed $value = null): void;
 * ```
 *
 * ## view 配置
 *
 * 系统的 view 配置位于应用下面的 `option/view.php` 文件。
 *
 * 可以定义多个视图连接，并且支持切换，每一个连接支持驱动设置。
 *
 * ``` php
 * {[file_get_contents('option/view.php')]}
 * ```
 *
 * 视图参数根据不同的连接会有所区别，通用的 view 参数如下：
 *
 * |配置项|配置描述|
 * |:-|:-|
 * |fail|错误模板|
 * |success|成功模板|
 * ",
 * note="",
 * )
 *
 * @internal
 */
final class ManagerTest extends TestCase
{
    protected function tearDown(): void
    {
        if (is_dir($cacheDirPath = __DIR__.'/cache_app')) {
            Helper::deleteDirectory($cacheDirPath);
        }
    }

    /**
     * @api(
     *     zh-CN:title="视图基本使用",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $manager = $this->createManager();
        $manager->setVar('foo', 'bar');
        $result = $manager->display('html_test');
        static::assertSame('hello html,bar.', $result);
    }

    /**
     * @api(
     *     zh-CN:title="PHP 自身作为模板",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testPhpUi(): void
    {
        $manager = $this->createManager('phpui');

        $manager->setVar('hello', 'world');
        static::assertSame('world', $manager->getVar('hello'));
        static::assertSame('world', $manager->connect('phpui')->getVar('hello'));
    }

    /**
     * @api(
     *     zh-CN:title="getVar 获取变量值",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testGetVar(): void
    {
        $manager = $this->createManager();

        $manager->setVar('hello', 'world');
        static::assertSame('world', $manager->getVar('hello'));
        static::assertNull($manager->getVar('hello2'));
    }

    /**
     * @api(
     *     zh-CN:title="getVar 获取所有变量值",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testGetVarAll(): void
    {
        $manager = $this->createManager();

        $manager->setVar('hello', 'world');
        static::assertSame(['hello' => 'world'], $manager->getVar());
    }

    /**
     * @api(
     *     zh-CN:title="deleteVar 删除变量值",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testDeleteVar(): void
    {
        $manager = $this->createManager('phpui');

        $manager->setVar('hello', 'world');
        static::assertSame('world', $manager->getVar('hello'));
        static::assertSame('world', $manager->connect('phpui')->getVar('hello'));

        $manager->deleteVar(['hello']);
        static::assertNull($manager->getVar('hello'));
        static::assertNull($manager->connect('phpui')->getVar('hello'));
    }

    /**
     * @api(
     *     zh-CN:title="clearVar 清空变量值",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testClearVar(): void
    {
        $manager = $this->createManager();

        $manager->setVar('foo', 'bar');
        static::assertSame('bar', $manager->getVar('foo'));
        static::assertSame('bar', $manager->connect('html')->getVar('foo'));

        $manager->clearVar();
        static::assertNull($manager->getVar('foo'));
        static::assertNull($manager->connect('html')->getVar('foo'));
    }

    /**
     * @api(
     *     zh-CN:title="display 加载视图文件",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testDisplay(): void
    {
        $manager = $this->createManager('phpui');

        $manager->setVar('foo', 'bar');
        static::assertSame(
            'Hi here! bar',
            $manager->display(__DIR__.'/assert/hello.php')
        );
    }

    public function testDisplayReconnect(): void
    {
        $manager = $this->createManager('phpui');

        $manager->setVar('foo', 'bar');
        static::assertSame(
            'Hi here! bar',
            $manager->display(__DIR__.'/assert/hello.php')
        );

        $connect = $manager->reconnect('phpui');
        $connect->setVar('foo', 'bar');
        static::assertSame(
            'Hi here! bar',
            $connect->display(__DIR__.'/assert/hello.php')
        );
    }

    protected function createManager(string $connect = 'html'): Manager
    {
        $app = new ExtendApp($container = new Container(), '');
        $container->instance('app', $app);

        $manager = new Manager($container);

        $this->assertInstanceof(IContainer::class, $manager->container());
        $this->assertInstanceof(Container::class, $manager->container());

        static::assertSame(__DIR__.'/assert', $app->themesPath());
        static::assertSame(__DIR__.'/cache_theme', $app->storagePath('theme'));

        $option = new Option([
            'view' => [
                'default' => $connect,
                'action_fail' => 'public/fail',
                'action_success' => 'public/success',
                'connect' => [
                    'html' => [
                        'driver' => 'html',
                        'suffix' => '.html',
                    ],
                    'phpui' => [
                        'driver' => 'phpui',
                        'suffix' => '.php',
                    ],
                ],
            ],
        ]);
        $container->singleton('option', $option);

        $request = new ExtendRequest();
        $container->singleton('request', $request);

        return $manager;
    }
}

class ExtendApp extends App
{
    public function development(): bool
    {
        return true;
    }

    public function themesPath(string $path = ''): string
    {
        return __DIR__.'/assert';
    }

    public function storagePath(string $path = ''): string
    {
        return __DIR__.'/cache_'.$path;
    }
}

class ExtendRequest
{
}
