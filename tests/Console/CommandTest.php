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

namespace Tests\Console;

use Tests\Console\Command\CallOtherCommand;
use Tests\Console\Load1\Test1;
use Tests\TestCase;

/**
 * command test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.24
 *
 * @version 1.0
 */
class CommandTest extends TestCase
{
    use BaseCommand;

    public function testBaseUse()
    {
        $result = $this->runCommand(new CallOtherCommand(), [
            'command'     => 'call:other',
        ], function ($container, $application) {
            $application->normalizeCommands([Test1::class]);
        });

        // dd($result);

        $this->assertContains('call other command test.', $result);
        $this->assertContains('load1 test1', $result);

        // argument and option
        $this->assertContains('argument is {"command":"call:other"}', $result);
        $this->assertContains('option is {"help":false,"quiet":false,"verbose":false,"version":false,"ansi":false,"no-ansi":false,"no-interaction":false}', $result);

        // table
        $this->assertContains('| Item  | Value |', $result);
        $this->assertContains('| hello | world |', $result);
        $this->assertContains('| foo   | bar   |', $result);

        // time
        $this->assertContains(']test time', $result);

        // question
        $this->assertContains('a question', $result);

        // error
        $this->assertContains('a error message', $result);
    }

    public function t2estActionSpecial()
    {
        $result = $this->runCommand(new Action(), [
            'command'     => 'make:action',
            'controller'  => 'Hello',
            'name'        => 'hello-world_Yes',
            '--namespace' => 'common',
        ]);

        $this->assertContains('action <hello-world_Yes> created successfully.', $result);

        $file = __DIR__.'/../../Console/Hello/HelloWorldYes.php';

        $this->assertContains('class HelloWorldYes extends Controller', file_get_contents($file));

        $this->assertContains('function run', file_get_contents($file));

        unlink($file);
        rmdir(dirname($file));
    }

    public function t2estExtend()
    {
        $result = $this->runCommand(new Action(), [
            'command'     => 'make:action',
            'controller'  => 'Hello',
            'name'        => 'hello-world_Yes',
            '--namespace' => 'common',
            '--extend'    => 0,
        ]);

        $this->assertContains('action <hello-world_Yes> created successfully.', $result);

        $file = __DIR__.'/../../Console/Hello/HelloWorldYes.php';

        $this->assertNotContains('class HelloWorldYes extends Controller', file_get_contents($file));

        $this->assertContains('function run', file_get_contents($file));

        unlink($file);
        rmdir(dirname($file));
    }
}
