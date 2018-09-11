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

namespace Tests\Event;

use Leevel\Di\Container;
use Leevel\Event\Observer;
use Leevel\Event\Subject;
use Tests\TestCase;

/**
 * subject test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.27
 *
 * @version 1.0
 */
class SubjectTest extends TestCase
{
    public function testBaseUse()
    {
        $container = new Container();

        $subject = new Subject($container);

        $subject->attach($observer1 = new Observer1());

        $_SERVER['runtime'] = [];

        $subject->notify('hello');

        $this->assertSame(['hello'], $_SERVER['runtime']);

        $_SERVER['runtime'] = [];

        $subject->detach($observer1);

        $this->assertSame([], $_SERVER['runtime']);

        unset($_SERVER['runtime']);
    }
}

class Observer1 extends Observer
{
    public function run($arg1)
    {
        $_SERVER['runtime'][] = $arg1;
    }
}
