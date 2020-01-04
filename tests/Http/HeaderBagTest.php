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

namespace Tests\Http;

use Leevel\Http\HeaderBag;
use Tests\TestCase;

/**
 * - This class borrows heavily from the Symfony4 Framework and is part of the symfony package.
 *
 * @see Symfony\Component\HttpFoundation (https://github.com/symfony/symfony)
 *
 * @api(
 *     title="Header Bag",
 *     path="component/http/headerbag",
 *     description="QueryPHP 提供了一个 header 包装 `\Leevel\Http\HeaderBag` 对象。",
 * )
 */
class HeaderBagTest extends TestCase
{
    /**
     * @api(
     *     title="创建一个 header 包装",
     *     description="",
     *     note="",
     * )
     */
    public function testConstructor(): void
    {
        $bag = new HeaderBag(['foo' => 'bar']);
        $this->assertTrue($bag->has('foo'));
    }

    public function testToStringNull(): void
    {
        $bag = new HeaderBag();
        $this->assertSame('', $bag->__toString());
    }

    /**
     * @api(
     *     title="实现了 __toString 魔术方法",
     *     description="",
     *     note="",
     * )
     */
    public function testToStringNotNull(): void
    {
        $bag = new HeaderBag(['foo' => 'bar']);
        $this->assertSame("Foo: bar\r\n", $bag->__toString());
    }

    /**
     * @api(
     *     title="keys 返回所有元素键值",
     *     description="",
     *     note="",
     * )
     */
    public function testKeys(): void
    {
        $bag = new HeaderBag(['foo' => 'bar']);
        $keys = $bag->keys();
        $this->assertSame('foo', $keys[0]);
    }

    /**
     * @api(
     *     title="all 取回所有元素",
     *     description="",
     *     note="",
     * )
     */
    public function testAll(): void
    {
        $bag = new HeaderBag(['foo' => 'bar']);
        $this->assertSame(['foo' => 'bar'], $bag->all(), '->all() gets all the input');

        $bag = new HeaderBag(['FOO' => 'BAR']);
        $this->assertSame(['foo' => 'BAR'], $bag->all(), '->all() gets all the input key are lower case');
    }

    /**
     * @api(
     *     title="replace 替换当前所有元素",
     *     description="",
     *     note="",
     * )
     */
    public function testReplace(): void
    {
        $bag = new HeaderBag(['foo' => 'bar']);
        $bag->replace(['NOPE' => 'BAR']);
        $this->assertSame(['nope' => 'BAR'], $bag->all(), '->replace() replaces the input with the argument');
        $this->assertFalse($bag->has('foo'), '->replace() overrides previously set the input');
    }

    /**
     * @api(
     *     title="get 取回元素值",
     *     description="",
     *     note="",
     * )
     */
    public function testGet(): void
    {
        $bag = new HeaderBag(['foo' => 'bar', 'fuzz' => 'bizz']);
        $this->assertSame('bar', $bag->get('foo'), '->get return current value');
        $this->assertSame('bar', $bag->get('FoO'), '->get key in case insensitive');
        $this->assertSame('bar', $bag->get('foo', 'nope'), '->get return the value');

        // defaults
        $this->assertNull($bag->get('none'), '->get unknown values returns null');
        $this->assertSame('default', $bag->get('none', 'default'), '->get unknown values returns default');

        $bag->set('foo', 'bor');
        $this->assertSame('bor', $bag->get('foo'), '->get return a new value');
        $this->assertSame('bor', $bag->get('foo', 'nope'), '->get return');
    }

    /**
     * @api(
     *     title="实现 \IteratorAggregate::getIterator 迭代器接口",
     *     description="",
     *     note="",
     * )
     */
    public function testGetIterator(): void
    {
        $headers = ['foo' => 'bar', 'hello' => 'world', 'third' => 'charm'];
        $headerBag = new HeaderBag($headers);

        $i = 0;
        foreach ($headerBag as $key => $val) {
            $i++;
            $this->assertSame($headers[$key], $val);
        }

        $this->assertSame(count($headers), $i);
    }

    /**
     * @api(
     *     title="实现 \Countable::count 统计接口",
     *     description="",
     *     note="",
     * )
     */
    public function testCount(): void
    {
        $headers = ['foo' => 'bar', 'HELLO' => 'WORLD'];
        $headerBag = new HeaderBag($headers);
        $this->assertCount(count($headers), $headerBag);
    }
}
