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
 *     title="命令行内核执行",
 *     path="architecture/kernel/kernelconsole",
 *     description="
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
 *     note="
 * 内核设计为可替代，只需要实现 `\Leevel\Kernel\IKernelConsole` 即可，然后在入口文件替换即可。
 * ",
 * )
 */
class KernelConsoleTest extends TestCase
{
    /**
     * @api(
     *     title="基本使用",
     *     description="
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
     * ",
     *     note="",
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

        $this->assertSame(0, $kernel->handle());
    }

    protected function createOption(IContainer $container): void
    {
        $map = [
            ['console\\template', null, []],
            ['_composer.commands', null, [
                'Tests\\Kernel\\Commands\\Test',
                'Tests\\Kernel\\Commands\\Console',
            ]],
        ];

        $option = $this->createMock(IOption::class);
        $option->method('get')->willReturnMap($map);
        $this->assertSame([], $option->get('console\\template'));
        $this->assertSame([
            'Tests\\Kernel\\Commands\\Test',
            'Tests\\Kernel\\Commands\\Console',
        ], $option->get('_composer.commands'));

        $container->singleton('option', function () use ($option) {
            return $option;
        });
    }
}

class KernelConsole1 extends KernelConsole
{
    public function bootstrap(): void
    {
    }

    protected function getConsoleApplication(): Application
    {
        if ($this->consoleApplication) {
            return $this->consoleApplication;
        }

        return $this->consoleApplication = new Application1($this->app->container(), $this->app->version());
    }
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
    public function run(?InputInterface $input = null, ?OutputInterface $output = null)
    {
        return 0;
    }
}
