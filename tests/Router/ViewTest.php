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

namespace Tests\Router;

use Leevel\Router\IView;
use Leevel\Router\View;
use Leevel\View\Html;
use Leevel\View\Phpui;
use Tests\TestCase;

/**
 * view test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.12
 *
 * @version 1.0
 */
class ViewTest extends TestCase
{
    public function testBaseUse()
    {
        $view = new View(
            $html = new Html()
        );

        $this->assertInstanceof(IView::class, $view);

        $view->setVar('hello', 'world');

        $this->assertSame('world', $view->getVar('hello'));

        $this->assertSame('world', $html->getVar('hello'));
    }

    public function testDelete()
    {
        $view = new View(
            $html = new Html()
        );

        $view->setVar('hello', 'world');

        $this->assertSame('world', $view->getVar('hello'));

        $this->assertSame('world', $html->getVar('hello'));

        // delete
        $view->deleteVar(['hello']);

        $this->assertNull($view->getVar('hello'));

        $this->assertNull($html->getVar('hello'));
    }

    public function testClear()
    {
        $view = new View(
            $html = new Html()
        );

        $view->setVar('foo', 'bar');

        $this->assertSame('bar', $view->getVar('foo'));

        $this->assertSame('bar', $html->getVar('foo'));

        $view->clearVar();

        $this->assertNull($view->getVar('foo'));

        $this->assertNull($html->getVar('foo'));
    }

    public function testDisplay()
    {
        $view = new View(
            $phpui = new Phpui([
                'theme_path' => __DIR__,
            ])
        );

        $view->setVar('foo', 'bar');

        $this->assertSame(
            'Hi here! bar',
            $view->display(__DIR__.'/assert/hello.php')
        );
    }

    public function testSwitchView()
    {
        $view = new View(
            $phpui = new Phpui()
        );

        $view->setVar('foo', 'bar');

        $this->assertSame('bar', $view->getVar('foo'));

        $this->assertSame('bar', $phpui->getVar('foo'));

        $view->switchView($html = new Html());

        $this->assertSame('bar', $view->getVar('foo'));

        $this->assertSame('bar', $html->getVar('foo'));
    }
}
