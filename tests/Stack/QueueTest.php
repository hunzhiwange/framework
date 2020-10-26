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

use Leevel\Stack\Queue;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="队列",
 *     path="component/queue",
 *     zh-CN:description="
 * 队列 (Queue) 具有先进先出的特性，支持在队列尾部插入元素，在队列头部删除元素的特性。
 *
 * 在 PHP 双向链表的基础上加上数据类型验证功能，不少业务场景中保证链表中数据一致性。
 *
 * 阻止链表返回空数据时抛出异常的默认行为。
 *
 * 底层基于 spldoublylinkedlist 开发，相关文档 <http://php.net/manual/zh/class.spldoublylinkedlist.php>。
 *
 * 标准库文档见 <http://php.net/manual/zh/class.splqueue.php>。
 * ",
 * )
 */
class QueueTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="队列基本使用方法",
     *     zh-CN:description="
     * 队列是一种操作受限的线性表数据结构，包含两个操作。入队 enqueue，放一个数据到队列尾部; 出队 dequeue，从队列头部取一个元素。
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $queue = new Queue();
        $this->assertSame(0, $queue->count());

        // 入对 5
        $queue->enqueue(5);
        $this->assertSame(1, $queue->count());

        // 入对 6
        $queue->enqueue(6);
        $this->assertSame(2, $queue->count());

        // 出对，先进先出
        $this->assertSame(5, $queue->dequeue());
        $this->assertSame(1, $queue->count());

        // 出对，先进先出
        $this->assertSame(6, $queue->dequeue());
        $this->assertSame(0, $queue->count());
    }

    /**
     * @api(
     *     zh-CN:title="队列支持元素类型限定",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testValidateType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The queue element type verification failed, and the allowed type is string.');

        $queue = new Queue(['string']);
        $queue->enqueue(5);
    }
}
