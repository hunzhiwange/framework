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

use Leevel\Mvc\Console\Controller;
use Tests\Console\BaseMake;
use Tests\TestCase;

/**
 * controller test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.15
 *
 * @version 1.0
 */
class ControllerTest extends TestCase
{
    use BaseMake;

    public function testBaseUse()
    {
        $result = $this->runCommand(new Controller(), [
            'command'     => 'make:controller',
            'name'        => 'BarValue',
            'action'      => 'hello',
            '--namespace' => 'common',
        ]);

        $this->assertContains('controller <BarValue> created successfully.', $result);

        $file = __DIR__.'/../../Console/BarValue.php';

        $this->assertContains('class BarValue extends Controller', file_get_contents($file));

        unlink($file);
    }

    public function testActionSpecial()
    {
        $result = $this->runCommand(new Controller(), [
            'command'     => 'make:controller',
            'name'        => 'Hello',
            'action'      => 'hello-world_Yes',
            '--namespace' => 'common',
        ]);

        $this->assertContains('controller <Hello> created successfully.', $result);

        $file = __DIR__.'/../../Console/Hello.php';

        $this->assertContains('class Hello extends Controller', file_get_contents($file));

        $this->assertContains('function helloWorldYes', file_get_contents($file));

        unlink($file);
    }

    public function testExtend()
    {
        $result = $this->runCommand(new Controller(), [
            'command'     => 'make:controller',
            'name'        => 'Hello',
            'action'      => 'hello-world_Yes',
            '--namespace' => 'common',
            '--extend'    => 0,
        ]);

        $this->assertContains('controller <Hello> created successfully.', $result);

        $file = __DIR__.'/../../Console/Hello.php';

        $this->assertNotContains('class Hello extends Controller', file_get_contents($file));

        $this->assertContains('function helloWorldYes', file_get_contents($file));

        unlink($file);
    }
}
