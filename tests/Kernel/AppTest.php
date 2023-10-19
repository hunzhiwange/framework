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

#[Api([
    'zh-CN:title' => '应用',
    'path' => 'architecture/kernel/app',
    'zh-CN:description' => <<<'EOT'
应用是整个系统非常核心的一部分，定义了应用的骨架。
EOT,
    'zh-CN:note' => <<<'EOT'
应用设计为可替代，只需要实现 `\Leevel\Kernel\IApp` 即可，然后在入口文件替换即可。
EOT,
])]
final class AppTest extends TestCase
{
    protected function setUp(): void
    {
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        Container::singletons()->clear();
    }

    #[Api([
        'zh-CN:title' => '基本使用',
    ])]
    public function testBaseUse(): void
    {
        $appPath = __DIR__.'/app';
        $app = $this->createApp();
        $container = $app->container();

        $this->assertInstanceof(IContainer::class, $container);
        $this->assertInstanceof(Container::class, $container);
        $this->assertInstanceof(IApp::class, $app);
        $this->assertInstanceof(Apps::class, $app);

        static::assertSame($appPath, $app->path());
        static::assertSame($appPath.'/foobar', $app->path('foobar'));
    }

    #[Api([
        'zh-CN:title' => 'version 获取程序版本',
    ])]
    public function testVersion(): void
    {
        $app = $this->createApp();

        static::assertSame(App::VERSION, $app->version());
    }

    #[Api([
        'zh-CN:title' => 'isConsole 是否为 PHP 运行模式命令行',
    ])]
    public function testIsConsole(): void
    {
        $app = $this->createApp();

        static::assertTrue($app->isConsole());
    }

    public function testIsConsole2(): void
    {
        $app = $this->createApp();

        $request = $this->createMock(Request::class);

        $request->method('isConsole')->willReturn(true);
        static::assertTrue($request->isConsole());

        $app->container()->singleton('request', function () use ($request) {
            return $request;
        });

        static::assertTrue($app->isConsole());
    }

    public function testIsConsole3(): void
    {
        $app = $this->createApp();
        $container = $app->container();

        $request = $this->createMock(Request::class);
        $request->method('isConsole')->willReturn(false);
        static::assertFalse($request->isConsole());
        $container->singleton('request', function () use ($request) {
            return $request;
        });

        static::assertFalse($app->isConsole());
    }

    #[Api([
        'zh-CN:title' => 'setPath 设置基础路径',
    ])]
    public function testSetPath(): void
    {
        $app = $this->createApp();

        $app->setPath(__DIR__.'/foo');

        static::assertSame(__DIR__.'/foo', $app->path());
    }

    #[Api([
        'zh-CN:title' => 'appPath 获取应用路径',
    ])]
    public function testAppPath(): void
    {
        $appPath = __DIR__.'/app';
        $app = $this->createApp();
        $container = $app->container();

        $request = $this->createMock(Request::class);
        $container->singleton('request', function () use ($request) {
            return $request;
        });

        static::assertSame($appPath, $app->appPath());
        static::assertSame($appPath, $app->appPath(''));
        static::assertSame($appPath.'/foo', $app->appPath('foo'));
        static::assertSame($appPath.'/bar', $app->appPath('bar'));
        static::assertSame($appPath.'/foo/foo/bar', $app->appPath('foo/foo/bar'));
        static::assertSame($appPath.'/bar/foo/bar', $app->appPath('bar/foo/bar'));
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

        static::assertSame($appPath, $app->appPath());
    }

    #[Api([
        'zh-CN:title' => 'setAppPath 设置应用路径',
    ])]
    public function testSetAppPath(): void
    {
        $appPath = __DIR__.'/app';
        $app = $this->createApp();
        $container = $app->container();

        $request = $this->createMock(Request::class);
        $container->singleton('request', function () use ($request) {
            return $request;
        });

        static::assertSame($appPath, $app->appPath());
        $app->setAppPath(__DIR__.'/app/foo');
        static::assertSame($appPath.'/foo', $app->appPath());
    }

    #[Api([
        'zh-CN:title' => 'storagePath 获取运行路径',
    ])]
    public function testStoragePath(): void
    {
        $app = $this->createApp();
        $appPath = __DIR__.'/app';

        static::assertSame($appPath.'/storage', $app->storagePath());
        static::assertSame($appPath.'/storage/foobar', $app->storagePath('foobar'));
    }

    #[Api([
        'zh-CN:title' => 'setStoragePath 设置运行时路径',
    ])]
    public function testSetStoragePath(): void
    {
        $app = $this->createApp();
        $appPath = __DIR__.'/app';

        static::assertSame($appPath.'/storage', $app->storagePath());
        static::assertSame($appPath.'/storage/foobar', $app->storagePath('foobar'));

        $app->setStoragePath(__DIR__.'/app/storageFoo');

        static::assertSame($appPath.'/storageFoo', $app->storagePath());
        static::assertSame($appPath.'/storageFoo/foobar', $app->storagePath('foobar'));
    }

    #[Api([
        'zh-CN:title' => 'optionPath 获取配置路径',
    ])]
    public function testOptionPath(): void
    {
        $app = $this->createApp();
        $appPath = __DIR__.'/app';

        static::assertSame($appPath.'/option', $app->optionPath());
        static::assertSame($appPath.'/option/foobar', $app->optionPath('foobar'));
    }

    #[Api([
        'zh-CN:title' => 'setOptionPath 设置配置路径',
    ])]
    public function testSetOptionPath(): void
    {
        $app = $this->createApp();
        $appPath = __DIR__.'/app';

        static::assertSame($appPath.'/option', $app->optionPath());
        static::assertSame($appPath.'/option/foobar', $app->optionPath('foobar'));

        $app->setOptionPath(__DIR__.'/app/optionFoo');

        static::assertSame($appPath.'/optionFoo', $app->optionPath());
        static::assertSame($appPath.'/optionFoo/foobar', $app->optionPath('foobar'));
    }

    #[Api([
        'zh-CN:title' => 'i18nPath 获取语言包路径',
    ])]
    public function testI18nPath(): void
    {
        $app = $this->createApp();
        $appPath = __DIR__.'/app';

        static::assertSame($appPath.'/assets/i18n', $app->i18nPath());
        static::assertSame($appPath.'/assets/i18n/foobar', $app->i18nPath('foobar'));
    }

    #[Api([
        'zh-CN:title' => 'setI18nPath 设置语言包路径',
    ])]
    public function testSetI18nPath(): void
    {
        $app = $this->createApp();
        $appPath = __DIR__.'/app';

        static::assertSame($appPath.'/assets/i18n', $app->i18nPath());
        static::assertSame($appPath.'/assets/i18n/foobar', $app->i18nPath('foobar'));

        $app->setI18nPath(__DIR__.'/app/i18nFoo');

        static::assertSame($appPath.'/i18nFoo', $app->i18nPath());
        static::assertSame($appPath.'/i18nFoo/foobar', $app->i18nPath('foobar'));
    }

    #[Api([
        'zh-CN:title' => 'envPath 获取环境变量路径',
    ])]
    public function testEnvPath(): void
    {
        $app = $this->createApp();
        $appPath = __DIR__.'/app';

        static::assertSame($appPath, $app->envPath());
    }

    #[Api([
        'zh-CN:title' => 'setEnvPath 设置环境变量路径',
    ])]
    public function testSetEnvPath(): void
    {
        $app = $this->createApp();
        $appPath = __DIR__.'/app';

        static::assertSame($appPath, $app->envPath());

        $app->setEnvPath(__DIR__.'/appFoo');

        static::assertSame(__DIR__.'/appFoo', $app->envPath());
    }

    #[Api([
        'zh-CN:title' => 'envFile 获取环境变量文件',
    ])]
    public function testEnvFile(): void
    {
        $app = $this->createApp();

        static::assertSame('.env', $app->envFile());
    }

    #[Api([
        'zh-CN:title' => 'setEnvFile 设置环境变量文件',
    ])]
    public function testSetEnvFile(): void
    {
        $app = $this->createApp();

        static::assertSame('.env', $app->envFile());

        $app->setEnvFile('.envfoo');

        static::assertSame('.envfoo', $app->envFile());
    }

    #[Api([
        'zh-CN:title' => 'fullEnvPath 获取环境变量完整路径',
    ])]
    public function testFullEnvPath(): void
    {
        $app = $this->createApp();
        $appPath = __DIR__.'/app';

        static::assertSame($appPath.'/.env', $app->fullEnvPath());

        $app->setEnvPath(__DIR__.'/appFoo');

        static::assertSame(__DIR__.'/appFoo/.env', $app->fullEnvPath());

        $app->setEnvFile('.envfoo');

        static::assertSame(__DIR__.'/appFoo/.envfoo', $app->fullEnvPath());
    }

    #[Api([
        'zh-CN:title' => 'i18nCachedPath 获取语言包缓存路径',
    ])]
    public function testSetI18nCachePath(): void
    {
        $app = $this->createApp();
        static::assertSame(__DIR__.'/app/storage/bootstrap/i18n/zh-CN.php', $app->i18nCachedPath('zh-CN'));
        $app->setI18nCachedPath(__DIR__.'/hello');
        static::assertSame(__DIR__.'/hello/zh-CN.php', $app->i18nCachedPath('zh-CN'));
    }

    #[Api([
        'zh-CN:title' => 'i18nCachedPath 获取语言包缓存路径',
    ])]
    public function testI18nCachedPath(): void
    {
        $app = $this->createApp();
        $appPath = __DIR__.'/app';

        static::assertSame($appPath.'/storage/bootstrap/i18n/zh-CN.php', $app->i18nCachedPath('zh-CN'));
        static::assertSame($appPath.'/storage/bootstrap/i18n/zh-TW.php', $app->i18nCachedPath('zh-TW'));
        static::assertSame($appPath.'/storage/bootstrap/i18n/en-US.php', $app->i18nCachedPath('en-US'));
    }

    #[Api([
        'zh-CN:title' => 'isCachedI18n 是否存在语言包缓存',
    ])]
    public function testIsCachedI18n(): void
    {
        $app = $this->createApp();
        $appPath = __DIR__.'/app';

        static::assertFalse($app->isCachedI18n('zh-CN'));

        mkdir($appPath.'/storage/bootstrap/i18n', 0o777, true);
        file_put_contents($appPath.'/storage/bootstrap/i18n/zh-CN.php', 'foo');
        static::assertTrue($app->isCachedI18n('zh-CN'));

        Helper::deleteDirectory($appPath);
    }

    #[Api([
        'zh-CN:title' => 'setOptionCachedPath 设置配置缓存路径',
    ])]
    public function testSetOptionCachePath(): void
    {
        $app = $this->createApp();
        static::assertSame(__DIR__.'/app/storage/bootstrap/option.php', $app->optionCachedPath());
        $app->setOptionCachedPath(__DIR__.'/hello');
        static::assertSame(__DIR__.'/hello/option.php', $app->optionCachedPath());
    }

    #[Api([
        'zh-CN:title' => 'optionCachedPath 获取配置缓存路径',
    ])]
    public function testOptionCachedPath(): void
    {
        $app = $this->createApp();
        $appPath = __DIR__.'/app';
        static::assertSame($appPath.'/storage/bootstrap/option.php', $app->optionCachedPath());
    }

    #[Api([
        'zh-CN:title' => 'isCachedOption 是否存在配置缓存',
    ])]
    public function testIsCachedOption(): void
    {
        $appPath = __DIR__.'/app';
        $app = $this->createApp();

        static::assertFalse($app->isCachedOption());
        mkdir($appPath.'/storage/bootstrap', 0o777, true);
        file_put_contents($appPath.'/storage/bootstrap/option.php', 'foo');
        static::assertTrue($app->isCachedOption());

        Helper::deleteDirectory($appPath);
    }

    #[Api([
        'zh-CN:title' => 'routerCachedPath 获取路由缓存路径',
    ])]
    public function testRouterCachedPath(): void
    {
        $appPath = __DIR__.'/app';
        $app = $this->createApp();

        static::assertSame($appPath.'/storage/bootstrap/router.php', $app->routerCachedPath());
    }

    #[Api([
        'zh-CN:title' => 'isCachedRouter 是否存在路由缓存',
    ])]
    public function testIsCachedRouter(): void
    {
        $appPath = __DIR__.'/app';
        $app = $this->createApp();

        static::assertFalse($app->isCachedRouter());

        mkdir($appPath.'/storage/bootstrap', 0o777, true);

        file_put_contents($routerPath = $appPath.'/storage/bootstrap/router.php', 'foo');

        static::assertTrue($app->isCachedRouter());

        Helper::deleteDirectory($appPath);
    }

    #[Api([
        'zh-CN:title' => 'namespacePath 获取命名空间目录真实路径',
    ])]
    public function testNamespacePath(): void
    {
        $appPath = \dirname(__DIR__, 2);
        $app = $this->createApp($appPath);
        static::assertSame(
            \dirname(__DIR__, 2).'/src/Leevel/Kernel/Console',
            realpath($app->namespacePath('Leevel\\Kernel\\Console'))
        );
    }

    public function testNamespacePathClassNotFound(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Namespaces `not_found_class` for was not found.'
        );

        $appPath = \dirname(__DIR__, 2);
        $app = $this->createApp($appPath);
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

    #[Api([
        'zh-CN:title' => 'isDebug 是否开启调试',
    ])]
    public function testIsDebug(): void
    {
        $app = $this->createApp();
        $container = $app->container();

        $option = $this->createMock(IOption::class);

        $option
            ->method('get')
            ->willReturnCallback(function (string $k) {
                $map = [
                    'debug' => true,
                    'environment' => 'development',
                ];

                return $map[$k];
            })
        ;

        static::assertSame('development', $option->get('environment'));
        static::assertTrue($option->get('debug'));

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        static::assertTrue($app->isDebug());
    }

    public function testIsDebug2(): void
    {
        $app = $this->createApp();
        $container = $app->container();

        $option = $this->createMock(IOption::class);

        $option
            ->method('get')
            ->willReturnCallback(function (string $k) {
                $map = [
                    'debug' => false,
                    'environment' => 'development',
                ];

                return $map[$k];
            })
        ;

        static::assertSame('development', $option->get('environment'));
        static::assertFalse($option->get('debug'));

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        static::assertFalse($app->isDebug());
    }

    #[Api([
        'zh-CN:title' => 'isDevelopment 是否为开发环境',
    ])]
    public function testIsDevelopment(): void
    {
        $app = $this->createApp();
        $container = $app->container();

        $option = $this->createMock(Request::class);

        $option->method('get')->willReturn('development');
        static::assertSame('development', $option->get('development'));

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        static::assertTrue($app->isDevelopment());
    }

    public function testIsDevelopment2(): void
    {
        $app = $this->createApp();
        $container = $app->container();

        $option = $this->createMock(Request::class);

        $option->method('get')->willReturn('foo');
        static::assertSame('foo', $option->get('development'));

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        static::assertFalse($app->isDevelopment());
    }

    #[Api([
        'zh-CN:title' => 'environment 获取运行环境',
    ])]
    public function testEnvironment(): void
    {
        $app = $this->createApp();
        $container = $app->container();

        $option = $this->createMock(Request::class);

        $option->method('get')->willReturn('foo');
        static::assertSame('foo', $option->get('development'));

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        static::assertSame('foo', $app->environment());
    }

    #[Api([
        'zh-CN:title' => 'bootstrap 初始化应用',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**Tests\Kernel\BootstrapTest1**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\BootstrapTest1::class)]}
