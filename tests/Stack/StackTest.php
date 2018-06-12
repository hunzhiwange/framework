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

use Leevel\Stack\Stack;
use Tests\TestCase;

/**
 * stack test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.10
 *
 * @version 1.0
 * @coversNothing
 */
class StackTest extends TestCase
{
    public function testBaseUse()
    {
        $stack = new Stack();

        $this->assertSame(0, $stack->count());

        // 入栈 5
        $stack->in(5);

        $this->assertSame(1, $stack->count());

        // 入栈 6
        $stack->in(6);

        $this->assertSame(2, $stack->count());

        // 出栈，后进先出
        $this->assertSame(6, $stack->out());

        $this->assertSame(1, $stack->count());

        // 出栈，后进先出
        $this->assertSame(5, $stack->out());

        $this->assertSame(0, $stack->count());
    }

    public function testValidateType()
    {
        $this->expectException(\InvalidArgumentException::class);

        $stack = new Stack(['string']);

        $stack->in(5);
    }
}
