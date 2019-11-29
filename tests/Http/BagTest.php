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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Http;

use Leevel\Http\Bag;
use Tests\TestCase;

/**
 * Bag test.
 *
 * - This class borrows heavily from the Symfony4 Framework and is part of the symfony package.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.03.10
 *
 * @version 1.0
 *
 * @see Symfony\Component\HttpFoundation (https://github.com/symfony/symfony)
 */
class BagTest extends TestCase
{
    public function testAll(): void
    {
        $bag = new Bag(['foo' => 'bar']);
        $this->assertSame(['foo' => 'bar'], $bag->all(), '->all() gets all the input');
    }

    public function testKeys(): void
    {
        $bag = new Bag(['foo' => 'bar']);
        $this->assertSame(['foo'], $bag->keys());
    }

    public function testAdd(): void
    {
        $bag = new Bag(['foo' => 'bar']);
        $bag->add(['bar' => 'bas']);
        $this->assertSame(['foo' => 'bar', 'bar' => 'bas'], $bag->all());
    }

    public function testRemove(): void
    {
        $bag = new Bag(['foo' => 'bar']);
        $bag->add(['bar' => 'bas']);
        $this->assertSame(['foo' => 'bar', 'bar' => 'bas'], $bag->all());
        $bag->remove('bar');
        $this->assertSame(['foo' => 'bar'], $bag->all());
    }

    public function testReplace(): void
    {
        $bag = new Bag(['foo' => 'bar']);
        $bag->replace(['FOO' => 'BAR']);
        $this->assertSame(['FOO' => 'BAR'], $bag->all(), '->replace() replaces the input with the argument');
        $this->assertFalse($bag->has('foo'), '->replace() overrides previously set the input');
    }

    public function testGet(): void
    {
        $bag = new Bag(['foo' => 'bar', 'null' => null]);
        $this->assertSame('bar', $bag->get('foo'), '->get() gets the value of a param');
        $this->assertSame('default', $bag->get('unknown', 'default'), '->get() returns second argument as default if a param is not defined');
        $this->assertNull($bag->get('null', 'default'), '->get() returns null if null is set');
    }

    public function testSet(): void
    {
        $bag = new Bag([]);
        $bag->set('foo', 'bar');
        $this->assertSame('bar', $bag->get('foo'), '->set() sets the value of param');
        $bag->set('foo', 'baz');
        $this->assertSame('baz', $bag->get('foo'), '->set() overrides previously set param');
    }

    public function testHas(): void
    {
        $bag = new Bag(['foo' => 'bar']);
        $this->assertTrue($bag->has('foo'), '->has() returns true if a param is defined');
        $this->assertFalse($bag->has('unknown'), '->has() return false if a param is not defined');
    }

    public function testGetIterator(): void
    {
        $params = ['foo' => 'bar', 'hello' => 'world'];
        $bag = new Bag($params);

        $i = 0;
        foreach ($bag as $key => $val) {
            $i++;
            $this->assertSame($params[$key], $val);
        }

        $this->assertSame(count($params), $i);
    }

    public function testCount(): void
    {
        $params = ['foo' => 'bar', 'hello' => 'world'];
        $bag = new Bag($params);
        $this->assertCount(count($params), $bag);
    }

    public function testToJson(): void
    {
        $params = ['foo' => 'bar', 'hello' => 'world'];
        $bag = new Bag($params);
        $this->assertSame($bag->toJson(), '{"foo":"bar","hello":"world"}');
    }