```

**Tests\Kernel\BootstrapTest2**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\BootstrapTest2::class)]}
```

**Tests\Console\Load1\Test1**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Console\Load1\Test1::class)]}
```
EOT,
    ])]
    public function testBootstrap(): void
    {
        $app = $this->createApp();

        $app->bootstrap([BootstrapTest1::class, BootstrapTest2::class]);

        static::assertSame(1, $_SERVER['bootstrapTest1']);
        static::assertSame(1, $_SERVER['bootstrapTest2']);

        unset($_SERVER['bootstrapTest1'], $_SERVER['bootstrapTest2']);
    }

    public function testBootstrap2(): void
    {
        $app = $this->createApp();
        $container = $app->container();

        $app->bootstrap([BootstrapTest1::class, BootstrapTest2::class]);

        static::assertFalse($container->isBootstrap());

        static::assertSame(1, $_SERVER['bootstrapTest1']);
        static::assertSame(1, $_SERVER['bootstrapTest2']);

        unset($_SERVER['bootstrapTest1'], $_SERVER['bootstrapTest2']);

        $container->registerProviders([], [], []);

        static::assertTrue($container->isBootstrap());

        $app->bootstrap([BootstrapTest1::class, BootstrapTest2::class]);

        static::assertArrayNotHasKey('bootstrapTest1', $_SERVER);
        static::assertArrayNotHasKey('bootstrapTest2', $_SERVER);
    }

    #[Api([
        'zh-CN:title' => 'registerAppProviders 注册应用服务提供者',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**Tests\Kernel\OptionTest**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\OptionTest::class)]}
```

