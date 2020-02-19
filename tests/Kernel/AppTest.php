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

namespace Tests\Kernel;

use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Di\Provider;
use Leevel\Filesystem\Helper;
use Leevel\Http\Request;
use Leevel\Kernel\App as Apps;
use Leevel\Kernel\IApp;
use Leevel\Option\IOption;
use Tests\TestCase;

/**
 * @api(
 *     title="应用",
 *     path="architecture/kernel/app",
 *     description="应用是整个系统非常核心的一部分，定义了应用的骨架。",
 *     note="
 * 应用设计为可替代，只需要实现 `\Leevel\Kernel\IApp` 即可，然后在入口文件替换即可。
 * ",
 * )
 */
class AppTest extends TestCase
{
    protected function setUp(): void
    {
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        Container::singletons()->clear();
    }

    /**
     * @api(
     *     title="基本使用",
     *     description="",
     *     note="",
     * )
     */
    public function testBaseUse(): void
    {
        $appPath = __DIR__.'/app';
        $app = $this->createApp();
        $container = $app->container();

        $this->assertInstanceof(IContainer::class, $container);
        $this->assertInstanceof(Container::class, $container);
        $this->assertInstanceof(IApp::class, $app);
        $this->assertInstanceof(Apps::class, $app);

        $this->assertSame($appPath, $app->path());
        $this->assertSame($appPath.'/foobar', $app->path('foobar'));
    }

    /**
     * @api(
     *     title="version 获取程序版本",
     *     description="",
     *     note="",
     * )
     */
    public function testVersion(): void
    {
        $app = $this->createApp();

        $this->assertSame(App::VERSION, $app->version());
    }

    public function testRunWithExtension(): void
    {
        $app = $this->createApp();

        if (extension_loaded('leevel')) {
            $this->assertTrue($app->runWithExtension());
        } else {
            $this->assertFalse($app->runWithExtension());
        }
    }

    /**
     * @api(
     *     title="isConsole 是否为 PHP 运行模式命令行",
     *     description="",
     *     note="",
     * )
     */
    public function testIsConsole(): void
    {
        $app = $this->createApp();

        $this->assertTrue($app->isConsole());
    }

    public function testIsConsole2(): void
    {
        $app = $this->createApp();

        $request = $this->createMock(Request::class);

        $request->method('isConsole')->willReturn(true);
        $this->assertTrue($request->isConsole());

        $app->container()->singleton('request', function () use ($request) {
            return $request;
        });

        $this->assertTrue($app->isConsole());
    }

    public function testIsConsole3(): void
    {
        $app = $this->createApp();
        $container = $app->container();

        $request = $this->createMock(Request::class);
        $request->method('isConsole')->willReturn(false);
        $this->assertFalse($request->isConsole());
        $container->singleton('request', function () use ($request) {
            return $request;
        });

        $this->assertFalse($app->isConsole());
    }

    /**
     * @api(
     *     title="setPath 设置基础路径",
     *     description="",
     *     note="",
     * )
     */
    public function testSetPath(): void
    {
        $app = $this->createApp();

        $app->setPath(__DIR__.'/foo');

        $this->assertSame(__DIR__.'/foo', $app->path());
    }

    /**
     * @api(
     *     title="appPath 获取应用路径",
     *     description="",
     *     note="",
     * )
     */
    public function testAppPath(): void
    {
        $appPath = __DIR__.'/app';
        $app = $this->createApp();
        $container = $app->container();

        $request = $this->createMock(Request::class);
        $container->singleton('request', function () use ($request) {
            return $request;
        });

        $container->instance('app_name', 'Blog');
        $this->assertEquals('Blog', $container->make('app_name'));

        $this->assertSame($appPath.'/application', $app->appPath());
        $this->assertSame($appPath.'/application', $app->appPath(false));
        $this->assertSame($appPath.'/application', $app->appPath(''));
        $this->assertSame($appPath.'/application/blog', $app->appPath(true));
        $this->assertSame($appPath.'/application/foo', $app->appPath('foo'));
        $this->assertSame($appPath.'/application/bar', $app->appPath('bar'));
        $this->assertSame($appPath.'/application/foo/bar', $app->appPath(false, 'foo/bar'));
        $this->assertSame($appPath.'/application/foo/bar', $app->appPath('', 'foo/bar'));
        $this->assertSame($appPath.'/application/blog/foo/bar', $app->appPath(true, 'foo/bar'));
        $this->assertSame($appPath.'/application/foo/foo/bar', $app->appPath('foo', 'foo/bar'));
        $this->assertSame($appPath.'/application/bar/foo/bar', $app->appPath('bar', 'foo/bar'));
    }

