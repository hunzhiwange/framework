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
 * kernelConsole test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.24
 *
 * @version 1.0
 */
class KernelConsoleTest extends TestCase
{
    public function testBaseUse()
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
        $option->method('get')->will($this->returnValueMap($map));
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
    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        return 0;
    }
}
