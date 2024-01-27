<?php

declare(strict_types=1);

namespace Tests\Kernel;

use Leevel\Config\Config;
use Leevel\Config\IConfig;
use Leevel\Console\Application;
use Leevel\Database\Console\SeedRun;
use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Kernel\App as Apps;
use Leevel\Kernel\IApp;
use Leevel\Kernel\IKernelConsole;
use Leevel\Kernel\KernelConsole;
use Leevel\Kernel\Utils\Api;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => '命令行内核',
    'path' => 'architecture/kernel/kernelconsole',
    'zh-CN:description' => <<<'EOT'
QueryPHP 命令行流程为入口接受输入，经过内核 kernel 传入输入，经过命令行应用程序调用命令执行业务，最后返回输出结果。

入口文件 `leevel`

``` php
{[file_get_contents('leevel')]}
```

内核通过 \Leevel\Kernel\KernelConsole 的 handle 方法来实现请求。

**handle 原型**

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Leevel\Kernel\KernelConsole::class, 'handle', 'define')]}
```
EOT,
    'zh-CN:note' => <<<'EOT'
命令行内核设计为可替代，只需要实现 `\Leevel\Kernel\IKernelConsole` 即可，然后在入口文件替换即可。
EOT,
])]
final class KernelConsoleTest extends TestCase
{
    #[Api([
        'zh-CN:title' => '基本使用',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**Tests\Kernel\AppKernelConsole**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\AppKernelConsole::class)]}
```

**Tests\Kernel\Application1**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\Application1::class)]}
```

**Tests\Kernel\KernelConsole1**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\KernelConsole1::class)]}
```

**Tests\Kernel\Commands\Test**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\Commands\Test::class)]}
```

**Tests\Kernel\Commands\Console\Foo**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\Commands\Console\Foo::class)]}
```

**Tests\Kernel\Commands\Console\Bar**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\Commands\Console\Bar::class)]}
```

**Tests\Kernel\DemoBootstrapForKernelConsole**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\DemoBootstrapForKernelConsole::class)]}
```
EOT,
    ])]
    public function testBaseUse(): void
    {
        $app = new AppKernelConsole($container = new Container(), '');
        $container->instance('app', $app);

        $this->createConfig($container);

        $kernel = new KernelConsole1($app);
        $this->assertInstanceof(IKernelConsole::class, $kernel);
        $this->assertInstanceof(IApp::class, $kernel->getApp());
        static::assertSame(0, $kernel->handle());
        $kernel->terminate(0);
        static::assertTrue($GLOBALS['DemoBootstrapForKernelConsole']);
        unset($GLOBALS['DemoBootstrapForKernelConsole']);
    }

    public function testBaseUse2(): void
    {
        $app = new AppKernelConsole($container = new Container(), '');
        $container->instance('app', $app);
        $kernel = new KernelConsole2($app);
        $this->assertInstanceof(IKernelConsole::class, $kernel);
        $this->assertInstanceof(IApp::class, $kernel->getApp());
        static::assertInstanceOf(Application::class, $this->invokeTestMethod($kernel, 'getConsoleApplication'));
        static::assertInstanceOf(Application::class, $this->invokeTestMethod($kernel, 'getConsoleApplication')); // cached
    }

    public function test3(): void
    {
        $app = new AppKernelConsole($container = new Container(), '');
        $container->instance('app', $app);
        $this->createConfig($container);
        $kernel = new KernelConsole3($app);
        $this->assertInstanceof(IKernelConsole::class, $kernel);
        $this->assertInstanceof(IApp::class, $kernel->getApp());
        static::assertSame(0, $kernel->handle());
    }

    protected function createConfig(IContainer $container): void
    {
        $configData = [
            'app' => [
                ':composer' => [
                    'commands' => [
                        'Tests\\Kernel\\Commands\\Test',
                        'Tests\\Kernel\\Commands\\Console',
                        SeedRun::class,
                    ],
                ],
            ],
            'console' => [
                'template' => [],
            ],
        ];
        $config = new Config($configData);
        static::assertSame([], $config->get('console\\template'));
        static::assertSame([
            'Tests\\Kernel\\Commands\\Test',
            'Tests\\Kernel\\Commands\\Console',
            SeedRun::class,
        ], $config->get(':composer.commands'));

        $container->singleton('config', function () use ($config): IConfig {
            return $config;
        });
    }
}

class KernelConsole1 extends KernelConsole
{
    protected array $bootstraps = [
        DemoBootstrapForKernelConsole::class,
    ];

    protected function getConsoleApplication(): Application
    {
        if ($this->consoleApplication) {
            return $this->consoleApplication;
        }

        return $this->consoleApplication = new Application1($this->app->container(), $this->app->version());
    }
}

class KernelConsole2 extends KernelConsole
{
    protected array $bootstraps = [];
}

class KernelConsole3 extends KernelConsole
{
    protected array $bootstraps = [];

    protected function getConsoleApplication(): Application
    {
        if ($this->consoleApplication) {
            return $this->consoleApplication;
        }

        return $this->consoleApplication = new Application1($this->app->container(), $this->app->version());
    }

    protected function includeInvalidCommands(): bool
    {
        return true;
    }
}

class AppKernelConsole extends Apps
{
    public function namespacePath(string $namespace): string
    {
        return __DIR__.'/Commands/Console';
    }

    protected function registerBaseProvider(): void
    {
    }
}

class Application1 extends Application
{
    public function run(?InputInterface $input = null, ?OutputInterface $output = null): int
    {
        return 0;
    }
}

class DemoBootstrapForKernelConsole
{
    public function handle(IApp $app): void
    {
        $GLOBALS['DemoBootstrapForKernelConsole'] = true;
    }
}