    public function testAppPath2(): void
    {
        $app = $this->createApp();
        $appPath = __DIR__.'/app';
        $container = $app->container();

        $request = $this->createMock(Request::class);
        $container->singleton('request', function () use ($request) {
            return $request;
        });

        $container->instance('app_name', '');
        $this->assertEquals('', $container->make('app_name'));
        $this->assertSame($appPath.'/application/app', $app->appPath(true));
    }

    /**
     * @api(
     *     title="setAppPath 设置应用路径",
     *     description="",
     *     note="",
     * )
     */
    public function testSetAppPath(): void
    {
        $appPath = __DIR__.'/app';
        $app = $this->createApp();
        $container = $app->container();

        $request = $this->createMock(Request::class);
        $container->singleton('request', function () use ($request) {
            return $request;
        });

        $container->instance('app_name', 'Blog');
        $this->assertEquals('Blog', $container->make('app_name'));
        $this->assertSame($appPath.'/application/blog', $app->appPath(true));

        $app->setAppPath(__DIR__.'/app/foo');
        $this->assertSame($appPath.'/foo/blog', $app->appPath(true));
    }

    /**
     * @api(
     *     title="themePath 获取应用主题目录",
     *     description="",
     *     note="",
     * )
     */
    public function testPathTheme(): void
    {
        $app = $this->createApp();
        $appPath = __DIR__.'/app';

        $this->assertSame($appPath.'/application/ui/theme', $app->themePath());
        $this->assertSame($appPath.'/application/blog/ui/theme', $app->themePath('blog'));
    }

    /**
     * @api(
     *     title="commonPath 获取公共路径",
     *     description="",
     *     note="",
     * )
     */
    public function testCommonPath(): void
    {
        $app = $this->createApp();
        $appPath = __DIR__.'/app';

        $this->assertSame($appPath.'/common', $app->commonPath());
        $this->assertSame($appPath.'/common/foobar', $app->commonPath('foobar'));
    }

    /**
     * @api(
     *     title="setCommonPath 设置公共路径",
     *     description="",
     *     note="",
     * )
     */
    public function testSetCommonPath(): void
    {
        $app = $this->createApp();
        $appPath = __DIR__.'/app';

        $this->assertSame($appPath.'/common', $app->commonPath());
        $this->assertSame($appPath.'/common/foobar', $app->commonPath('foobar'));

        $app->setCommonPath(__DIR__.'/app/commonFoo');

        $this->assertSame($appPath.'/commonFoo', $app->commonPath());
        $this->assertSame($appPath.'/commonFoo/foobar', $app->commonPath('foobar'));
    }

    /**
     * @api(
     *     title="runtimePath 获取运行路径",
     *     description="",
     *     note="",
     * )
     */
    public function testRuntimePath(): void
    {
        $app = $this->createApp();
        $appPath = __DIR__.'/app';

        $this->assertSame($appPath.'/runtime', $app->runtimePath());
        $this->assertSame($appPath.'/runtime/foobar', $app->runtimePath('foobar'));
    }

