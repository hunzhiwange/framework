<?php

declare(strict_types=1);

namespace Tests\Support;

use Leevel\Support\LinkedList;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="双向链表",
 *     path="component/linkedlist",
 *     zh-CN:description="
 * 在 PHP 双向链表的基础上加上数据类型验证功能，不少业务场景中保证链表中数据一致性。
 *
 * 阻止链表返回空数据时抛出异常的默认行为。
 *
 * 底层基于 spldoublylinkedlist 开发，相关文档 <http://php.net/manual/zh/class.spldoublylinkedlist.php>。
 * ",
 * )
 *
 * @internal
 */
final class LinkedListTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="链表基本使用方法",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $linkedList = new LinkedList();

        static::assertSame(0, $linkedList->count());
        static::assertNull($linkedList->pop());
        static::assertNull($linkedList->pop());
    }

    /**
     * @api(
     *     zh-CN:title="push 链表尾部弹出元素",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testPush(): void
    {
        $linkedList = new LinkedList();
        $linkedList->push(5);
        $linkedList->push(6);

        static::assertSame(2, $linkedList->count());
        static::assertSame(6, $linkedList->pop());
        static::assertSame(5, $linkedList->pop());
        static::assertSame(0, $linkedList->count());
    }

    /**
     * @api(
     *     zh-CN:title="unshift 链表头插入元素",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testUnshift(): void
    {
        $linkedList = new LinkedList();
        $linkedList->unshift(5);
        $linkedList->unshift(6);

        static::assertSame(2, $linkedList->count());
        static::assertSame(5, $linkedList->pop());
        static::assertSame(6, $linkedList->pop());
        static::assertSame(0, $linkedList->count());
    }

    /**
     * @api(
     *     zh-CN:title="add 链表指定位置插入新值",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testAdd(): void
    {
        $linkedList = new LinkedList();
        $linkedList->add(0, 'hello');
        $linkedList->add(1, 'world');
        $linkedList->add(2, 'foo');
        $linkedList->add(3, 'bar');

        static::assertSame('hello', $linkedList->offsetGet(0));
        static::assertSame('world', $linkedList->offsetGet(1));
        static::assertSame('foo', $linkedList->offsetGet(2));
        static::assertSame('bar', $linkedList->offsetGet(3));
    }

    /**
     * @api(
     *     zh-CN:title="offsetSet 更新链表指定位置链表的值",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testOffsetSet(): void
    {
        $linkedList = new LinkedList();
        $linkedList->add(0, 'hello');
        $linkedList->add(1, 'world');
        $linkedList->add(2, 'foo');
        $linkedList->add(3, 'bar');

        $linkedList->offsetSet(0, 'hello2');
        $linkedList->offsetSet(1, 'world2');
        $linkedList->offsetSet(2, 'foo2');
        $linkedList->offsetSet(3, 'bar2');

        static::assertSame('hello2', $linkedList->offsetGet(0));
        static::assertSame('world2', $linkedList->offsetGet(1));
        static::assertSame('foo2', $linkedList->offsetGet(2));
        static::assertSame('bar2', $linkedList->offsetGet(3));
    }

    /**
     * @api(
     *     zh-CN:title="链表支持元素类型限定",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testValidateType(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('The element type must be one of the following `string`.');

        $linkedList = new LinkedList(['string']);
        $linkedList->push(5);
    }
}
