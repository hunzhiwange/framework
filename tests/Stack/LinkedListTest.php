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

use Leevel\Stack\LinkedList;
use Tests\TestCase;

/**
 * linkedList test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.22
 *
 * @version 1.0
 */
class LinkedListTest extends TestCase
{
    public function testBaseUse()
    {
        $linkedList = new LinkedList();

        $this->assertSame(0, $linkedList->count());
        $this->assertNull($linkedList->pop());
        $this->assertNull($linkedList->pop());

        $linkedList->push(5);
        $linkedList->push(6);

        $this->assertSame(2, $linkedList->count());
        $this->assertSame(6, $linkedList->pop());
        $this->assertSame(5, $linkedList->pop());
        $this->assertSame(0, $linkedList->count());

        $linkedList->unshift(5);
        $linkedList->unshift(6);

        $this->assertSame(2, $linkedList->count());
        $this->assertSame(5, $linkedList->pop());
        $this->assertSame(6, $linkedList->pop());
        $this->assertSame(0, $linkedList->count());

        $linkedList->add(0, 'hello');
        $linkedList->add(1, 'world');
        $linkedList->add(2, 'foo');
        $linkedList->add(3, 'bar');

        $this->assertSame('hello', $linkedList->offsetGet(0));
        $this->assertSame('world', $linkedList->offsetGet(1));
        $this->assertSame('foo', $linkedList->offsetGet(2));
        $this->assertSame('bar', $linkedList->offsetGet(3));

        $linkedList->offsetSet(0, 'hello');
        $linkedList->offsetSet(1, 'world');
        $linkedList->offsetSet(2, 'foo');
        $linkedList->offsetSet(3, 'bar');

        $this->assertSame('hello', $linkedList->offsetGet(0));
        $this->assertSame('world', $linkedList->offsetGet(1));
        $this->assertSame('foo', $linkedList->offsetGet(2));
        $this->assertSame('bar', $linkedList->offsetGet(3));
    }

    public function testValidateType()
    {
        $this->expectException(\InvalidArgumentException::class);

        $linkedList = new LinkedList(['string']);

        $linkedList->push(5);
    }
}
