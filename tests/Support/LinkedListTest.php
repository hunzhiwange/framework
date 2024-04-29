<?php

declare(strict_types=1);

namespace Tests\Support;

use Leevel\Kernel\Utils\Api;
use Leevel\Support\LinkedList;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => '双向链表',
    'path' => 'component/linkedlist',
    'zh-CN:description' => <<<'EOT'
在 PHP 双向链表的基础上加上数据类型验证功能，不少业务场景中保证链表中数据一致性。

阻止链表返回空数据时抛出异常的默认行为。

底层基于 spldoublylinkedlist 开发，相关文档 <http://php.net/manual/zh/class.spldoublylinkedlist.php>。
EOT,
])]
final class LinkedListTest extends TestCase
{
    #[Api([
        'zh-CN:title' => '链表基本使用方法',
    ])]
    public function testBaseUse(): void
    {
        $linkedList = new LinkedList();

        self::assertSame(0, $linkedList->count());
        self::assertNull($linkedList->pop());
        self::assertNull($linkedList->pop());
    }

    #[Api([
        'zh-CN:title' => 'push 链表尾部弹出元素',
    ])]
    public function testPush(): void
    {
        $linkedList = new LinkedList();
        $linkedList->push(5);
        $linkedList->push(6);

        self::assertSame(2, $linkedList->count());
        self::assertSame(6, $linkedList->pop());
        self::assertSame(5, $linkedList->pop());
        self::assertSame(0, $linkedList->count());
    }

    #[Api([
        'zh-CN:title' => 'unshift 链表头插入元素',
    ])]
    public function testUnshift(): void
    {
        $linkedList = new LinkedList();
        $linkedList->unshift(5);
        $linkedList->unshift(6);

        self::assertSame(2, $linkedList->count());
        self::assertSame(5, $linkedList->pop());
        self::assertSame(6, $linkedList->pop());
        self::assertSame(0, $linkedList->count());
    }

    #[Api([
        'zh-CN:title' => 'add 链表指定位置插入新值',
    ])]
    public function testAdd(): void
    {
        $linkedList = new LinkedList();
        $linkedList->add(0, 'hello');
        $linkedList->add(1, 'world');
        $linkedList->add(2, 'foo');
        $linkedList->add(3, 'bar');

        self::assertSame('hello', $linkedList->offsetGet(0));
        self::assertSame('world', $linkedList->offsetGet(1));
        self::assertSame('foo', $linkedList->offsetGet(2));
        self::assertSame('bar', $linkedList->offsetGet(3));
    }

    #[Api([
        'zh-CN:title' => 'offsetSet 更新链表指定位置链表的值',
    ])]
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

        self::assertSame('hello2', $linkedList->offsetGet(0));
        self::assertSame('world2', $linkedList->offsetGet(1));
        self::assertSame('foo2', $linkedList->offsetGet(2));
        self::assertSame('bar2', $linkedList->offsetGet(3));
    }

    #[Api([
        'zh-CN:title' => '链表支持元素类型限定',
    ])]
    public function testValidateType(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('The element type must be one of the following `string`.');

        $linkedList = new LinkedList(['string']);
        $linkedList->push(5);
    }
}
