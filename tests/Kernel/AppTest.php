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
use Leevel\Filesystem\Fso;
use Leevel\Http\IRequest;
use Leevel\Kernel\App as Apps;
use Leevel\Kernel\IApp;
use Leevel\Option\IOption;
use Tests\TestCase;

/**
 * app test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.19
 *
 * @version 1.0
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

    public function testConsole(): void
    {
        $app = $this->createApp();

        $this->assertTrue($app->console());
    }

    public function testConsole2(): void
    {
        $app = $this->createApp();

        $request = $this->createMock(IRequest::class);

        $request->method('isCli')->willReturn(true);
        $this->assertTrue($request->isCli());

        $app->container()->singleton('request', function () use ($request) {
            return $request;
        });

        $this->assertTrue($app->console());
    }

    public function testConsole3(): void
    {
        $app = $this->createApp();
        $container = $app->container();

        $request = $this->createMock(IRequest::class);
        $request->method('isCli')->willReturn(false);
        $this->assertFalse($request->isCli());
        $container->singleton('request', function () use ($request) {
            return $request;
        });

        $this->assertFalse($app->console());
    }

    public function testSetPath(): void
    {
        $app = $this->createApp();

        $app->setPath(__DIR__.'/foo');

        $this->assertSame(__DIR__.'/foo', $app->path());
    }

    public function testAppPath(): void
    {
        $appPath = __DIR__.'/app';
        $app = $this->createApp();
        $container = $app->container();

        $request = $this->createMock(IRequest::class);
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

        $request = $this->createMock(IRequest::class);
        $container->singleton('request', function () use ($request) {
            return $request;
        });

        $container->instance('app_name', '');
        $this->assertEquals('', $container->make('app_name'));
        $this->assertSame($appPath.'/application/app', $app->appPath(true));
    }

    public function testSetAppPath(): void
    {
        $appPath = __DIR__.'/app';
        $app = $this->createApp();
        $container = $app->container();

        $request = $this->createMock(IRequest::class);
        $container->singleton('request', function () use ($request) {
            return $request;
        });

        $container->instance('app_name', 'Blog');
        $this->assertEquals('Blog', $container->make('app_name'));
        $this->assertSame($appPath.'/application/blog', $app->appPath(true));

        $app->setAppPath(__DIR__.'/app/foo');
        $this->assertSame($appPath.'/foo/blog', $app->appPath(true));
    }

    public function testPathTheme(): void
    {
        $app = $this->createApp();
        $appPath = __DIR__.'/app';

        $this->assertSame($appPath.'/application/ui/theme', $app->themePath());
        $this->assertSame($appPath.'/application/blog/ui/theme', $app->themePath('blog'));
    }

    public function testCommonPath(): void
    {
        $app = $this->createApp();
        $appPath = __DIR__.'/app';

        $this->assertSame($appPath.'/common', $app->commonPath());
        $this->assertSame($appPath.'/common/foobar', $app->commonPath('foobar'));
    }

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

    public function testRuntimePath(): void
    {
        $app = $this->createApp();
        $appPath = __DIR__.'/app';

        $this->assertSame($appPath.'/runtime', $app->runtimePath());
        $this->assertSame($appPath.'/runtime/foobar', $app->runtimePath('foobar'));
    }

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

    public function testStoragePath(): void
    {
        $app = $this->createApp();
        $appPath = __DIR__.'/app';

        $this->assertSame($appPath.'/storage', $app->storagePath());
        $this->assertSame($appPath.'/storage/foobar', $app->storagePath('foobar'));
    }

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

    public function testOptionPath(): void
    {
        $app = $this->createApp();
        $appPath = __DIR__.'/app';

        $this->assertSame($appPath.'/option', $app->optionPath());
        $this->assertSame($appPath.'/option/foobar', $app->optionPath('foobar'));
    }

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

    public function testI18nPath(): void
    {
        $app = $this->createApp();
        $appPath = __DIR__.'/app';

        $this->assertSame($appPath.'/i18n', $app->i18nPath());
        $this->assertSame($appPath.'/i18n/foobar', $app->i18nPath('foobar'));
    }

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

    public function testEnvPath(): void
    {
        $app = $this->createApp();
        $appPath = __DIR__.'/app';

        $this->assertSame($appPath, $app->envPath());
    }

    public function testSetEnvPath(): void
    {
        $app = $this->createApp();
        $appPath = __DIR__.'/app';

        $this->assertSame($appPath, $app->envPath());

        $app->setEnvPath(__DIR__.'/appFoo');

        $this->assertSame(__DIR__.'/appFoo', $app->envPath());
    }

    public function testEnvFile(): void
    {
        $app = $this->createApp();

        $this->assertSame('.env', $app->envFile());
    }

    public function testSetEnvFile(): void
    {
        $app = $this->createApp();

        $this->assertSame('.env', $app->envFile());

        $app->setEnvFile('.envfoo');

        $this->assertSame('.envfoo', $app->envFile());
    }

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

    public function testI18nCachedPath(): void
    {
        $app = $this->createApp();
        $appPath = __DIR__.'/app';

        $this->assertSame($appPath.'/bootstrap/i18n/zh-CN.php', $app->i18nCachedPath('zh-CN'));
        $this->assertSame($appPath.'/bootstrap/i18n/zh-TW.php', $app->i18nCachedPath('zh-TW'));
        $this->assertSame($appPath.'/bootstrap/i18n/en-US.php', $app->i18nCachedPath('en-US'));
    }

    public function testIsCachedI18n(): void
    {
        $app = $this->createApp();
        $appPath = __DIR__.'/app';

        $this->assertFalse($app->isCachedI18n('zh-CN'));

        mkdir($appPath.'/bootstrap/i18n', 0777, true);

        file_put_contents($langPath = $appPath.'/bootstrap/i18n/zh-CN.php', 'foo');

        $this->assertTrue($app->isCachedI18n('zh-CN'));

        Fso::deleteDirectory($appPath, true);
    }

    public function testOptionCachedPath(): void
    {
        $app = $this->createApp();
        $appPath = __DIR__.'/app';

        $this->assertSame($appPath.'/bootstrap/option.php', $app->optionCachedPath());
    }

    public function testIsCachedOption(): void
    {
        $appPath = __DIR__.'/app';
        $app = $this->createApp();

        $this->assertFalse($app->isCachedOption());

        mkdir($appPath.'/bootstrap', 0777, true);

        file_put_contents($optionPath = $appPath.'/bootstrap/option.php', 'foo');

        $this->assertTrue($app->isCachedOption());

        Fso::deleteDirectory($appPath, true);
    }

    public function testRouterCachedPath(): void
    {
        $appPath = __DIR__.'/app';
        $app = $this->createApp();

        $this->assertSame($appPath.'/bootstrap/router.php', $app->routerCachedPath());
    }

    public function testIsCachedRouter(): void
    {
        $appPath = __DIR__.'/app';
        $app = $this->createApp();

        $this->assertFalse($app->isCachedRouter());

        mkdir($appPath.'/bootstrap', 0777, true);

        file_put_contents($routerPath = $appPath.'/bootstrap/router.php', 'foo');

        $this->assertTrue($app->isCachedRouter());

        Fso::deleteDirectory($appPath, true);
    }

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

        $this->assertTrue($app->debug());
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

        $this->assertFalse($app->debug());
    }

    public function testDevelopment(): void
    {
        $appPath = __DIR__.'/app';
        $app = $this->createApp();
        $container = $app->container();

        $option = $this->createMock(IRequest::class);

        $option->method('get')->willReturn('development');
        $this->assertEquals('development', $option->get('development'));

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        $this->assertTrue($app->development());
    }

    public function testDevelopment2(): void
    {
        $appPath = __DIR__.'/app';
        $app = $this->createApp();
        $container = $app->container();

        $option = $this->createMock(IRequest::class);

        $option->method('get')->willReturn('foo');
        $this->assertEquals('foo', $option->get('development'));

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        $this->assertFalse($app->development());
    }

    public function testEnvironment(): void
    {
        $appPath = __DIR__.'/app';
        $app = $this->createApp();
        $container = $app->container();

        $option = $this->createMock(IRequest::class);

        $option->method('get')->willReturn('foo');
        $this->assertEquals('foo', $option->get('development'));

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        $this->assertSame('foo', $app->environment());
    }

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

    public function testSetPublicPath(): void
    {
        $app = $this->createApp();
        $this->assertSame(__DIR__.'/app/public', $app->publicPath());
        $app->setPublicPath(__DIR__.'/hello');
        $this->assertSame(__DIR__.'/hello', $app->publicPath());
    }

    public function testSetThemesPath(): void
    {
        $app = $this->createApp();
        $this->assertSame(__DIR__.'/app/themes', $app->themesPath());
        $app->setThemesPath(__DIR__.'/hello');
        $this->assertSame(__DIR__.'/hello', $app->themesPath());
    }

    public function testSetI18nCachePath(): void
    {
        $app = $this->createApp();
        $this->assertSame(__DIR__.'/app/bootstrap/i18n/zh-CN.php', $app->i18nCachedPath('zh-CN'));
        $app->setI18nCachedPath(__DIR__.'/hello');
        $this->assertSame(__DIR__.'/hello/zh-CN.php', $app->i18nCachedPath('zh-CN'));
    }

    public function testSetOptionCachePath(): void
    {
        $app = $this->createApp();
        $this->assertSame(__DIR__.'/app/bootstrap/option.php', $app->optionCachedPath());
        $app->setOptionCachedPath(__DIR__.'/hello');
        $this->assertSame(__DIR__.'/hello/option.php', $app->optionCachedPath());
    }

    /**
     * @dataProvider envProvider
     *
     * @param mixed $value
     * @param mixed $envValue
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

class ProviderTest1 extends Provider
{
    public function __construct(IContainer $container)
    {
        $_SERVER['testMakeProvider'] = 1;
    }

    public function register(): void
    {
    }
}

class ProviderTest2 extends Provider
{
    public function __construct(IContainer $container)
    {
    }

    public function bootstrap()
    {
        $_SERVER['testCallProviderBootstrap'] = 1;
    }

    public function register(): void
    {
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
