<?php

declare(strict_types=1);

namespace Tests\Kernel;

use Leevel\Console\Application;
use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Kernel\App as Apps;
use Leevel\Kernel\IApp;
use Leevel\Kernel\IKernelConsole;
use Leevel\Kernel\KernelConsole;
use Leevel\Option\IOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="命令行内核",
 *     path="architecture/kernel/kernelconsole",
 *     zh-CN:description="
 * QueryPHP 命令行流程为入口接受输入，经过内核 kernel 传入输入，经过命令行应用程序调用命令执行业务，最后返回输出结果。
 *
 * 入口文件 `leevel`
 *
 * ``` php
 * {[file_get_contents('leevel')]}
 * ```
 *
 * 内核通过 \Leevel\Kernel\KernelConsole 的 handle 方法来实现请求。
 *
 * **handle 原型**
 *
 * ``` php
 * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Leevel\Kernel\KernelConsole::class, 'handle', 'define')]}
 * ```
 * ",
 *     zh-CN:note="
 * 命令行内核设计为可替代，只需要实现 `\Leevel\Kernel\IKernelConsole` 即可，然后在入口文件替换即可。
 * ",
 * )
 *
 * @internal
 *
 * @coversNothing
 */
final class KernelConsoleTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="基本使用",
     *     zh-CN:description="
     * **fixture 定义**
     *
     * **Tests\Kernel\AppKernelConsole**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\AppKernelConsole::class)]}
     * ```
     *
     * **Tests\Kernel\Application1**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\Application1::class)]}
     * ```
     *
     * **Tests\Kernel\KernelConsole1**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\KernelConsole1::class)]}
     * ```
     *
     * **Tests\Kernel\Commands\Test**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\Commands\Test::class)]}
     * ```
     *
     * **Tests\Kernel\Commands\Console\Foo**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\Commands\Console\Foo::class)]}
     * ```
     *
     * **Tests\Kernel\Commands\Console\Bar**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\Commands\Console\Bar::class)]}
     * ```
     *
     * **Tests\Kernel\DemoBootstrapForKernelConsole**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Kernel\DemoBootstrapForKernelConsole::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $app = new AppKernelConsole($container = new Container(), '');
        $container->instance('app', $app);

        $this->createOption($container);

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

    protected function createOption(IContainer $container): void
    {
        $map = [
            ['console\\template', null, []],
            [':composer.commands', null, [
                'Tests\\Kernel\\Commands\\Test',
                'Tests\\Kernel\\Commands\\Console',
            ]],
        ];

        $option = $this->createMock(IOption::class);
        $option->method('get')->willReturnMap($map);
        static::assertSame([], $option->get('console\\template'));
        static::assertSame([
            'Tests\\Kernel\\Commands\\Test',
            'Tests\\Kernel\\Commands\\Console',
        ], $option->get(':composer.commands'));

        $container->singleton('option', function () use ($option) {
            return $option;
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

class AppKernelConsole extends Apps
{
    public function namespacePath(string $specificClass, bool $throwException = true): string
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
