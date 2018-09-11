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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Bootstrap;

use Leevel\Bootstrap\Project as Projects;
use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Di\Provider;
use Leevel\Filesystem\Fso;
use Leevel\Http\IRequest;
use Tests\TestCase;

/**
 * project test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.19
 *
 * @version 1.0
 */
class ProjectTest extends TestCase
{
    public function testBaseUse()
    {
        $project = new Project($appPath = __DIR__.'/app');

        $this->assertInstanceof(IContainer::class, $project);
        $this->assertInstanceof(Container::class, $project);

        $this->assertSame($appPath, $project->path());
        $this->assertSame($appPath.'/foobar', $project->path('foobar'));
    }

    public function testClone()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Project disallowed clone.'
        );

        $project = new Project($appPath = __DIR__.'/app');

        $project2 = clone $project;
    }

    public function testVersion()
    {
        $project = new Project($appPath = __DIR__.'/app');

        $this->assertSame(Project::VERSION, $project->version());
    }

    public function testRunWithExtension()
    {
        $project = new Project($appPath = __DIR__.'/app');

        if (extension_loaded('leevel')) {
            $this->assertTrue($project->runWithExtension());
        } else {
            $this->assertFalse($project->runWithExtension());
        }
    }

    public function testConsole()
    {
        $project = new Project($appPath = __DIR__.'/app');

        $this->assertTrue($project->console());
    }

    public function testConsole2()
    {
        $project = new Project($appPath = __DIR__.'/app');

        $request = $this->createMock(IRequest::class);

        $request->method('isCli')->willReturn(true);
        $this->assertTrue($request->isCli());

        $project->singleton('request', function () use ($request) {
            return $request;
        });

        $this->assertTrue($project->console());
    }

    public function testConsole3()
    {
        $project = new Project($appPath = __DIR__.'/app');

        $request = $this->createMock(IRequest::class);

        $request->method('isCli')->willReturn(false);
        $this->assertFalse($request->isCli());

        $project->singleton('request', function () use ($request) {
            return $request;
        });

        $this->assertFalse($project->console());
    }

    public function testSetPath()
    {
        $project = new Project($appPath = __DIR__.'/app');

        $project->setPath(__DIR__.'/foo');

        $this->assertSame(__DIR__.'/foo', $project->path());
    }

    public function testAppPath()
    {
        $project = new Project($appPath = __DIR__.'/app');

        $request = $this->createMock(IRequest::class);

        $request->method('app')->willReturn('blog');
        $this->assertEquals('blog', $request->app());

        $project->singleton('request', function () use ($request) {
            return $request;
        });

        $this->assertSame($appPath.'/application', $project->appPath());
        $this->assertSame($appPath.'/application', $project->appPath(false));
        $this->assertSame($appPath.'/application', $project->appPath(''));
        $this->assertSame($appPath.'/application/blog', $project->appPath(true));
        $this->assertSame($appPath.'/application/foo', $project->appPath('foo'));
        $this->assertSame($appPath.'/application/bar', $project->appPath('bar'));

        $this->assertSame($appPath.'/application/foo/bar', $project->appPath(false, 'foo/bar'));
        $this->assertSame($appPath.'/application/foo/bar', $project->appPath('', 'foo/bar'));
        $this->assertSame($appPath.'/application/blog/foo/bar', $project->appPath(true, 'foo/bar'));
        $this->assertSame($appPath.'/application/foo/foo/bar', $project->appPath('foo', 'foo/bar'));
        $this->assertSame($appPath.'/application/bar/foo/bar', $project->appPath('bar', 'foo/bar'));
    }

    public function testAppPath2()
    {
        $project = new Project($appPath = __DIR__.'/app');

        $request = $this->createMock(IRequest::class);

        $request->method('app')->willReturn('');
        $this->assertEquals('', $request->app());

        $project->singleton('request', function () use ($request) {
            return $request;
        });

        $this->assertSame($appPath.'/application/app', $project->appPath(true));
    }

    public function testSetAppPath()
    {
        $project = new Project($appPath = __DIR__.'/app');

        $request = $this->createMock(IRequest::class);

        $request->method('app')->willReturn('blog');
        $this->assertEquals('blog', $request->app());

        $project->singleton('request', function () use ($request) {
            return $request;
        });

        $this->assertSame($appPath.'/application/blog', $project->appPath(true));

        $project->setAppPath(__DIR__.'/app/foo');

        $this->assertSame($appPath.'/foo/blog', $project->appPath(true));
    }

    public function testPathTheme()
    {
        $project = new Project($appPath = __DIR__.'/app');

        $this->assertSame($appPath.'/application/ui/theme', $project->themePath());
        $this->assertSame($appPath.'/application/blog/ui/theme', $project->themePath('blog'));
    }

    public function testCommonPath()
    {
        $project = new Project($appPath = __DIR__.'/app');

        $this->assertSame($appPath.'/common', $project->commonPath());
        $this->assertSame($appPath.'/common/foobar', $project->commonPath('foobar'));
    }

    public function testSetCommonPath()
    {
        $project = new Project($appPath = __DIR__.'/app');

        $this->assertSame($appPath.'/common', $project->commonPath());
        $this->assertSame($appPath.'/common/foobar', $project->commonPath('foobar'));

        $project->setCommonPath(__DIR__.'/app/commonFoo');

        $this->assertSame($appPath.'/commonFoo', $project->commonPath());
        $this->assertSame($appPath.'/commonFoo/foobar', $project->commonPath('foobar'));
    }

    public function testRuntimePath()
    {
        $project = new Project($appPath = __DIR__.'/app');

        $this->assertSame($appPath.'/runtime', $project->runtimePath());
        $this->assertSame($appPath.'/runtime/foobar', $project->runtimePath('foobar'));
    }

    public function testSetRuntimePath()
    {
        $project = new Project($appPath = __DIR__.'/app');

        $this->assertSame($appPath.'/runtime', $project->runtimePath());
        $this->assertSame($appPath.'/runtime/foobar', $project->runtimePath('foobar'));

        $project->setRuntimePath(__DIR__.'/app/runtimeFoo');

        $this->assertSame($appPath.'/runtimeFoo', $project->runtimePath());
        $this->assertSame($appPath.'/runtimeFoo/foobar', $project->runtimePath('foobar'));
    }

    public function testStoragePath()
    {
        $project = new Project($appPath = __DIR__.'/app');

        $this->assertSame($appPath.'/storage', $project->storagePath());
        $this->assertSame($appPath.'/storage/foobar', $project->storagePath('foobar'));
    }

    public function testSetStoragePath()
    {
        $project = new Project($appPath = __DIR__.'/app');

        $this->assertSame($appPath.'/storage', $project->storagePath());
        $this->assertSame($appPath.'/storage/foobar', $project->storagePath('foobar'));

        $project->setStoragePath(__DIR__.'/app/storageFoo');

        $this->assertSame($appPath.'/storageFoo', $project->storagePath());
        $this->assertSame($appPath.'/storageFoo/foobar', $project->storagePath('foobar'));
    }

    public function testOptionPath()
    {
        $project = new Project($appPath = __DIR__.'/app');

        $this->assertSame($appPath.'/option', $project->optionPath());
        $this->assertSame($appPath.'/option/foobar', $project->optionPath('foobar'));
    }

    public function testSetOptionPath()
    {
        $project = new Project($appPath = __DIR__.'/app');

        $this->assertSame($appPath.'/option', $project->optionPath());
        $this->assertSame($appPath.'/option/foobar', $project->optionPath('foobar'));

        $project->setOptionPath(__DIR__.'/app/optionFoo');

        $this->assertSame($appPath.'/optionFoo', $project->optionPath());
        $this->assertSame($appPath.'/optionFoo/foobar', $project->optionPath('foobar'));
    }

    public function testI18nPath()
    {
        $project = new Project($appPath = __DIR__.'/app');

        $this->assertSame($appPath.'/i18n', $project->i18nPath());
        $this->assertSame($appPath.'/i18n/foobar', $project->i18nPath('foobar'));
    }

    public function testSetI18nPath()
    {
        $project = new Project($appPath = __DIR__.'/app');

        $this->assertSame($appPath.'/i18n', $project->i18nPath());
        $this->assertSame($appPath.'/i18n/foobar', $project->i18nPath('foobar'));

        $project->setI18nPath(__DIR__.'/app/i18nFoo');

        $this->assertSame($appPath.'/i18nFoo', $project->i18nPath());
        $this->assertSame($appPath.'/i18nFoo/foobar', $project->i18nPath('foobar'));
    }

    public function testEnvPath()
    {
        $project = new Project($appPath = __DIR__.'/app');

        $this->assertSame($appPath, $project->envPath());
    }

    public function testSetEnvPath()
    {
        $project = new Project($appPath = __DIR__.'/app');

        $this->assertSame($appPath, $project->envPath());

        $project->setEnvPath(__DIR__.'/appFoo');

        $this->assertSame(__DIR__.'/appFoo', $project->envPath());
    }

    public function testEnvFile()
    {
        $project = new Project($appPath = __DIR__.'/app');

        $this->assertSame('.env', $project->envFile());
    }

    public function testSetEnvFile()
    {
        $project = new Project($appPath = __DIR__.'/app');

        $this->assertSame('.env', $project->envFile());

        $project->setEnvFile('.envfoo');

        $this->assertSame('.envfoo', $project->envFile());
    }

    public function testFullEnvPath()
    {
        $project = new Project($appPath = __DIR__.'/app');

        $this->assertSame($appPath.'/.env', $project->fullEnvPath());

        $project->setEnvPath(__DIR__.'/appFoo');

        $this->assertSame(__DIR__.'/appFoo/.env', $project->fullEnvPath());

        $project->setEnvFile('.envfoo');

        $this->assertSame(__DIR__.'/appFoo/.envfoo', $project->fullEnvPath());
    }

    public function testI18nCachedPath()
    {
        $project = new Project($appPath = __DIR__.'/app');

        $this->assertSame($appPath.'/runtime/i18n/zh-CN.php', $project->i18nCachedPath('zh-CN'));
        $this->assertSame($appPath.'/runtime/i18n/zh-TW.php', $project->i18nCachedPath('zh-TW'));
        $this->assertSame($appPath.'/runtime/i18n/en-US.php', $project->i18nCachedPath('en-US'));
    }

    public function testIsCachedI18n()
    {
        $project = new Project($appPath = __DIR__.'/app');

        $this->assertFalse($project->isCachedI18n('zh-CN'));

        mkdir($appPath.'/runtime/i18n', 0777, true);

        file_put_contents($langPath = $appPath.'/runtime/i18n/zh-CN.php', 'foo');

        $this->assertTrue($project->isCachedI18n('zh-CN'));

        Fso::deleteDirectory($appPath, true);
    }

    public function testOptionCachedPath()
    {
        $project = new Project($appPath = __DIR__.'/app');

        $this->assertSame($appPath.'/runtime/bootstrap/option.php', $project->optionCachedPath());
    }

    public function testIsCachedOption()
    {
        $project = new Project($appPath = __DIR__.'/app');

        $this->assertFalse($project->isCachedOption());

        mkdir($appPath.'/runtime/bootstrap', 0777, true);

        file_put_contents($optionPath = $appPath.'/runtime/bootstrap/option.php', 'foo');

        $this->assertTrue($project->isCachedOption());

        Fso::deleteDirectory($appPath, true);
    }

    public function testRouterCachedPath()
    {
        $project = new Project($appPath = __DIR__.'/app');

        $this->assertSame($appPath.'/runtime/bootstrap/router.php', $project->routerCachedPath());
    }

    public function testIsCachedRouter()
    {
        $project = new Project($appPath = __DIR__.'/app');

        $this->assertFalse($project->isCachedRouter());

        mkdir($appPath.'/runtime/bootstrap', 0777, true);

        file_put_contents($routerPath = $appPath.'/runtime/bootstrap/router.php', 'foo');

        $this->assertTrue($project->isCachedRouter());

        Fso::deleteDirectory($appPath, true);
    }

    public function testDebug()
    {
        $project = new Project($appPath = __DIR__.'/app');

        $option = $this->createMock(IRequest::class);

        $option->method('get')->willReturn(true);
        $this->assertTrue($option->get('debug'));

        $project->singleton('option', function () use ($option) {
            return $option;
        });

        $this->assertTrue($project->debug());
    }

    public function testDebug2()
    {
        $project = new Project($appPath = __DIR__.'/app');

        $option = $this->createMock(IRequest::class);

        $option->method('get')->willReturn(false);
        $this->assertFalse($option->get('debug'));

        $project->singleton('option', function () use ($option) {
            return $option;
        });

        $this->assertFalse($project->debug());
    }

    public function testDevelopment()
    {
        $project = new Project($appPath = __DIR__.'/app');

        $option = $this->createMock(IRequest::class);

        $option->method('get')->willReturn('development');
        $this->assertEquals('development', $option->get('development'));

        $project->singleton('option', function () use ($option) {
            return $option;
        });

        $this->assertTrue($project->development());
    }

    public function testDevelopment2()
    {
        $project = new Project($appPath = __DIR__.'/app');

        $option = $this->createMock(IRequest::class);

        $option->method('get')->willReturn('foo');
        $this->assertEquals('foo', $option->get('development'));

        $project->singleton('option', function () use ($option) {
            return $option;
        });

        $this->assertFalse($project->development());
    }

    public function testEnvironment()
    {
        $project = new Project($appPath = __DIR__.'/app');

        $option = $this->createMock(IRequest::class);

        $option->method('get')->willReturn('foo');
        $this->assertEquals('foo', $option->get('development'));

        $project->singleton('option', function () use ($option) {
            return $option;
        });

        $this->assertSame('foo', $project->environment());
    }

    public function testMakeProvider()
    {
        $project = new Project($appPath = __DIR__.'/app');

        $project->makeProvider(ProviderTest1::class);

        $this->assertSame(1, $_SERVER['testMakeProvider']);

        unset($_SERVER['testMakeProvider']);
    }

    public function testCallProviderBootstrap()
    {
        $project = new Project($appPath = __DIR__.'/app');

        $project->callProviderBootstrap(new ProviderTest1($project));

        $this->assertSame(1, $_SERVER['testMakeProvider']);

        unset($_SERVER['testMakeProvider']);

        $project->callProviderBootstrap(new ProviderTest2($project));

        $this->assertSame(1, $_SERVER['testCallProviderBootstrap']);

        unset($_SERVER['testCallProviderBootstrap']);
    }

    public function testBootstrap()
    {
        $project = new Project($appPath = __DIR__.'/app');

        $project->bootstrap([BootstrapTest1::class, BootstrapTest2::class]);

        $this->assertSame(1, $_SERVER['bootstrapTest1']);
        $this->assertSame(1, $_SERVER['bootstrapTest2']);

        unset($_SERVER['bootstrapTest1'], $_SERVER['bootstrapTest2']);
    }

    public function testBootstrap2()
    {
        $project = new Project($appPath = __DIR__.'/app');

        $project->bootstrap([BootstrapTest1::class, BootstrapTest2::class]);

        $this->assertFalse($project->isBootstrap());

        $this->assertSame(1, $_SERVER['bootstrapTest1']);
        $this->assertSame(1, $_SERVER['bootstrapTest2']);

        unset($_SERVER['bootstrapTest1'], $_SERVER['bootstrapTest2']);

        $project->bootstrapProviders();

        $this->assertTrue($project->isBootstrap());

        $project->bootstrap([BootstrapTest1::class, BootstrapTest2::class]);

        $this->assertArrayNotHasKey('bootstrapTest1', $_SERVER);
        $this->assertArrayNotHasKey('bootstrapTest2', $_SERVER);
    }

    public function testRegisterProviders()
    {
        $project = new Project($appPath = __DIR__.'/app');

        $option = new OptionTest();

        $project->singleton('option', function () use ($option) {
            return $option;
        });

        $project->registerProviders();

        // for deferredAlias
        $this->assertArrayNotHasKey('providerDeferTest1', $_SERVER);
        $this->assertSame('bar', $project->make('foo'));
        $this->assertSame('bar', $project->make(ProviderDeferTest1::class));
        $this->assertSame(1, $_SERVER['providerDeferTest1']);

        // for providers
        $this->assertSame(1, $_SERVER['testRegisterProvidersRegister']);
        $this->assertArrayNotHasKey('testRegisterProvidersBootstrap', $_SERVER);

        unset(
            $_SERVER['providerDeferTest1'],
            $_SERVER['testRegisterProvidersRegister']
        );

        // bootstrap
        $this->assertFalse($project->isBootstrap());
        $project->bootstrapProviders();
        $this->assertSame(1, $_SERVER['testRegisterProvidersBootstrap']);
        unset($_SERVER['testRegisterProvidersBootstrap']);
        $this->assertTrue($project->isBootstrap());

        // bootstrap again but already bootstrap
        $project->bootstrapProviders();
        $this->assertArrayNotHasKey('testRegisterProvidersBootstrap', $_SERVER);

        // again but already bootstrap
        $project->registerProviders();
        $this->assertArrayNotHasKey('testRegisterProvidersRegister', $_SERVER);
    }
}

class Project extends Projects
{
    protected function registerBaseProvider()
    {
    }
}

class ProviderTest1 extends Provider
{
    public function __construct(Project $project)
    {
        $_SERVER['testMakeProvider'] = 1;
    }

    public function register()
    {
    }
}

class ProviderTest2 extends Provider
{
    public function __construct(Project $project)
    {
    }

    public function bootstrap()
    {
        $_SERVER['testCallProviderBootstrap'] = 1;
    }

    public function register()
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
                    'foo' => 'Tests\\Bootstrap\\ProviderDeferTest1',
                ],
                [
                    'Tests\\Bootstrap\\ProviderDeferTest1' => [
                        'foo' => [
                            'Tests\\Bootstrap\\ProviderDeferTest1',
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
    public function register()
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
                'Tests\\Bootstrap\\ProviderDeferTest1',
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
    public function __construct(Project $project)
    {
    }

    public function bootstrap()
    {
        $_SERVER['testRegisterProvidersBootstrap'] = 1;
    }

    public function register()
    {
        $_SERVER['testRegisterProvidersRegister'] = 1;
    }
}