    /**
     * @api(
     *     title="setRuntimePath 设置运行时路径",
     *     description="",
     *     note="",
     * )
     */
    public function testSetRuntimePath(): void
    {
        $app = $this->createApp();
        $appPath = __DIR__.'/app';

        $this->assertSame($appPath.'/runtime', $app->runtimePath());
        $this->assertSame($appPath.'/runtime/foobar', $app->runtimePath('foobar'));

        $app->setRuntimePath(__DIR__.'/app/runtimeFoo');

        $this->assertSame($appPath.'/runtimeFoo', $app->runtimePath());
        $this->assertSame($appPath.'/runtimeFoo/foobar', $app->runtimePath('foobar'));
    }

    /**
     * @api(
     *     title="version 获取附件存储路径",
     *     description="",
     *     note="",
     * )
     */
    public function testStoragePath(): void
    {
        $app = $this->createApp();
        $appPath = __DIR__.'/app';

        $this->assertSame($appPath.'/storage', $app->storagePath());
        $this->assertSame($appPath.'/storage/foobar', $app->storagePath('foobar'));
    }

    /**
     * @api(
     *     title="setStoragePath 设置附件存储路径",
     *     description="",
     *     note="",
     * )
     */
    public function testSetStoragePath(): void
    {
        $app = $this->createApp();
        $appPath = __DIR__.'/app';

        $this->assertSame($appPath.'/storage', $app->storagePath());
        $this->assertSame($appPath.'/storage/foobar', $app->storagePath('foobar'));

        $app->setStoragePath(__DIR__.'/app/storageFoo');

        $this->assertSame($appPath.'/storageFoo', $app->storagePath());
        $this->assertSame($appPath.'/storageFoo/foobar', $app->storagePath('foobar'));
    }

    /**
     * @api(
     *     title="optionPath 获取配置路径",
     *     description="",
     *     note="",
     * )
     */
    public function testOptionPath(): void
    {
        $app = $this->createApp();
        $appPath = __DIR__.'/app';

        $this->assertSame($appPath.'/option', $app->optionPath());
        $this->assertSame($appPath.'/option/foobar', $app->optionPath('foobar'));
    }

    /**
     * @api(
     *     title="setOptionPath 设置配置路径",
     *     description="",
     *     note="",
     * )
     */
    public function testSetOptionPath(): void
    {
        $app = $this->createApp();
        $appPath = __DIR__.'/app';

        $this->assertSame($appPath.'/option', $app->optionPath());
        $this->assertSame($appPath.'/option/foobar', $app->optionPath('foobar'));

        $app->setOptionPath(__DIR__.'/app/optionFoo');

        $this->assertSame($appPath.'/optionFoo', $app->optionPath());
        $this->assertSame($appPath.'/optionFoo/foobar', $app->optionPath('foobar'));
    }

    /**
     * @api(
     *     title="i18nPath 获取语言包路径",
     *     description="",
     *     note="",
     * )
     */
    public function testI18nPath(): void
    {
        $app = $this->createApp();
        $appPath = __DIR__.'/app';

        $this->assertSame($appPath.'/i18n', $app->i18nPath());
        $this->assertSame($appPath.'/i18n/foobar', $app->i18nPath('foobar'));
    }

    /**
     * @api(
     *     title="setI18nPath 设置语言包路径",
     *     description="",
     *     note="",
     * )
     */
    public function testSetI18nPath(): void
    {
        $app = $this->createApp();
        $appPath = __DIR__.'/app';

        $this->assertSame($appPath.'/i18n', $app->i18nPath());
        $this->assertSame($appPath.'/i18n/foobar', $app->i18nPath('foobar'));

        $app->setI18nPath(__DIR__.'/app/i18nFoo');

        $this->assertSame($appPath.'/i18nFoo', $app->i18nPath());
        $this->assertSame($appPath.'/i18nFoo/foobar', $app->i18nPath('foobar'));
    }

    /**
     * @api(
     *     title="envPath 获取环境变量路径",
     *     description="",
     *     note="",
     * )
     */
    public function testEnvPath(): void
    {
        $app = $this->createApp();
        $appPath = __DIR__.'/app';

        $this->assertSame($appPath, $app->envPath());
    }

