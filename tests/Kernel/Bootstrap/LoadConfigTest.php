<?php

declare(strict_types=1);

namespace Tests\Kernel\Bootstrap;

use Leevel\Config\Config;
use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Filesystem\Helper;
use Leevel\Kernel\App as Apps;
use Leevel\Kernel\Bootstrap\LoadConfig;
use Leevel\Kernel\IApp;
use Leevel\Kernel\Utils\Api;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => '初始化载入配置',
    'path' => 'architecture/kernel/bootstrap/loadconfig',
    'zh-CN:description' => <<<'EOT'
QueryPHP 在内核执行过程中会执行初始化，分为 4 个步骤，载入配置、载入语言包、注册异常运行时和遍历服务提供者注册服务。

内核初始化，包括 `\Leevel\Kernel\IKernel::bootstrap` 和 `\Leevel\Kernel\IKernelConsole::bootstrap` 均会执行上述 4 个步骤。
EOT,
])]
final class LoadConfigTest extends TestCase
{
    protected function setUp(): void
    {
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        Container::singletons()->clear();

        $appPath = __DIR__.'/app';
        $storagePath = $appPath.'/storage';

        if (is_dir($storagePath)) {
            Helper::deleteDirectory($storagePath);
        }

        if (getenv('RUNTIME_ENVIRONMENT')) {
            putenv('RUNTIME_ENVIRONMENT=');
        }
    }

    #[Api([
        'zh-CN:title' => '基本使用方法',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**环境变量 tests/Kernel/Bootstrap/app/.env**

``` php
{[file_get_contents('vendor/hunzhiwange/framework/tests/Kernel/Bootstrap/app/.env')]}
```

**配置文件 tests/Kernel/Bootstrap/app/config/app.php**

``` php
{[file_get_contents('vendor/hunzhiwange/framework/tests/Kernel/Bootstrap/app/config/app.php')]}
```

**配置文件 tests/Kernel/Bootstrap/app/config/demo.php**

``` php
{[file_get_contents('vendor/hunzhiwange/framework/tests/Kernel/Bootstrap/app/config/demo.php')]}
```
EOT,
    ])]
    public function testBaseUse(): void
    {
        $bootstrap = new LoadConfig1();

        $container = Container::singletons();
        $app = new App3($container, $appPath = __DIR__.'/app');

        $this->assertInstanceof(IContainer::class, $container);
        $this->assertInstanceof(Container::class, $container);
        $this->assertInstanceof(IApp::class, $app);
        $this->assertInstanceof(Apps::class, $app);

        static::assertSame($appPath.'/storage/bootstrap/config.php', $app->configCachedPath());
        static::assertFalse($app->isCachedConfig());
        static::assertSame($appPath.'/config', $app->configPath());

        static::assertNull($bootstrap->handle($app));

        $config = $container->make('config');

        static::assertSame('development', $config->get('environment'));
        static::assertSame('bar', $config->get('demo\\foo'));
    }

    #[Api([
        'zh-CN:title' => 'RUNTIME_ENVIRONMENT 载入自定义环境变量文件',
        'zh-CN:description' => <<<'EOT'
设置 `RUNTIME_ENVIRONMENT` 环境变量可以载入自定义环境变量文件。

**fixture 定义**

**环境变量 tests/Kernel/Bootstrap/app/.fooenv**

``` php
{[file_get_contents('vendor/hunzhiwange/framework/tests/Kernel/Bootstrap/app/.fooenv')]}
```
EOT,
    ])]
    public function testWithRuntimeEnv(): void
    {
        putenv('RUNTIME_ENVIRONMENT=fooenv');

        $bootstrap = new LoadConfig1();

        $container = Container::singletons();
        $app = new App3($container, $appPath = __DIR__.'/app');

        $this->assertInstanceof(IContainer::class, $container);
        $this->assertInstanceof(Container::class, $container);
        $this->assertInstanceof(IApp::class, $app);
        $this->assertInstanceof(Apps::class, $app);

        static::assertSame($appPath.'/storage/bootstrap/fooenv.php', $app->configCachedPath());
        static::assertFalse($app->isCachedConfig());
        static::assertSame($appPath.'/config', $app->configPath());

        static::assertNull($bootstrap->handle($app));

        $config = $container->make('config');

        static::assertSame('testing', $config->get('environment'));
        static::assertSame('bar', $config->get('demo\\foo'));
    }

    public function testWithRuntimeEnvNotFound(): void
    {
        $appPath = __DIR__.'/app';

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            sprintf('Env file `%s` was not found.', $appPath.'/.notfoundenv')
        );

        putenv('RUNTIME_ENVIRONMENT=notfoundenv');

        $bootstrap = new LoadConfig1();

        $container = Container::singletons();
        $app = new App3($container, $appPath);

        $this->assertInstanceof(IContainer::class, $container);
        $this->assertInstanceof(Container::class, $container);
        $this->assertInstanceof(IApp::class, $app);
        $this->assertInstanceof(Apps::class, $app);

        static::assertSame($appPath.'/storage/bootstrap/notfoundenv.php', $app->configCachedPath());
        static::assertFalse($app->isCachedConfig());
        static::assertSame($appPath.'/config', $app->configPath());

        $bootstrap->handle($app);
    }

    #[Api([
        'zh-CN:title' => '配置支持缓存',
        'zh-CN:description' => <<<'EOT'
配置文件支持缓存，通过缓存可以降低开销提高性能，适合生产环境。

**fixture 定义**

**配置缓存文件 tests/Kernel/Bootstrap/app/assert/config.php**

``` php
{[file_get_contents('vendor/hunzhiwange/framework/tests/Kernel/Bootstrap/app/assert/config.php')]}
```
EOT,
    ])]
    public function testLoadCached(): void
    {
        $bootstrap = new LoadConfig1();

        $container = Container::singletons();
        $app = new App3($container, $appPath = __DIR__.'/app');

        $this->assertInstanceof(IContainer::class, $container);
        $this->assertInstanceof(Container::class, $container);
        $this->assertInstanceof(IApp::class, $app);
        $this->assertInstanceof(Apps::class, $app);

        static::assertSame($appPath.'/storage/bootstrap/config.php', $app->configCachedPath());
        static::assertFalse($app->isCachedConfig());
        static::assertSame($appPath.'/config', $app->configPath());

        mkdir($appPath.'/storage/bootstrap', 0o777, true);
        file_put_contents($appPath.'/storage/bootstrap/config.php', file_get_contents($appPath.'/assert/config.php'));

        static::assertTrue($app->isCachedConfig());

        static::assertNull($bootstrap->handle($app));

        $config = $container->make('config');

        static::assertSame('development', $config->get('environment'));
        static::assertSame('bar', $config->get('demo\\foo'));
        static::assertNull($config->get(':env.foo'));
        static::assertTrue($config->get(':env.debug'));
    }
}

class App3 extends Apps
{
    protected function registerBaseProvider(): void
    {
    }
}

class LoadConfig1 extends LoadConfig
{
    protected function initialization(Config $config): void
    {
    }
}
