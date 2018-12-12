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

namespace Tests\Stack;

use Leevel\Stack\Queue;
use Tests\TestCase;

/**
 * queue test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.10
 *
 * @version 1.0
 */
class QueueTest extends TestCase
{
    public function testBaseUse()
    {
        $queue = new Queue();

        $this->assertSame(0, $queue->count());

        // 入对 5
        $queue->in(5);

        $this->assertSame(1, $queue->count());

        // 入对 6
        $queue->in(6);

        $this->assertSame(2, $queue->count());

        // 出对，先进先出
        $this->assertSame(5, $queue->out());

        $this->assertSame(1, $queue->count());

        // 出对，先进先出
        $this->assertSame(6, $queue->out());

        $this->assertSame(0, $queue->count());
    }

    public function testValidateType()
    {
        $this->expectException(\InvalidArgumentException::class);

        $queue = new Queue(['string']);

        $queue->in(5);
    }
}