**Tests\Kernel\ProviderTest3**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\ProviderTest3::class)]}
```

**Tests\Kernel\ProviderDeferTest1**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\ProviderDeferTest1::class)]}
```
EOT,
    ])]
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
        static::assertArrayNotHasKey('providerDeferTest1', $_SERVER);
        static::assertSame('bar', $container->make('foo'));
        static::assertSame('bar', $container->make(ProviderDeferTest1::class));
        static::assertSame(1, $_SERVER['providerDeferTest1']);

        // for providers
        static::assertSame(1, $_SERVER['testRegisterProvidersRegister']);
        static::assertArrayHasKey('testRegisterProvidersBootstrap', $_SERVER);

        unset(
            $_SERVER['providerDeferTest1'],
            $_SERVER['testRegisterProvidersRegister']
        );

        // bootstrap
        static::assertTrue($container->isBootstrap());
        static::assertSame(1, $_SERVER['testRegisterProvidersBootstrap']);
        unset($_SERVER['testRegisterProvidersBootstrap']);
        static::assertTrue($container->isBootstrap());

        // again but already bootstrap
        $app->registerAppProviders();
        static::assertArrayNotHasKey('testRegisterProvidersBootstrap', $_SERVER);
        static::assertArrayNotHasKey('testRegisterProvidersRegister', $_SERVER);
    }

    #[Api([
        'zh-CN:title' => 'setThemesPath 设置主题路径',
    ])]
    public function testSetThemesPath(): void
    {
        $app = $this->createApp();
        static::assertSame(__DIR__.'/app/assets/themes', $app->themesPath());
        $app->setThemesPath(__DIR__.'/hello');
        static::assertSame(__DIR__.'/hello', $app->themesPath());
    }

    /**
     * @dataProvider envProvider
     *
     * @param mixed $value
     * @param mixed $envValue
     */
    #[Api([
        'zh-CN:title' => 'env 获取应用的环境变量',
        'zh-CN:description' => <<<'EOT'
测试数据

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Kernel\AppTest::class, 'envProvider')]}
```
EOT,
    ])]
    public function testEnv(string $name, $value, $envValue): void
    {
        $app = $this->createApp();
        $name = 'test_env_'.$name;
        putenv($name.'='.$value);
        static::assertSame($envValue, $app->env($name));
    }

    public static function envProvider(): array
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
        static::assertSame('default message', $app->env('not_found_env', 'default message'));
        static::assertNull($app->env('not_found_env'));
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

    public function bootstrap(): void
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
    public function handle(): void
    {
        $_SERVER['bootstrapTest1'] = 1;
    }
}

class BootstrapTest2
{
    public function handle(): void
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
