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

namespace Tests\Console;

use Leevel\Console\Application;
use Leevel\Console\RunCommand;
use Leevel\Di\Container;
use Tests\Console\Command\CallOtherCommand;
use Tests\Console\Load1\Test1;
use Tests\TestCase;

/**
 * @api(
 *     title="运行命令代码",
 *     path="component/console/runcommand",
 *     description="
 * 有时候我们需要在非命令行调用命令，比如在控制器等地方直接运行命令行代码，系统对这种场景进行了简单封装。
 * ",
 * )
 */
class RunCommandTest extends TestCase
{
    /**
     * @api(
     *     title="运行命令代码基本使用方法",
     *     description="
     * **fixture 定义**
     *
     * **Tests\Console\Load1\Test1**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Console\Load1\Test1::class)]}
     * ```
     *
     * **Tests\Console\Command\CallOtherCommand**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Console\Command\CallOtherCommand::class)]}
     * ```
     * ",
     *     note="",
     * )
     */
    public function testBaseUse(): void
    {
        $application = new Application(new Container(), '1.0');
        $runCommand = new RunCommand($application);

        $runCommand->normalizeCommand(Test1::class);
        $result = $runCommand->handle(new CallOtherCommand(), [
            'command'     => 'call:other',
        ]);

        $this->assertStringContainsString('call other command test.', $result);
        $this->assertStringContainsString('load1 test1', $result);

        // argument and option
        $this->assertStringContainsString('argument is {"command":"call:other"}', $result);
        $this->assertStringContainsString('option is {"help":false,"quiet":false,"verbose":false,"version":false,"ansi":false,"no-ansi":false,"no-interaction":false}', $result);

        // table
        $this->assertStringContainsString('| Item  | Value |', $result);
        $this->assertStringContainsString('| hello | world |', $result);
        $this->assertStringContainsString('| foo   | bar   |', $result);

        // time
        $this->assertStringContainsString(']test time', $result);

        // question
        $this->assertStringContainsString('a question', $result);

        // error
        $this->assertStringContainsString('a error message', $result);
    }
}
