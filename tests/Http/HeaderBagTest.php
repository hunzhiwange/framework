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

namespace Tests\Http;

use Leevel\Http\HeaderBag;
use Tests\TestCase;

/**
 * HeaderBagTest test
 * This class borrows heavily from the Symfony4 Framework and is part of the symfony package.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.03.24
 *
 * @version 1.0
 *
 * @see Symfony\Component\HttpFoundation (https://github.com/symfony/symfony)
 * @coversNothing
 */
class HeaderBagTest extends TestCase
{
    public function testConstructor()
    {
        $bag = new HeaderBag(['foo' => 'bar']);
        $this->assertTrue($bag->has('foo'));
    }

    public function testToStringNull()
    {
        $bag = new HeaderBag();
        $this->assertSame('', $bag->__toString());
    }

    public function testToStringNotNull()
    {
        $bag = new HeaderBag(['foo' => 'bar']);
        $this->assertSame("Foo: bar\r\n", $bag->__toString());
    }

    public function testKeys()
    {
        $bag = new HeaderBag(['foo' => 'bar']);
        $keys = $bag->keys();
        $this->assertSame('foo', $keys[0]);
    }

    public function testAll()
    {
        $bag = new HeaderBag(['foo' => 'bar']);
        $this->assertSame(['foo' => 'bar'], $bag->all(), '->all() gets all the input');

        $bag = new HeaderBag(['FOO' => 'BAR']);
        $this->assertSame(['foo' => 'BAR'], $bag->all(), '->all() gets all the input key are lower case');
    }

    public function testReplace()
    {
        $bag = new HeaderBag(['foo' => 'bar']);
        $bag->replace(['NOPE' => 'BAR']);
        $this->assertSame(['nope' => 'BAR'], $bag->all(), '->replace() replaces the input with the argument');
        $this->assertFalse($bag->has('foo'), '->replace() overrides previously set the input');
    }

    public function testGet()
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

    public function testGetIterator()
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

    public function testCount()
    {
        $headers = ['foo' => 'bar', 'HELLO' => 'WORLD'];
        $headerBag = new HeaderBag($headers);
        $this->assertCount(count($headers), $headerBag);
    }
}
