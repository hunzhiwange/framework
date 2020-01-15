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

namespace Tests\Stack;

use Leevel\Stack\Stack;
use Tests\TestCase;

/**
 * @api(
 *     title="栈",
 *     path="component/stack",
 *     description="
 * 栈（stack）又名堆栈，它是一种运算受限的线性表。限定仅在表尾进行插入和删除操作的线性表。这一端被称为栈顶，相对地，把另一端称为栈底。
 *
 * 在 PHP 双向链表的基础上加上数据类型验证功能，不少业务场景中保证链表中数据一致性。
 *
 * 阻止链表返回空数据时抛出异常的默认行为。
 *
 * 底层基于 spldoublylinkedlist 开发，相关文档 <http://php.net/manual/zh/class.spldoublylinkedlist.php>。
 *
 * 标准库文档见 <http://php.net/manual/zh/class.splstack.php>。
 * ",
 * )
 */
class StackTest extends TestCase
{
    /**
     * @api(
     *     title="栈基本使用方法",
     *     description="
     * 栈是一种操作受限的线性表数据结构，包含两个操作。入栈 push，放一个数据到栈顶; 出栈 pop，从栈顶取一个元素。
     * ",
     *     note="",
     * )
     */
    public function testBaseUse(): void
    {
        $stack = new Stack();
        $this->assertSame(0, $stack->count());

        // 入栈 5
        $stack->push(5);
        $this->assertSame(1, $stack->count());

        // 入栈 6
        $stack->push(6);
        $this->assertSame(2, $stack->count());

        // 出栈，后进先出
        $this->assertSame(6, $stack->pop());
        $this->assertSame(1, $stack->count());

        // 出栈，后进先出
        $this->assertSame(5, $stack->pop());
        $this->assertSame(0, $stack->count());
    }

    /**
     * @api(
     *     title="栈支持元素类型限定",
     *     description="",
     *     note="",
     * )
     */
    public function testValidateType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The stack element type verification failed, and the allowed type is string.');

        $stack = new Stack(['string']);
        $stack->push(5);
    }
}
