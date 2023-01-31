<?php

declare(strict_types=1);

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
 *     zh-CN:title="应用",
 *     path="architecture/kernel/app",
 *     zh-CN:description="应用是整个系统非常核心的一部分，定义了应用的骨架。",
 *     zh-CN:note="
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
     *     zh-CN:title="基本使用",
     *     zh-CN:description="",
     *     zh-CN:note="",
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
     *     zh-CN:title="version 获取程序版本",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testVersion(): void
    {
        $app = $this->createApp();

        $this->assertSame(App::VERSION, $app->version());
    }

    /**
     * @api(
     *     zh-CN:title="isConsole 是否为 PHP 运行模式命令行",
     *     zh-CN:description="",
     *     zh-CN:note="",
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
     *     zh-CN:title="setPath 设置基础路径",
     *     zh-CN:description="",
     *     zh-CN:note="",
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
     *     zh-CN:title="appPath 获取应用路径",
     *     zh-CN:description="",
     *     zh-CN:note="",
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

        $this->assertSame($appPath, $app->appPath());
        $this->assertSame($appPath, $app->appPath(''));
        $this->assertSame($appPath.'/foo', $app->appPath('foo'));
        $this->assertSame($appPath.'/bar', $app->appPath('bar'));
        $this->assertSame($appPath.'/foo/foo/bar', $app->appPath('foo/foo/bar'));
        $this->assertSame($appPath.'/bar/foo/bar', $app->appPath('bar/foo/bar'));
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

        $this->assertSame($appPath, $app->appPath());
    }

    /**
     * @api(
     *     zh-CN:title="setAppPath 设置应用路径",
     *     zh-CN:description="",
     *     zh-CN:note="",
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

        $this->assertSame($appPath, $app->appPath());
        $app->setAppPath(__DIR__.'/app/foo');
        $this->assertSame($appPath.'/foo', $app->appPath());
    }

    /**
     * @api(
     *     zh-CN:title="storagePath 获取运行路径",
     *     zh-CN:description="",
     *     zh-CN:note="",
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
     *     zh-CN:title="setStoragePath 设置运行时路径",
     *     zh-CN:description="",
     *     zh-CN:note="",
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
     *     zh-CN:title="optionPath 获取配置路径",
     *     zh-CN:description="",
     *     zh-CN:note="",
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
     *     zh-CN:title="setOptionPath 设置配置路径",
     *     zh-CN:description="",
     *     zh-CN:note="",
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
     *     zh-CN:title="i18nPath 获取语言包路径",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testI18nPath(): void
    {
        $app = $this->createApp();
        $appPath = __DIR__.'/app';

        $this->assertSame($appPath.'/assets/i18n', $app->i18nPath());
        $this->assertSame($appPath.'/assets/i18n/foobar', $app->i18nPath('foobar'));
    }

    /**
     * @api(
     *     zh-CN:title="setI18nPath 设置语言包路径",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testSetI18nPath(): void
    {
        $app = $this->createApp();
        $appPath = __DIR__.'/app';

        $this->assertSame($appPath.'/assets/i18n', $app->i18nPath());
        $this->assertSame($appPath.'/assets/i18n/foobar', $app->i18nPath('foobar'));

        $app->setI18nPath(__DIR__.'/app/i18nFoo');

        $this->assertSame($appPath.'/i18nFoo', $app->i18nPath());
        $this->assertSame($appPath.'/i18nFoo/foobar', $app->i18nPath('foobar'));
    }

    /**
     * @api(
     *     zh-CN:title="envPath 获取环境变量路径",
     *     zh-CN:description="",
     *     zh-CN:note="",
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
     *     zh-CN:title="setEnvPath 设置环境变量路径",
     *     zh-CN:description="",
     *     zh-CN:note="",
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
     *     zh-CN:title="envFile 获取环境变量文件",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testEnvFile(): void
    {
        $app = $this->createApp();

        $this->assertSame('.env', $app->envFile());
    }

    /**
     * @api(
     *     zh-CN:title="setEnvFile 设置环境变量文件",
     *     zh-CN:description="",
     *     zh-CN:note="",
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
     *     zh-CN:title="fullEnvPath 获取环境变量完整路径",
     *     zh-CN:description="",
     *     zh-CN:note="",
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
     *     zh-CN:title="i18nCachedPath 获取语言包缓存路径",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testSetI18nCachePath(): void
    {
        $app = $this->createApp();
        $this->assertSame(__DIR__.'/app/storage/bootstrap/i18n/zh-CN.php', $app->i18nCachedPath('zh-CN'));
        $app->setI18nCachedPath(__DIR__.'/hello');
        $this->assertSame(__DIR__.'/hello/zh-CN.php', $app->i18nCachedPath('zh-CN'));
    }

    /**
     * @api(
     *     zh-CN:title="i18nCachedPath 获取语言包缓存路径",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testI18nCachedPath(): void
    {
        $app = $this->createApp();
        $appPath = __DIR__.'/app';

        $this->assertSame($appPath.'/storage/bootstrap/i18n/zh-CN.php', $app->i18nCachedPath('zh-CN'));
        $this->assertSame($appPath.'/storage/bootstrap/i18n/zh-TW.php', $app->i18nCachedPath('zh-TW'));
        $this->assertSame($appPath.'/storage/bootstrap/i18n/en-US.php', $app->i18nCachedPath('en-US'));
    }

    /**
     * @api(
     *     zh-CN:title="isCachedI18n 是否存在语言包缓存",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testIsCachedI18n(): void
    {
        $app = $this->createApp();
        $appPath = __DIR__.'/app';

        $this->assertFalse($app->isCachedI18n('zh-CN'));

        mkdir($appPath.'/storage/bootstrap/i18n', 0777, true);
        file_put_contents($appPath.'/storage/bootstrap/i18n/zh-CN.php', 'foo');
        $this->assertTrue($app->isCachedI18n('zh-CN'));

        Helper::deleteDirectory($appPath);
    }

    /**
     * @api(
     *     zh-CN:title="setOptionCachedPath 设置配置缓存路径",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testSetOptionCachePath(): void
    {
        $app = $this->createApp();
        $this->assertSame(__DIR__.'/app/storage/bootstrap/option.php', $app->optionCachedPath());
        $app->setOptionCachedPath(__DIR__.'/hello');
        $this->assertSame(__DIR__.'/hello/option.php', $app->optionCachedPath());
    }

    /**
     * @api(
     *     zh-CN:title="optionCachedPath 获取配置缓存路径",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testOptionCachedPath(): void
    {
        $app = $this->createApp();
        $appPath = __DIR__.'/app';
        $this->assertSame($appPath.'/storage/bootstrap/option.php', $app->optionCachedPath());
    }

    /**
     * @api(
     *     zh-CN:title="isCachedOption 是否存在配置缓存",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testIsCachedOption(): void
    {
        $appPath = __DIR__.'/app';
        $app = $this->createApp();

        $this->assertFalse($app->isCachedOption());
        mkdir($appPath.'/storage/bootstrap', 0777, true);
        file_put_contents($appPath.'/storage/bootstrap/option.php', 'foo');
        $this->assertTrue($app->isCachedOption());

        Helper::deleteDirectory($appPath);
    }

    /**
     * @api(
     *     zh-CN:title="routerCachedPath 获取路由缓存路径",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testRouterCachedPath(): void
    {
        $appPath = __DIR__.'/app';
        $app = $this->createApp();

        $this->assertSame($appPath.'/storage/bootstrap/router.php', $app->routerCachedPath());
    }

    /**
     * @api(
     *     zh-CN:title="isCachedRouter 是否存在路由缓存",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testIsCachedRouter(): void
    {
        $appPath = __DIR__.'/app';
        $app = $this->createApp();

        $this->assertFalse($app->isCachedRouter());

        mkdir($appPath.'/storage/bootstrap', 0777, true);

        file_put_contents($routerPath = $appPath.'/storage/bootstrap/router.php', 'foo');

        $this->assertTrue($app->isCachedRouter());

        Helper::deleteDirectory($appPath);
    }

    /**
     * @api(
     *     zh-CN:title="namespacePath 获取命名空间目录真实路径",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testNamespacePath(): void
    {
        $appPath = dirname(__DIR__, 2);
        $app = $this->createApp($appPath);
        $container = $app->container();
        $this->assertSame(
            dirname(__DIR__, 2).'/src/Leevel/Kernel/Console',
            realpath($app->namespacePath('Leevel\\Kernel\\Console'))
        );
    }

    public function testNamespacePathClassNotFound(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Namespaces `not_found_class` for was not found.'
        );

        $appPath = dirname(__DIR__, 2);
        $app = $this->createApp($appPath);
        $container = $app->container();
        $app->namespacePath('not_found_class');
    }

    public function testNamespacePathComposerWasNotFound(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Composer was not found.'
        );

        $appPath = __DIR__.'/assert/app';
        $app = $this->createApp($appPath);
        $container = $app->container();
        $app->namespacePath('not_found_class');
    }

    /**
     * @api(
     *     zh-CN:title="isDebug 是否开启调试",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testIsDebug(): void
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

    public function testIsDebug2(): void
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
     *     zh-CN:title="isDevelopment 是否为开发环境",
     *     zh-CN:description="",
     *     zh-CN:note="",
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
     *     zh-CN:title="environment 获取运行环境",
     *     zh-CN:description="",
     *     zh-CN:note="",
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
     *     zh-CN:title="bootstrap 初始化应用",
     *     zh-CN:description="
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
     *     zh-CN:note="",
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
     *     zh-CN:title="registerAppProviders 注册应用服务提供者",
     *     zh-CN:description="
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
     *     zh-CN:note="",
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
     *     zh-CN:title="setThemesPath 设置主题路径",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testSetThemesPath(): void
    {
        $app = $this->createApp();
        $this->assertSame(__DIR__.'/app/assets/themes', $app->themesPath());
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
     *     zh-CN:title="env 获取应用的环境变量",
     *     zh-CN:description="
     * 测试数据
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Kernel\AppTest::class, 'envProvider')]}
     * ```
     * ",
     *     zh-CN:note="",
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

    protected function createApp(?string $appPath = null): App
    {
        if (null === $appPath) {
            $appPath = __DIR__.'/app';
        }
        $container = Container::singletons();
        $app = new App($container, $appPath);

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
        if (':deferred_providers' === $name) {
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

        if (':composer.providers' === $name) {
            return [ProviderTest3::class];
        }
    }
}
