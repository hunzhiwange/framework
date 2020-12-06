<?php

declare(strict_types=1);

namespace Tests\Kernel\Bootstrap;

use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Filesystem\Helper;
use Leevel\Kernel\App as Apps;
use Leevel\Kernel\Bootstrap\LoadOption;
use Leevel\Kernel\IApp;
use Leevel\Option\Option;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="初始化载入配置",
 *     path="architecture/kernel/bootstrap/loadoption",
 *     zh-CN:description="
 * QueryPHP 在内核执行过程中会执行初始化，分为 4 个步骤，载入配置、载入语言包、注册异常运行时和遍历服务提供者注册服务。
 *
 * 内核初始化，包括 `\Leevel\Kernel\IKernel::bootstrap` 和 `\Leevel\Kernel\IKernelConsole::bootstrap` 均会执行上述 4 个步骤。
 * ",
 *     zh-CN:note="",
 * )
 */
class LoadOptionTest extends TestCase
{
    protected function setUp(): void
    {
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        Container::singletons()->clear();

        $appPath = __DIR__.'/app';
        $runtimePath = $appPath.'/bootstrap';

        if (is_dir($runtimePath)) {
            Helper::deleteDirectory($runtimePath);
        }

        if (getenv('RUNTIME_ENVIRONMENT')) {
            putenv('RUNTIME_ENVIRONMENT=');
        }
    }

    /**
     * @api(
     *     zh-CN:title="基本使用方法",
     *     zh-CN:description="
     * **fixture 定义**
     *
     * **环境变量 tests/Kernel/Bootstrap/app/.env**
     *
     * ``` php
     * {[file_get_contents('vendor/hunzhiwange/framework/tests/Kernel/Bootstrap/app/.env')]}
     * ```
     *
     * **配置文件 tests/Kernel/Bootstrap/app/option/app.php**
     *
     * ``` php
     * {[file_get_contents('vendor/hunzhiwange/framework/tests/Kernel/Bootstrap/app/option/app.php')]}
     * ```
     *
     * **配置文件 tests/Kernel/Bootstrap/app/option/demo.php**
     *
     * ``` php
     * {[file_get_contents('vendor/hunzhiwange/framework/tests/Kernel/Bootstrap/app/option/demo.php')]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $bootstrap = new LoadOption1();

        $container = Container::singletons();
        $app = new App3($container, $appPath = __DIR__.'/app');

        $this->assertInstanceof(IContainer::class, $container);
        $this->assertInstanceof(Container::class, $container);
        $this->assertInstanceof(IApp::class, $app);
        $this->assertInstanceof(Apps::class, $app);

        $this->assertSame($appPath.'/bootstrap/option.php', $app->optionCachedPath());
        $this->assertFalse($app->isCachedOption());
        $this->assertSame($appPath.'/option', $app->optionPath());

        $this->assertNull($bootstrap->handle($app));

        $option = $container->make('option');

        $this->assertSame('development', $option->get('environment'));
        $this->assertSame('bar', $option->get('demo\\foo'));
    }

    /**
     * @api(
     *     zh-CN:title="RUNTIME_ENVIRONMENT 载入自定义环境变量文件",
     *     zh-CN:description="
     * 设置 `RUNTIME_ENVIRONMENT` 环境变量可以载入自定义环境变量文件。
     *
     * **fixture 定义**
     *
     * **环境变量 tests/Kernel/Bootstrap/app/.fooenv**
     *
     * ``` php
     * {[file_get_contents('vendor/hunzhiwange/framework/tests/Kernel/Bootstrap/app/.fooenv')]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testWithRuntimeEnv(): void
    {
        putenv('RUNTIME_ENVIRONMENT=fooenv');

        $bootstrap = new LoadOption1();

        $container = Container::singletons();
        $app = new App3($container, $appPath = __DIR__.'/app');

        $this->assertInstanceof(IContainer::class, $container);
        $this->assertInstanceof(Container::class, $container);
        $this->assertInstanceof(IApp::class, $app);
        $this->assertInstanceof(Apps::class, $app);

        $this->assertSame($appPath.'/bootstrap/fooenv.php', $app->optionCachedPath());
        $this->assertFalse($app->isCachedOption());
        $this->assertSame($appPath.'/option', $app->optionPath());

        $this->assertNull($bootstrap->handle($app));

        $option = $container->make('option');

        $this->assertSame('testing', $option->get('environment'));
        $this->assertSame('bar', $option->get('demo\\foo'));
    }

    public function testWithRuntimeEnvNotFound(): void
    {
        $appPath = __DIR__.'/app';

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            sprintf('Env file `%s` was not found.', $appPath.'/.notfoundenv')
        );

        putenv('RUNTIME_ENVIRONMENT=notfoundenv');

        $bootstrap = new LoadOption1();

        $container = Container::singletons();
        $app = new App3($container, $appPath);

        $this->assertInstanceof(IContainer::class, $container);
        $this->assertInstanceof(Container::class, $container);
        $this->assertInstanceof(IApp::class, $app);
        $this->assertInstanceof(Apps::class, $app);

        $this->assertSame($appPath.'/bootstrap/notfoundenv.php', $app->optionCachedPath());
        $this->assertFalse($app->isCachedOption());
        $this->assertSame($appPath.'/option', $app->optionPath());

        $bootstrap->handle($app);
    }

    /**
     * @api(
     *     zh-CN:title="配置支持缓存",
     *     zh-CN:description="
     * 配置文件支持缓存，通过缓存可以降低开销提高性能，适合生产环境。
     *
     * **fixture 定义**
     *
     * **配置缓存文件 tests/Kernel/Bootstrap/app/assert/option.php**
     *
     * ``` php
     * {[file_get_contents('vendor/hunzhiwange/framework/tests/Kernel/Bootstrap/app/assert/option.php')]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testLoadCached(): void
    {
        $bootstrap = new LoadOption1();

        $container = Container::singletons();
        $app = new App3($container, $appPath = __DIR__.'/app');

        $this->assertInstanceof(IContainer::class, $container);
        $this->assertInstanceof(Container::class, $container);
        $this->assertInstanceof(IApp::class, $app);
        $this->assertInstanceof(Apps::class, $app);

        $this->assertSame($appPath.'/bootstrap/option.php', $app->optionCachedPath());
        $this->assertFalse($app->isCachedOption());
        $this->assertSame($appPath.'/option', $app->optionPath());

        mkdir($appPath.'/bootstrap', 0777, true);
        file_put_contents($appPath.'/bootstrap/option.php', file_get_contents($appPath.'/assert/option.php'));

        $this->assertTrue($app->isCachedOption());

        $this->assertNull($bootstrap->handle($app));

        $option = $container->make('option');

        $this->assertSame('development', $option->get('environment'));
        $this->assertSame('bar', $option->get('demo\\foo'));
        $this->assertNull($option->get(':env.foo'));
        $this->assertTrue($option->get(':env.debug'));
    }
}

class App3 extends Apps
{
    protected function registerBaseProvider(): void
    {
    }
}

class LoadOption1 extends LoadOption
{
    protected function initialization(Option $option): void
    {
    }
}