    /**
     * @api(
     *     title="setEnvPath 设置环境变量路径",
     *     description="",
     *     note="",
     * )
     */
    public function testSetEnvPath(): void
    {
        $app = $this->createApp();
        $appPath = __DIR__.'/app';

        $this->assertSame($appPath, $app->envPath());

        $app->setEnvPath(__DIR__.'/appFoo');

        $this->assertSame(__DIR__.'/appFoo', $app->envPath());
    }

    /**
     * @api(
     *     title="envFile 获取环境变量文件",
     *     description="",
     *     note="",
     * )
     */
    public function testEnvFile(): void
    {
        $app = $this->createApp();

        $this->assertSame('.env', $app->envFile());
    }

    /**
     * @api(
     *     title="setEnvFile 设置环境变量文件",
     *     description="",
     *     note="",
     * )
     */
    public function testSetEnvFile(): void
    {
        $app = $this->createApp();

        $this->assertSame('.env', $app->envFile());

        $app->setEnvFile('.envfoo');

        $this->assertSame('.envfoo', $app->envFile());
    }

    /**
     * @api(
     *     title="fullEnvPath 获取环境变量完整路径",
     *     description="",
     *     note="",
     * )
     */
    public function testFullEnvPath(): void
    {
        $app = $this->createApp();
        $appPath = __DIR__.'/app';

        $this->assertSame($appPath.'/.env', $app->fullEnvPath());

        $app->setEnvPath(__DIR__.'/appFoo');

        $this->assertSame(__DIR__.'/appFoo/.env', $app->fullEnvPath());

        $app->setEnvFile('.envfoo');

        $this->assertSame(__DIR__.'/appFoo/.envfoo', $app->fullEnvPath());
    }

    /**
     * @api(
     *     title="i18nCachedPath 获取语言包缓存路径",
     *     description="",
     *     note="",
     * )
     */
    public function testSetI18nCachePath(): void
    {
        $app = $this->createApp();
        $this->assertSame(__DIR__.'/app/bootstrap/i18n/zh-CN.php', $app->i18nCachedPath('zh-CN'));
        $app->setI18nCachedPath(__DIR__.'/hello');
        $this->assertSame(__DIR__.'/hello/zh-CN.php', $app->i18nCachedPath('zh-CN'));
    }

    /**
     * @api(
     *     title="i18nCachedPath 获取语言包缓存路径",
     *     description="",
     *     note="",
     * )
     */
    public function testI18nCachedPath(): void
    {
        $app = $this->createApp();
        $appPath = __DIR__.'/app';

        $this->assertSame($appPath.'/bootstrap/i18n/zh-CN.php', $app->i18nCachedPath('zh-CN'));
        $this->assertSame($appPath.'/bootstrap/i18n/zh-TW.php', $app->i18nCachedPath('zh-TW'));
        $this->assertSame($appPath.'/bootstrap/i18n/en-US.php', $app->i18nCachedPath('en-US'));
    }

    /**
     * @api(
     *     title="isCachedI18n 是否存在语言包缓存",
     *     description="",
     *     note="",
     * )
     */
    public function testIsCachedI18n(): void
    {
        $app = $this->createApp();
        $appPath = __DIR__.'/app';

        $this->assertFalse($app->isCachedI18n('zh-CN'));

        mkdir($appPath.'/bootstrap/i18n', 0777, true);

        file_put_contents($appPath.'/bootstrap/i18n/zh-CN.php', 'foo');

        $this->assertTrue($app->isCachedI18n('zh-CN'));

        Helper::deleteDirectory($appPath, true);
    }

    /**
     * @api(
     *     title="setOptionCachedPath 设置配置缓存路径",
     *     description="",
     *     note="",
     * )
     */
    public function testSetOptionCachePath(): void
    {
        $app = $this->createApp();
        $this->assertSame(__DIR__.'/app/bootstrap/option.php', $app->optionCachedPath());
        $app->setOptionCachedPath(__DIR__.'/hello');
        $this->assertSame(__DIR__.'/hello/option.php', $app->optionCachedPath());
    }

