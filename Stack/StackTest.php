<?php declare(strict_types=1);
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

use Tests\TestCase;
use Leevel\Stack\Stack;

/**
 * stack stack test
 * 
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2018.06.10
 * @version 1.0
 */
class StackTest extends TestCase
{

    public function testBaseUse()
    {
        $stack = new Stack();

        $this->assertEquals(0, $stack->count());

        // 入栈 5
        $stack->in(5);

        $this->assertEquals(1, $stack->count());

        // 入栈 6
        $stack->in(6);

        $this->assertEquals(2, $stack->count());

        // 出栈，后进先出
        $this->assertEquals(6, $stack->out());

        $this->assertEquals(1, $stack->count());

        // 出栈，后进先出
        $this->assertEquals(5, $stack->out());

        $this->assertEquals(0, $stack->count());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testValidateType()
    {
        $stack = new Stack(['string']);

        $stack->in(5);
    }
}
