<?php

declare(strict_types=1);

namespace Tests\Support;

use Leevel\Support\Stack;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="栈",
 *     path="component/stack",
 *     zh-CN:description="
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
final class StackTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="栈基本使用方法",
     *     zh-CN:description="
     * 栈是一种操作受限的线性表数据结构，包含两个操作。入栈 push，放一个数据到栈顶; 出栈 pop，从栈顶取一个元素。
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $stack = new Stack();
        static::assertSame(0, $stack->count());

        // 入栈 5
        $stack->push(5);
        static::assertSame(1, $stack->count());

        // 入栈 6
        $stack->push(6);
        static::assertSame(2, $stack->count());

        // 出栈，后进先出
        static::assertSame(6, $stack->pop());
        static::assertSame(1, $stack->count());

        // 出栈，后进先出
        static::assertSame(5, $stack->pop());
        static::assertSame(0, $stack->count());
    }

    /**
     * @api(
     *     zh-CN:title="栈支持元素类型限定",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testValidateType(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('The element type must be one of the following `string`.');

        $stack = new Stack(['string']);
        $stack->push(5);
    }
}
