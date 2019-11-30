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

namespace Tests\Mail;

use Leevel\Mail\Test;
use Leevel\Router\View;
use Leevel\View\Phpui;
use Swift_Message;
use Tests\TestCase;

/**
 * test test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.28
 *
 * @version 1.0
 */
class TestTest extends TestCase
{
    public function testBaseUse(): void
    {
        $test = new Test($this->makeView());

        $message = (new Swift_Message('Wonderful Subject'))
            ->setFrom(['foo@qq.com' => 'John Doe'])
            ->setTo(['bar@qq.com' => 'A name'])
            ->setBody('Here is the message itself');

        $result = $test->send($message);

        $this->assertSame(1, $result);

        $this->assertTrue($test->isStarted());
        $this->assertNull($test->start());
        $this->assertNull($test->stop());
        $this->assertTrue($test->ping());
    }

    protected function makeView(): View
    {
        return new View(
            new Phpui([
                'theme_path' => __DIR__,
            ])
        );
    }
}