    /**
     * @api(
     *     title="optionCachedPath 获取配置缓存路径",
     *     description="",
     *     note="",
     * )
     */
    public function testOptionCachedPath(): void
    {
        $app = $this->createApp();
        $appPath = __DIR__.'/app';

        $this->assertSame($appPath.'/bootstrap/option.php', $app->optionCachedPath());
    }

    /**
     * @api(
     *     title="isCachedOption 是否存在配置缓存",
     *     description="",
     *     note="",
     * )
     */
    public function testIsCachedOption(): void
    {
        $appPath = __DIR__.'/app';
        $app = $this->createApp();

        $this->assertFalse($app->isCachedOption());

        mkdir($appPath.'/bootstrap', 0777, true);

        file_put_contents($optionPath = $appPath.'/bootstrap/option.php', 'foo');

        $this->assertTrue($app->isCachedOption());

        Helper::deleteDirectory($appPath, true);
    }

    /**
     * @api(
     *     title="routerCachedPath 获取路由缓存路径",
     *     description="",
     *     note="",
     * )
     */
    public function testRouterCachedPath(): void
    {
        $appPath = __DIR__.'/app';
        $app = $this->createApp();

        $this->assertSame($appPath.'/bootstrap/router.php', $app->routerCachedPath());
    }

    /**
     * @api(
     *     title="isCachedRouter 是否存在路由缓存",
     *     description="",
     *     note="",
     * )
     */
    public function testIsCachedRouter(): void
    {
        $appPath = __DIR__.'/app';
        $app = $this->createApp();

        $this->assertFalse($app->isCachedRouter());

        mkdir($appPath.'/bootstrap', 0777, true);

        file_put_contents($routerPath = $appPath.'/bootstrap/router.php', 'foo');

        $this->assertTrue($app->isCachedRouter());

        Helper::deleteDirectory($appPath, true);
    }

    /**
     * @api(
     *     title="debug 是否开启调试",
     *     description="",
     *     note="",
     * )
     */
    public function testDebug(): void
    {
        $appPath = __DIR__.'/app';
        $app = $this->createApp();
        $container = $app->container();

        $option = $this->createMock(IOption::class);

        $option
            ->method('get')
            ->willReturnCallback(function (string $k) {
                $map = [
                    'debug'       => true,
                    'environment' => 'development',
                ];

                return $map[$k];
            });

        $this->assertSame('development', $option->get('environment'));
        $this->assertTrue($option->get('debug'));

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        $this->assertTrue($app->isDebug());
    }

    public function testDebug2(): void
    {
        $appPath = __DIR__.'/app';
        $app = $this->createApp();
        $container = $app->container();

        $option = $this->createMock(IOption::class);

        $option
            ->method('get')
            ->willReturnCallback(function (string $k) {
                $map = [
                    'debug'       => false,
                    'environment' => 'development',
                ];

                return $map[$k];
            });

        $this->assertSame('development', $option->get('environment'));
        $this->assertFalse($option->get('debug'));

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        $this->assertFalse($app->isDebug());
    }

    /**
     * @api(
     *     title="isDevelopment 是否为开发环境",
     *     description="",
     *     note="",
     * )
     */
    public function testIsDevelopment(): void
    {
        $appPath = __DIR__.'/app';
        $app = $this->createApp();
        $container = $app->container();

        $option = $this->createMock(Request::class);

        $option->method('get')->willReturn('development');
        $this->assertEquals('development', $option->get('development'));

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        $this->assertTrue($app->isDevelopment());
    }

    public function testIsDevelopment2(): void
    {
        $appPath = __DIR__.'/app';
        $app = $this->createApp();
        $container = $app->container();

        $option = $this->createMock(Request::class);

        $option->method('get')->willReturn('foo');
        $this->assertEquals('foo', $option->get('development'));

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        $this->assertFalse($app->isDevelopment());
    }

    /**
     * @api(
     *     title="environment 获取运行环境",
     *     description="",
     *     note="",
     * )
     */
    public function testEnvironment(): void
    {
        $appPath = __DIR__.'/app';
        $app = $this->createApp();
        $container = $app->container();

        $option = $this->createMock(Request::class);

        $option->method('get')->willReturn('foo');
        $this->assertEquals('foo', $option->get('development'));

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        $this->assertSame('foo', $app->environment());
    }

