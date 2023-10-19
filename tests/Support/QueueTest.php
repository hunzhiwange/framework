<?php

declare(strict_types=1);

namespace Tests\Support;

use Leevel\Kernel\Utils\Api;
use Leevel\Support\Queue;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => '队列',
    'path' => 'component/queue',
    'zh-CN:description' => <<<'EOT'
队列 (Queue) 具有先进先出的特性，支持在队列尾部插入元素，在队列头部删除元素的特性。

在 PHP 双向链表的基础上加上数据类型验证功能，不少业务场景中保证链表中数据一致性。

阻止链表返回空数据时抛出异常的默认行为。

底层基于 spldoublylinkedlist 开发，相关文档 <http://php.net/manual/zh/class.spldoublylinkedlist.php>。

标准库文档见 <http://php.net/manual/zh/class.splqueue.php>。
EOT,
])]
final class QueueTest extends TestCase
{
    #[Api([
        'zh-CN:title' => '队列基本使用方法',
        'zh-CN:description' => <<<'EOT'
队列是一种操作受限的线性表数据结构，包含两个操作。入队 enqueue，放一个数据到队列尾部; 出队 dequeue，从队列头部取一个元素。
EOT,
    ])]
    public function testBaseUse(): void
    {
        $queue = new Queue();
        static::assertSame(0, $queue->count());

        // 入对 5
        $queue->enqueue(5);
        static::assertSame(1, $queue->count());

        // 入对 6
        $queue->enqueue(6);
        static::assertSame(2, $queue->count());

        // 出对，先进先出
        static::assertSame(5, $queue->dequeue());
        static::assertSame(1, $queue->count());

        // 出对，先进先出
        static::assertSame(6, $queue->dequeue());
        static::assertSame(0, $queue->count());
    }

    #[Api([
        'zh-CN:title' => '队列支持元素类型限定',
    ])]
    public function testValidateType(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('The element type must be one of the following `string`.');

        $queue = new Queue(['string']);
        $queue->enqueue(5);
    }
}
