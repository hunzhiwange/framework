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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Leevel;

use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Di\Provider;
use Leevel\Filesystem\Fso;
use Leevel\Http\IRequest;
use Leevel\Leevel\App as Apps;
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
    public function testBaseUse()
    {
        $app = new App($appPath = __DIR__.'/app');

        $this->assertInstanceof(IContainer::class, $app);
        $this->assertInstanceof(Container::class, $app);

        $this->assertSame($appPath, $app->path());
        $this->assertSame($appPath.'/foobar', $app->path('foobar'));
    }

    public function testClone()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'App disallowed clone.'
        );

        $app = new App($appPath = __DIR__.'/app');

        $app2 = clone $app;
    }

    public function testVersion()
    {
        $app = new App($appPath = __DIR__.'/app');

        $this->assertSame(App::VERSION, $app->version());
    }

    public function testRunWithExtension()
    {
        $app = new App($appPath = __DIR__.'/app');

        if (extension_loaded('leevel')) {
            $this->assertTrue($app->runWithExtension());
        } else {
            $this->assertFalse($app->runWithExtension());
        }
    }

    public function testConsole()
    {
        $app = new App($appPath = __DIR__.'/app');

        $this->assertTrue($app->console());
    }

    public function testConsole2()
    {
        $app = new App($appPath = __DIR__.'/app');

        $request = $this->createMock(IRequest::class);

        $request->method('isCli')->willReturn(true);
        $this->assertTrue($request->isCli());

        $app->singleton('request', function () use ($request) {
            return $request;
        });

        $this->assertTrue($app->console());
    }

    public function testConsole3()
    {
        $app = new App($appPath = __DIR__.'/app');

        $request = $this->createMock(IRequest::class);

        $request->method('isCli')->willReturn(false);
        $this->assertFalse($request->isCli());

        $app->singleton('request', function () use ($request) {
            return $request;
        });

        $this->assertFalse($app->console());
    }

    public function testSetPath()
    {
        $app = new App($appPath = __DIR__.'/app');

        $app->setPath(__DIR__.'/foo');

        $this->assertSame(__DIR__.'/foo', $app->path());
    }

    public function testAppPath()
    {
        $app = new App($appPath = __DIR__.'/app');

        $request = $this->createMock(IRequest::class);

        $app->singleton('request', function () use ($request) {
            return $request;
        });

        $app->instance('app_name', 'Blog');
        $this->assertEquals('Blog', $app->make('app_name'));

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

    public function testAppPath2()
    {
        $app = new App($appPath = __DIR__.'/app');

        $request = $this->createMock(IRequest::class);

        $app->singleton('request', function () use ($request) {
            return $request;
        });

        $app->instance('app_name', '');
        $this->assertEquals('', $app->make('app_name'));

        $this->assertSame($appPath.'/application/app', $app->appPath(true));
    }

    public function testSetAppPath()
    {
        $app = new App($appPath = __DIR__.'/app');

        $request = $this->createMock(IRequest::class);

        $app->singleton('request', function () use ($request) {
            return $request;
        });

        $app->instance('app_name', 'Blog');
        $this->assertEquals('Blog', $app->make('app_name'));

        $this->assertSame($appPath.'/application/blog', $app->appPath(true));

        $app->setAppPath(__DIR__.'/app/foo');

        $this->assertSame($appPath.'/foo/blog', $app->appPath(true));
    }

    public function testPathTheme()
    {
        $app = new App($appPath = __DIR__.'/app');

        $this->assertSame($appPath.'/application/ui/theme', $app->themePath());
        $this->assertSame($appPath.'/application/blog/ui/theme', $app->themePath('blog'));
    }

    public function testCommonPath()
    {
        $app = new App($appPath = __DIR__.'/app');

        $this->assertSame($appPath.'/common', $app->commonPath());
        $this->assertSame($appPath.'/common/foobar', $app->commonPath('foobar'));
    }

    public function testSetCommonPath()
    {
        $app = new App($appPath = __DIR__.'/app');

        $this->assertSame($appPath.'/common', $app->commonPath());
        $this->assertSame($appPath.'/common/foobar', $app->commonPath('foobar'));

        $app->setCommonPath(__DIR__.'/app/commonFoo');

        $this->assertSame($appPath.'/commonFoo', $app->commonPath());
        $this->assertSame($appPath.'/commonFoo/foobar', $app->commonPath('foobar'));
    }

    public function testRuntimePath()
    {
        $app = new App($appPath = __DIR__.'/app');

        $this->assertSame($appPath.'/runtime', $app->runtimePath());
        $this->assertSame($appPath.'/runtime/foobar', $app->runtimePath('foobar'));
    }

    public function testSetRuntimePath()
    {
        $app = new App($appPath = __DIR__.'/app');

        $this->assertSame($appPath.'/runtime', $app->runtimePath());
        $this->assertSame($appPath.'/runtime/foobar', $app->runtimePath('foobar'));

        $app->setRuntimePath(__DIR__.'/app/runtimeFoo');

        $this->assertSame($appPath.'/runtimeFoo', $app->runtimePath());
        $this->assertSame($appPath.'/runtimeFoo/foobar', $app->runtimePath('foobar'));
    }

    public function testStoragePath()
    {
        $app = new App($appPath = __DIR__.'/app');

        $this->assertSame($appPath.'/storage', $app->storagePath());
        $this->assertSame($appPath.'/storage/foobar', $app->storagePath('foobar'));
    }

    public function testSetStoragePath()
    {
        $app = new App($appPath = __DIR__.'/app');

        $this->assertSame($appPath.'/storage', $app->storagePath());
        $this->assertSame($appPath.'/storage/foobar', $app->storagePath('foobar'));

        $app->setStoragePath(__DIR__.'/app/storageFoo');

        $this->assertSame($appPath.'/storageFoo', $app->storagePath());
        $this->assertSame($appPath.'/storageFoo/foobar', $app->storagePath('foobar'));
    }

    public function testOptionPath()
    {
        $app = new App($appPath = __DIR__.'/app');

        $this->assertSame($appPath.'/option', $app->optionPath());
        $this->assertSame($appPath.'/option/foobar', $app->optionPath('foobar'));
    }

    public function testSetOptionPath()
    {
        $app = new App($appPath = __DIR__.'/app');

        $this->assertSame($appPath.'/option', $app->optionPath());
        $this->assertSame($appPath.'/option/foobar', $app->optionPath('foobar'));

        $app->setOptionPath(__DIR__.'/app/optionFoo');

        $this->assertSame($appPath.'/optionFoo', $app->optionPath());
        $this->assertSame($appPath.'/optionFoo/foobar', $app->optionPath('foobar'));
    }

    public function testI18nPath()
    {
        $app = new App($appPath = __DIR__.'/app');

        $this->assertSame($appPath.'/i18n', $app->i18nPath());
        $this->assertSame($appPath.'/i18n/foobar', $app->i18nPath('foobar'));
    }

    public function testSetI18nPath()
    {
        $app = new App($appPath = __DIR__.'/app');

        $this->assertSame($appPath.'/i18n', $app->i18nPath());
        $this->assertSame($appPath.'/i18n/foobar', $app->i18nPath('foobar'));

        $app->setI18nPath(__DIR__.'/app/i18nFoo');

        $this->assertSame($appPath.'/i18nFoo', $app->i18nPath());
        $this->assertSame($appPath.'/i18nFoo/foobar', $app->i18nPath('foobar'));
    }

    public function testEnvPath()
    {
        $app = new App($appPath = __DIR__.'/app');

        $this->assertSame($appPath, $app->envPath());
    }

    public function testSetEnvPath()
    {
        $app = new App($appPath = __DIR__.'/app');

        $this->assertSame($appPath, $app->envPath());

        $app->setEnvPath(__DIR__.'/appFoo');

        $this->assertSame(__DIR__.'/appFoo', $app->envPath());
    }

    public function testEnvFile()
    {
        $app = new App($appPath = __DIR__.'/app');

        $this->assertSame('.env', $app->envFile());
    }

    public function testSetEnvFile()
    {
        $app = new App($appPath = __DIR__.'/app');

        $this->assertSame('.env', $app->envFile());

        $app->setEnvFile('.envfoo');

        $this->assertSame('.envfoo', $app->envFile());
    }

    public function testFullEnvPath()
    {
        $app = new App($appPath = __DIR__.'/app');

        $this->assertSame($appPath.'/.env', $app->fullEnvPath());

        $app->setEnvPath(__DIR__.'/appFoo');

        $this->assertSame(__DIR__.'/appFoo/.env', $app->fullEnvPath());

        $app->setEnvFile('.envfoo');

        $this->assertSame(__DIR__.'/appFoo/.envfoo', $app->fullEnvPath());
    }

    public function testI18nCachedPath()
    {
        $app = new App($appPath = __DIR__.'/app');

        $this->assertSame($appPath.'/bootstrap/i18n/zh-CN.php', $app->i18nCachedPath('zh-CN'));
        $this->assertSame($appPath.'/bootstrap/i18n/zh-TW.php', $app->i18nCachedPath('zh-TW'));
        $this->assertSame($appPath.'/bootstrap/i18n/en-US.php', $app->i18nCachedPath('en-US'));
    }

    public function testIsCachedI18n()
    {
        $app = new App($appPath = __DIR__.'/app');

        $this->assertFalse($app->isCachedI18n('zh-CN'));

        mkdir($appPath.'/bootstrap/i18n', 0777, true);

        file_put_contents($langPath = $appPath.'/bootstrap/i18n/zh-CN.php', 'foo');

        $this->assertTrue($app->isCachedI18n('zh-CN'));

        Fso::deleteDirectory($appPath, true);
    }

    public function testOptionCachedPath()
    {
        $app = new App($appPath = __DIR__.'/app');

        $this->assertSame($appPath.'/bootstrap/option.php', $app->optionCachedPath());
    }

    public function testIsCachedOption()
    {
        $app = new App($appPath = __DIR__.'/app');

        $this->assertFalse($app->isCachedOption());

        mkdir($appPath.'/bootstrap', 0777, true);

        file_put_contents($optionPath = $appPath.'/bootstrap/option.php', 'foo');

        $this->assertTrue($app->isCachedOption());

        Fso::deleteDirectory($appPath, true);
    }

    public function testRouterCachedPath()
    {
        $app = new App($appPath = __DIR__.'/app');

        $this->assertSame($appPath.'/bootstrap/router.php', $app->routerCachedPath());
    }

    public function testIsCachedRouter()
    {
        $app = new App($appPath = __DIR__.'/app');

        $this->assertFalse($app->isCachedRouter());

        mkdir($appPath.'/bootstrap', 0777, true);

        file_put_contents($routerPath = $appPath.'/bootstrap/router.php', 'foo');

        $this->assertTrue($app->isCachedRouter());

        Fso::deleteDirectory($appPath, true);
    }

    public function testDebug()
    {
        $app = new App($appPath = __DIR__.'/app');

        $option = $this->createMock(IRequest::class);

        $option->method('get')->willReturn(true);
        $this->assertTrue($option->get('debug'));

        $app->singleton('option', function () use ($option) {
            return $option;
        });

        $this->assertTrue($app->debug());
    }

    public function testDebug2()
    {
        $app = new App($appPath = __DIR__.'/app');

        $option = $this->createMock(IRequest::class);

        $option->method('get')->willReturn(false);
        $this->assertFalse($option->get('debug'));

        $app->singleton('option', function () use ($option) {
            return $option;
        });

        $this->assertFalse($app->debug());
    }

    public function testDevelopment()
    {
        $app = new App($appPath = __DIR__.'/app');

        $option = $this->createMock(IRequest::class);

        $option->method('get')->willReturn('development');
        $this->assertEquals('development', $option->get('development'));

        $app->singleton('option', function () use ($option) {
            return $option;
        });

        $this->assertTrue($app->development());
    }

    public function testDevelopment2()
    {
        $app = new App($appPath = __DIR__.'/app');

        $option = $this->createMock(IRequest::class);

        $option->method('get')->willReturn('foo');
        $this->assertEquals('foo', $option->get('development'));

        $app->singleton('option', function () use ($option) {
            return $option;
        });

        $this->assertFalse($app->development());
    }

    public function testEnvironment()
    {
        $app = new App($appPath = __DIR__.'/app');

        $option = $this->createMock(IRequest::class);

        $option->method('get')->willReturn('foo');
        $this->assertEquals('foo', $option->get('development'));

        $app->singleton('option', function () use ($option) {
            return $option;
        });

        $this->assertSame('foo', $app->environment());
    }

    public function testMakeProvider()
    {
        $app = new App($appPath = __DIR__.'/app');

        $app->makeProvider(ProviderTest1::class);

        $this->assertSame(1, $_SERVER['testMakeProvider']);

        unset($_SERVER['testMakeProvider']);
    }

    public function testCallProviderBootstrap()
    {
        $app = new App($appPath = __DIR__.'/app');

        $app->callProviderBootstrap(new ProviderTest1($app));

        $this->assertSame(1, $_SERVER['testMakeProvider']);

        unset($_SERVER['testMakeProvider']);

        $app->callProviderBootstrap(new ProviderTest2($app));

        $this->assertSame(1, $_SERVER['testCallProviderBootstrap']);

        unset($_SERVER['testCallProviderBootstrap']);
    }

    public function testBootstrap()
    {
        $app = new App($appPath = __DIR__.'/app');

        $app->bootstrap([BootstrapTest1::class, BootstrapTest2::class]);

        $this->assertSame(1, $_SERVER['bootstrapTest1']);
        $this->assertSame(1, $_SERVER['bootstrapTest2']);

        unset($_SERVER['bootstrapTest1'], $_SERVER['bootstrapTest2']);
    }

    public function testBootstrap2()
    {
        $app = new App($appPath = __DIR__.'/app');

        $app->bootstrap([BootstrapTest1::class, BootstrapTest2::class]);

        $this->assertFalse($app->isBootstrap());

        $this->assertSame(1, $_SERVER['bootstrapTest1']);
        $this->assertSame(1, $_SERVER['bootstrapTest2']);

        unset($_SERVER['bootstrapTest1'], $_SERVER['bootstrapTest2']);

        $app->bootstrapProviders();

        $this->assertTrue($app->isBootstrap());

        $app->bootstrap([BootstrapTest1::class, BootstrapTest2::class]);

        $this->assertArrayNotHasKey('bootstrapTest1', $_SERVER);
        $this->assertArrayNotHasKey('bootstrapTest2', $_SERVER);
    }

    public function testRegisterProviders()
    {
        $app = new App($appPath = __DIR__.'/app');

        $option = new OptionTest();

        $app->singleton('option', function () use ($option) {
            return $option;
        });

        $app->registerProviders();

        // for deferredAlias
        $this->assertArrayNotHasKey('providerDeferTest1', $_SERVER);
        $this->assertSame('bar', $app->make('foo'));
        $this->assertSame('bar', $app->make(ProviderDeferTest1::class));
        $this->assertSame(1, $_SERVER['providerDeferTest1']);

        // for providers
        $this->assertSame(1, $_SERVER['testRegisterProvidersRegister']);
        $this->assertArrayNotHasKey('testRegisterProvidersBootstrap', $_SERVER);

        unset(
            $_SERVER['providerDeferTest1'],
            $_SERVER['testRegisterProvidersRegister']
        );

        // bootstrap
        $this->assertFalse($app->isBootstrap());
        $app->bootstrapProviders();
        $this->assertSame(1, $_SERVER['testRegisterProvidersBootstrap']);
        unset($_SERVER['testRegisterProvidersBootstrap']);
        $this->assertTrue($app->isBootstrap());

        // bootstrap again but already bootstrap
        $app->bootstrapProviders();
        $this->assertArrayNotHasKey('testRegisterProvidersBootstrap', $_SERVER);

        // again but already bootstrap
        $app->registerProviders();
        $this->assertArrayNotHasKey('testRegisterProvidersRegister', $_SERVER);
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
    public function __construct(App $app)
    {
        $_SERVER['testMakeProvider'] = 1;
    }

    public function register(): void
    {
    }
}

class ProviderTest2 extends Provider
{
    public function __construct(App $app)
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
                    'foo' => 'Tests\\Leevel\\ProviderDeferTest1',
                ],
                [
                    'Tests\\Leevel\\ProviderDeferTest1' => [
                        'foo' => [
                            'Tests\\Leevel\\ProviderDeferTest1',
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
                'Tests\\Leevel\\ProviderDeferTest1',
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
    public function __construct(App $app)
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
