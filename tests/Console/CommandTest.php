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

use Tests\Console\Command\CallOtherCommand;
use Tests\Console\Load1\Test1;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="命令行脚本",
 *     path="component/console",
 *     zh-CN:description="
 * QueryPHP 内置控制台命名，底层采用 `Symfony/console` 开发，用法与 Symfony 一致，对基础命令进行了简单的封装。
 * 几个简单的封装来自 `Laravel`，是对 Symfony 的基础命令做了一些常用功能的包装，可以完全满足常用开发需求。
 *
 * Console 组件是 Symfony 里面的一个控制台命令组件，可以轻松地编写出运行在 CLI 上面的命名。
 * ",
 * )
 */
class CommandTest extends TestCase
{
    use BaseCommand;

    /**
     * @api(
     *     zh-CN:title="基本使用方法",
     *     zh-CN:description="
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
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $result = $this->runCommand(new CallOtherCommand(), [
            'command'     => 'call:other',
        ], function ($container, $application) {
            $application->normalizeCommands([Test1::class]);
        });

        $result = $this->normalizeContent($result);

        $this->assertStringContainsString($this->normalizeContent('call other command test.'), $result);
        $this->assertStringContainsString($this->normalizeContent('load1 test1'), $result);

        // argument and option
        $this->assertStringContainsString($this->normalizeContent('argument is {"command":"call:other"}'), $result);
        $this->assertStringContainsString($this->normalizeContent('option is {"help":false,"quiet":false,"verbose":false,"version":false,"ansi":false,"no-ansi":false,"no-interaction":false}'), $result);

        // table
        $this->assertStringContainsString($this->normalizeContent('| Item  | Value |'), $result);
        $this->assertStringContainsString($this->normalizeContent('| hello | world |'), $result);
        $this->assertStringContainsString($this->normalizeContent('| foo   | bar   |'), $result);

        // time
        $this->assertStringContainsString($this->normalizeContent(']test time'), $result);

        // question
        $this->assertStringContainsString($this->normalizeContent('a question'), $result);

        // error
        $this->assertStringContainsString($this->normalizeContent('a error message'), $result);
    }
}