    /**
     * @api(
     *     title="bootstrap 初始化应用",
     *     description="
     * **fixture 定义**
     *
     * **Tests\Kernel\BootstrapTest1**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\BootstrapTest1::class)]}
     * ```
     *
     * **Tests\Kernel\BootstrapTest2**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\BootstrapTest2::class)]}
     * ```
     *
     * **Tests\Console\Load1\Test1**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Console\Load1\Test1::class)]}
     * ```
     * ",
     *     note="",
     * )
     */
    public function testBootstrap(): void
    {
        $app = $this->createApp();

        $app->bootstrap([BootstrapTest1::class, BootstrapTest2::class]);

        $this->assertSame(1, $_SERVER['bootstrapTest1']);
        $this->assertSame(1, $_SERVER['bootstrapTest2']);

        unset($_SERVER['bootstrapTest1'], $_SERVER['bootstrapTest2']);
    }

    public function testBootstrap2(): void
    {
        $app = $this->createApp();
        $container = $app->container();

        $app->bootstrap([BootstrapTest1::class, BootstrapTest2::class]);

        $this->assertFalse($container->isBootstrap());

        $this->assertSame(1, $_SERVER['bootstrapTest1']);
        $this->assertSame(1, $_SERVER['bootstrapTest2']);

        unset($_SERVER['bootstrapTest1'], $_SERVER['bootstrapTest2']);

        $container->registerProviders([], [], []);

        $this->assertTrue($container->isBootstrap());

        $app->bootstrap([BootstrapTest1::class, BootstrapTest2::class]);

        $this->assertArrayNotHasKey('bootstrapTest1', $_SERVER);
        $this->assertArrayNotHasKey('bootstrapTest2', $_SERVER);
    }

    /**
     * @api(
     *     title="registerAppProviders 注册应用服务提供者",
     *     description="
     * **fixture 定义**
     *
     * **Tests\Kernel\OptionTest**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\OptionTest::class)]}
     * ```
     *
     * **Tests\Kernel\ProviderTest3**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\ProviderTest3::class)]}
     * ```
     *
     * **Tests\Kernel\ProviderDeferTest1**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\ProviderDeferTest1::class)]}
     * ```
     * ",
     *     note="",
     * )
     */
    public function testRegisterProviders(): void
    {
        $app = $this->createApp();
        $container = $app->container();

        $option = new OptionTest();
        $container->singleton('option', function () use ($option) {
            return $option;
        });

        $app->registerAppProviders();

        // for deferredAlias
        $this->assertArrayNotHasKey('providerDeferTest1', $_SERVER);
        $this->assertSame('bar', $container->make('foo'));
        $this->assertSame('bar', $container->make(ProviderDeferTest1::class));
        $this->assertSame(1, $_SERVER['providerDeferTest1']);

        // for providers
        $this->assertSame(1, $_SERVER['testRegisterProvidersRegister']);
        $this->assertArrayHasKey('testRegisterProvidersBootstrap', $_SERVER);

        unset(
            $_SERVER['providerDeferTest1'],
            $_SERVER['testRegisterProvidersRegister']
        );

        // bootstrap
        $this->assertTrue($container->isBootstrap());
        $this->assertSame(1, $_SERVER['testRegisterProvidersBootstrap']);
        unset($_SERVER['testRegisterProvidersBootstrap']);
        $this->assertTrue($container->isBootstrap());

        // again but already bootstrap
        $app->registerAppProviders();
        $this->assertArrayNotHasKey('testRegisterProvidersBootstrap', $_SERVER);
        $this->assertArrayNotHasKey('testRegisterProvidersRegister', $_SERVER);
    }