    public function testFilter(): void
    {
        $params = ['foo' => '- 1234', 'hello' => 'world', 'number' => ' 12.11'];
        $bag = new Bag($params);
        $this->assertSame($bag->filter('foo|intval'), 0);
        $this->assertSame($bag->get('number|intval'), 12);

        $this->assertSame($bag->get('foo|substr=1|intval'), 1234);

        $this->assertSame($bag->get('foo|Tests\\Http\\custom_func=hello,**,concact'), 'hello-- 1234-concact');

        $this->assertSame($bag->filter('foo|Tests\\Http\\custom_func=hello,**,concact', null, 'substr=5'), '-- 1234-concact');

        $this->assertSame($bag->filter('foo|substr=5', null, 'Tests\\Http\\custom_func=hello,**,concact'), 'hello-4-concact');

        $this->assertSame($bag->get('foo|Tests\\Http\\custom_func=hello,**,MY_CONST'), 'hello-- 1234-hello const');

        $this->assertSame($bag->get('no|default=5'), 5);
        $this->assertSame($bag->get('no|default=helloworld'), 'helloworld');
        $this->assertSame($bag->get('no|default=MY_CONST'), 'hello const');
    }

    public function testGetPartData(): void
    {
        $params = [
            'foo' => [
                'hello'    => 'world',
                'namepace' => ['sub' => 'i am here'],
            ],
        ];
        $bag = new Bag($params);

        $this->assertSame($bag->get('foo\\hello'), 'world');
        $this->assertSame($bag->get('foo\\namepace.sub'), 'i am here');
        $this->assertSame($bag->get('foo\\namepace.sub|substr=2'), 'am here');
    }

    public function testGetPartDataButNotArray(): void
    {
        $params = [
            'bar' => 'helloworld',
        ];
        $bag = new Bag($params);

        $this->assertSame($bag->get('bar\\hello'), 'helloworld');
    }

    public function testGetPartDataButSubNotFoundInArray(): void
    {
        $params = [
            'bar' => ['hello'    => 'world'],
        ];
        $bag = new Bag($params);

        $this->assertSame($bag->get('bar\\hello.world.sub', 'defaults'), 'defaults');
    }

    public function testToString(): void
    {
        $params = ['foo' => 'bar', 'hello' => 'world'];
        $bag = new Bag($params);

        $this->assertSame($bag->__toString(), '{"foo":"bar","hello":"world"}');
        $this->assertSame((string) ($bag), '{"foo":"bar","hello":"world"}');
    }

    public function testFilterValueWithFilterId(): void
    {
        $params = ['foo' => 'bar', 'hello' => 'world'];
        $bag = new Bag($params);

        $this->assertSame($bag->get('foo|email'), 'bar');
    }

    public function testFilterValueWithFilterIdReturnFalse(): void
    {
        $params = ['foo' => 'bar', 'hello' => 'world'];
        $bag = new Bag($params);

        $this->assertSame($bag->get('foo|validate_email', 'hello'), 'hello');
    }

    public function testFilterValueWithFilterIdReturnTrue(): void
    {
        $params = ['foo' => 'hello@foo.com', 'hello' => 'world'];
        $bag = new Bag($params);

        $this->assertSame($bag->get('foo|validate_email', 'hello'), 'hello@foo.com');
    }

    public function testFilterValueWithRealInt(): void
    {
        $params = ['foo' => 'hello@foo.com', 'hello' => 'world'];
        $bag = new Bag($params);

        $this->assertSame($bag->filter('foo', 'hello', [FILTER_VALIDATE_EMAIL]), 'hello@foo.com');
    }

    public function testFilterValueWithOption(): void
    {
        $params = ['foo' => '0755'];
        $bag = new Bag($params);

        $options = [
            'options' => [
                'default'   => 3, // value to return if the filter fails
                'min_range' => 0, // other options here
            ],
            'flags' => FILTER_FLAG_ALLOW_OCTAL,
        ];

        $this->assertSame(493, filter_var('0755', FILTER_VALIDATE_INT, $options));

        $this->assertSame($bag->filter('foo', 'hello', [FILTER_VALIDATE_INT], $options), 493);
    }
}

function custom_func($arg1, $arg2, $arg3)
{
    return $arg1.'-'.$arg2.'-'.$arg3;
}

if (!defined('MY_CONST')) {
    define('MY_CONST', 'hello const');
}
