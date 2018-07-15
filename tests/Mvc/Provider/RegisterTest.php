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

use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Mvc\IView;
use Leevel\Mvc\Provider\Register;
use Leevel\Mvc\View;
use Leevel\View\Phpui;
use Tests\TestCase;

/**
 * register test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.15
 *
 * @version 1.0
 */
class RegisterTest extends TestCase
{
    public function testBaseUse()
    {
        $test = new Register($container = new Container());

        $this->assertInstanceof(IContainer::class, $test->container());
        $this->assertInstanceof(Container::class, $test->container());

        $test->register();

        $container->singleton('view.view', function () {
            return new Phpui();
        });

        $container->alias($test->providers());

        $container->make('view')->assign('hello', 'world');

        $this->assertSame('world', $container->make('view')->getAssign('hello'));
        $this->assertSame('world', $container->make(IView::class)->getAssign('hello'));
        $this->assertSame('world', $container->make(View::class)->getAssign('hello'));

        $this->assertTrue($test->isDeferred());
    }
}
