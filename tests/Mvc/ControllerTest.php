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

namespace Tests\Mvc;

use Leevel\Mvc\Controller;
use Leevel\Mvc\IController;
use Leevel\Mvc\View;
use Leevel\View\Html;
use Leevel\View\Phpui;
use Leevel\View\View as Views;
use Tests\TestCase;

/**
 * controller test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.14
 *
 * @version 1.0
 */
class ControllerTest extends TestCase
{
    public function testBaseUse()
    {
        $test = new Test();

        $test->setView(
            new View(
                new Views(new Html())
            )
        );

        $this->assertInstanceof(IController::class, $test);

        $test->assign('hello', 'world');

        $this->assertSame('world', $test->getAssign('hello'));
    }

    public function testDelete()
    {
        $test = new Test();

        $test->setView(
            new View(
                new Views(new Html())
            )
        );

        $test->assign('hello', 'world');

        $this->assertSame('world', $test->getAssign('hello'));

        // delete
        $test->deleteAssign('hello');

        $this->assertNull($test->getAssign('hello'));
    }

    public function testClear()
    {
        $test = new Test();

        $test->setView(
            new View(
                new Views(new Html())
            )
        );

        $test->assign('foo', 'bar');

        $this->assertSame('bar', $test->getAssign('foo'));

        $test->clearAssign();

        $this->assertNull($test->getAssign('foo'));
    }

    public function testDisplay()
    {
        $test = new Test();

        $test->setView(
            new View(
                new Views(new Phpui([
                    'theme_path' => __DIR__,
                ]))
            )
        );

        $test->assign('foo', 'bar');

        $this->assertSame(
            'Hi here! bar',
            $test->display(__DIR__.'/assert/hello.php')
        );
    }

    public function testSwitchView()
    {
        $test = new Test();

        $test->setView(
            new View(
                $phpui = new Views(new Phpui())
            )
        );

        $test->assign('foo', 'bar');

        $this->assertSame('bar', $test->getAssign('foo'));

        $this->assertSame('bar', $phpui->getVar('foo'));

        $test->switchView($html = new Views(new Html()));

        $this->assertSame('bar', $test->getAssign('foo'));

        $this->assertSame('bar', $html->getVar('foo'));
    }

    public function testViewNotFound()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'View is not set in controller.'
        );

        $test = new Test();

        $test->assign('foo', 'bar');
    }
}

class Test extends Controller
{
}
