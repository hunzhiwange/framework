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

use Leevel\Bootstrap\KernelConsole;
use Leevel\Bootstrap\Project as Projects;
use Leevel\Console\Application;
use Leevel\Kernel\IKernelConsole;
use Leevel\Kernel\IProject;
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
        $project = new ProjectKernelConsole();

        $this->createOption($project);

        $kernel = new KernelConsole1($project);
        $this->assertInstanceof(IKernelConsole::class, $kernel);
        $this->assertInstanceof(IProject::class, $kernel->getProject());

        $this->assertSame(0, $kernel->handle());
    }

    protected function createOption(IProject $project): void
    {
        $map = [
            ['console\\template', null, []],
            ['_composer.commands', null, [
                'Tests\\Bootstrap\\Commands\\Test',
                'Tests\\Bootstrap\\Commands\\Console',
            ]],
        ];

        $option = $this->createMock(IOption::class);
        $option->method('get')->will($this->returnValueMap($map));
        $this->assertSame([], $option->get('console\\template'));
        $this->assertSame([
            'Tests\\Bootstrap\\Commands\\Test',
            'Tests\\Bootstrap\\Commands\\Console',
        ], $option->get('_composer.commands'));

        $project->singleton('option', function () use ($option) {
            return $option;
        });
    }
}

class KernelConsole1 extends KernelConsole
{
    protected function bootstrap(): void
    {
    }

    protected function getConsoleApplication(): Application
    {
        if ($this->consoleApplication) {
            return $this->consoleApplication;
        }

        return $this->consoleApplication = new Application1($this->project, $this->project->version());
    }
}

class ProjectKernelConsole extends Projects
{
    public function getPathByComposer($namespaces)
    {
        return __DIR__.'/Commands/Console';
    }

    protected function registerBaseProvider()
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
