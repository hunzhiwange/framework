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

use Leevel\Console\Application;
use Leevel\Kernel\IApp;
use Leevel\Kernel\IKernelConsole;
use Leevel\Leevel\App as Apps;
use Leevel\Leevel\KernelConsole;
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
        $app = new AppKernelConsole();

        $this->createOption($app);

        $kernel = new KernelConsole1($app);
        $this->assertInstanceof(IKernelConsole::class, $kernel);
        $this->assertInstanceof(IApp::class, $kernel->getApp());

        $this->assertSame(0, $kernel->handle());
    }

    protected function createOption(IApp $app): void
    {
        $map = [
            ['console\\template', null, []],
            ['_composer.commands', null, [
                'Tests\\Leevel\\Commands\\Test',
                'Tests\\Leevel\\Commands\\Console',
            ]],
        ];

        $option = $this->createMock(IOption::class);
        $option->method('get')->will($this->returnValueMap($map));
        $this->assertSame([], $option->get('console\\template'));
        $this->assertSame([
            'Tests\\Leevel\\Commands\\Test',
            'Tests\\Leevel\\Commands\\Console',
        ], $option->get('_composer.commands'));

        $app->singleton('option', function () use ($option) {
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

        return $this->consoleApplication = new Application1($this->app, $this->app->version());
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