    /**
     * @api(
     *     title="setPublicPath 设置资源路径",
     *     description="",
     *     note="",
     * )
     */
    public function testSetPublicPath(): void
    {
        $app = $this->createApp();
        $this->assertSame(__DIR__.'/app/public', $app->publicPath());
        $app->setPublicPath(__DIR__.'/hello');
        $this->assertSame(__DIR__.'/hello', $app->publicPath());
    }

    /**
     * @api(
     *     title="setThemesPath 设置主题路径",
     *     description="",
     *     note="",
     * )
     */
    public function testSetThemesPath(): void
    {
        $app = $this->createApp();
        $this->assertSame(__DIR__.'/app/themes', $app->themesPath());
        $app->setThemesPath(__DIR__.'/hello');
        $this->assertSame(__DIR__.'/hello', $app->themesPath());
    }

    /**
     * @dataProvider envProvider
     *
     * @param mixed $value
     * @param mixed $envValue
     *
     * @api(
     *     title="env 获取应用的环境变量",
     *     description="
     * 测试数据
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Kernel\AppTest::class, 'envProvider')]}
     * ```
     * ",
     *     note="",
     * )
     */
    public function testEnv(string $name, $value, $envValue): void
    {
        $app = $this->createApp();
        $name = 'test_env_'.$name;
        putenv($name.'='.$value);
        $this->assertSame($envValue, $app->env($name));
    }

    public function envProvider(): array
    {
        return [
            ['bar', 'true', true],
            ['bar', '(true)', true],
            ['bar', 'false', false],
            ['bar', '(false)', false],
            ['bar', 'empty', ''],
            ['bar', '(empty)', ''],
            ['bar', 'null', null],
            ['bar', '(null)', null],
            ['bar', '"hello"', 'hello'],
            ['bar', "'hello'", "'hello'"],
            ['bar', true, '1'],
            ['bar', false, ''],
            ['bar', 1, '1'],
            ['bar', '', ''],
        ];
    }

    public function testEnvFalse(): void
    {
        $app = $this->createApp();
        $this->assertSame('default message', $app->env('not_found_env', 'default message'));
        $this->assertNull($app->env('not_found_env'));
    }

    protected function createApp(): App
    {
        $container = Container::singletons();
        $app = new App($container, $appPath = __DIR__.'/app');

        $this->assertInstanceof(IContainer::class, $container);
        $this->assertInstanceof(Container::class, $container);
        $this->assertInstanceof(IApp::class, $app);
        $this->assertInstanceof(Apps::class, $app);

        return $app;
    }
}

class App extends Apps
{
    protected function registerBaseProvider(): void
    {
    }
}

class ProviderTest3 extends Provider
{
    public function __construct(IContainer $container)
    {
    }

    public function bootstrap()
    {
        $_SERVER['testRegisterProvidersBootstrap'] = 1;
    }

    public function register(): void
    {
        $_SERVER['testRegisterProvidersRegister'] = 1;
    }
}

class ProviderDeferTest1 extends Provider
{
    public function register(): void
    {
        $_SERVER['providerDeferTest1'] = 1;

        $this->container->singleton('foo', function (IContainer $container) {
            return 'bar';
        });
    }

    public static function providers(): array
    {
        return [
            'foo' => [
                'Tests\\Kernel\\ProviderDeferTest1',
            ],
        ];
    }

    public static function isDeferred(): bool
    {
        return true;
    }
}

class BootstrapTest1
{
    public function handle()
    {
        $_SERVER['bootstrapTest1'] = 1;
    }
}

class BootstrapTest2
{
    public function handle()
    {
        $_SERVER['bootstrapTest2'] = 1;
    }
}

class OptionTest
{
    public function get(string $name)
    {
        if ('_deferred_providers' === $name) {
            return [
                [
                    'foo' => 'Tests\\Kernel\\ProviderDeferTest1',
                ],
                [
                    'Tests\\Kernel\\ProviderDeferTest1' => [
                        'foo' => [
                            'Tests\\Kernel\\ProviderDeferTest1',
                        ],
                    ],
                ],
            ];
        }

        if ('_composer.providers' === $name) {
            return [ProviderTest3::class];
        }
    }
}
