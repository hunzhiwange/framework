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

namespace Tests\Mvc\Provider;

use Leevel\Mvc\Console\Action;
use Tests\Console\BaseMake;
use Tests\TestCase;

/**
 * action test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.15
 *
 * @version 1.0
 */
class ActionTest extends TestCase
{
    use BaseMake;

    public function testBaseUse()
    {
        $result = $this->runCommand(new Action(), [
            'command'     => 'make:action',
            'controller'  => 'BarValue',
            'name'        => 'hello',
            '--namespace' => 'common',
        ]);

        $this->assertContains('action <hello> created successfully.', $result);

        $file = __DIR__.'/../../Console/BarValue/Hello.php';

        $this->assertContains('class Hello extends Controller', file_get_contents($file));

        unlink($file);
        rmdir(dirname($file));
    }

    public function testActionSpecial()
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

    public function testExtend()
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
