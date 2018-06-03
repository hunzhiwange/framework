<?php declare(strict_types=1);
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

use Tests\TestCase;
use Leevel\Http\Bag;

/**
 * Bag test
 * This class borrows heavily from the Symfony4 Framework and is part of the symfony package
 * 
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2018.03.10
 * @version 1.0
 * @see Symfony\Component\HttpFoundation (https://github.com/symfony/symfony)
 */
class BagTest extends TestCase
{

    public function testAll()
    {
        $bag = new Bag(array('foo' => 'bar'));
        $this->assertEquals(array('foo' => 'bar'), $bag->all(), '->all() gets all the input');
    }

    public function testKeys()
    {
        $bag = new Bag(array('foo' => 'bar'));
        $this->assertEquals(array('foo'), $bag->keys());
    }

    public function testAdd()
    {
        $bag = new Bag(array('foo' => 'bar'));
        $bag->add(array('bar' => 'bas'));
        $this->assertEquals(array('foo' => 'bar', 'bar' => 'bas'), $bag->all());
    }

    public function testRemove()
    {
        $bag = new Bag(array('foo' => 'bar'));
        $bag->add(array('bar' => 'bas'));
        $this->assertEquals(array('foo' => 'bar', 'bar' => 'bas'), $bag->all());
        $bag->remove('bar');
        $this->assertEquals(array('foo' => 'bar'), $bag->all());
    }

    public function testReplace()
    {
        $bag = new Bag(array('foo' => 'bar'));
        $bag->replace(array('FOO' => 'BAR'));
        $this->assertEquals(array('FOO' => 'BAR'), $bag->all(), '->replace() replaces the input with the argument');
        $this->assertFalse($bag->has('foo'), '->replace() overrides previously set the input');
    }

    public function testGet()
    {
        $bag = new Bag(array('foo' => 'bar', 'null' => null));
        $this->assertEquals('bar', $bag->get('foo'), '->get() gets the value of a parameter');
        $this->assertEquals('default', $bag->get('unknown', 'default'), '->get() returns second argument as default if a parameter is not defined');
        $this->assertNull($bag->get('null', 'default'), '->get() returns null if null is set');
    }

    public function testSet()
    {
        $bag = new Bag(array());
        $bag->set('foo', 'bar');
        $this->assertEquals('bar', $bag->get('foo'), '->set() sets the value of parameter');
        $bag->set('foo', 'baz');
        $this->assertEquals('baz', $bag->get('foo'), '->set() overrides previously set parameter');
    }

    public function testHas()
    {
        $bag = new Bag(array('foo' => 'bar'));
        $this->assertTrue($bag->has('foo'), '->has() returns true if a parameter is defined');
        $this->assertFalse($bag->has('unknown'), '->has() return false if a parameter is not defined');
    }

    public function testGetIterator()
    {
        $parameters = array('foo' => 'bar', 'hello' => 'world');
        $bag = new Bag($parameters);

        $i = 0;
        foreach ($bag as $key => $val) {
            ++ $i;
            $this->assertEquals($parameters[$key], $val);
        }

        $this->assertEquals(count($parameters), $i);
    }

    public function testCount()
    {
        $parameters = array('foo' => 'bar', 'hello' => 'world');
        $bag = new Bag($parameters);
        $this->assertCount(count($parameters), $bag);
    }

    public function testToJson()
    {
        $parameters = array('foo' => 'bar', 'hello' => 'world');
        $bag = new Bag($parameters);
        $this->assertEquals($bag->toJson(), '{"foo":"bar","hello":"world"}');
    }

    public function testFilter()
    {
        $parameters = array('foo' => '- 1234', 'hello' => 'world', 'number' => ' 12.11');
        $bag = new Bag($parameters);
        $this->assertSame($bag->filter('foo|intval'), 0);
        $this->assertSame($bag->get('number|intval'), 12);

        $this->assertSame($bag->get('foo|substr=1|intval'), 1234);

        $this->assertEquals($bag->get('foo|tests\Router\custom_func=hello,**,concact'), 'hello-- 1234-concact');

        $this->assertEquals($bag->filter('foo|tests\Router\custom_func=hello,**,concact', null, 'substr=5'), '-- 1234-concact');

        $this->assertEquals($bag->filter('foo|substr=5', null, 'tests\Router\custom_func=hello,**,concact'), 'hello-4-concact');

        $this->assertEquals($bag->get('foo|tests\Router\custom_func=hello,**,MY_CONST'), 'hello-- 1234-hello const');

        $this->assertEquals($bag->get('no|default=5'), '5');
        $this->assertEquals($bag->get('no|default=helloworld'), 'helloworld');
        $this->assertEquals($bag->get('no|default=MY_CONST'), 'hello const');
    }

    public function testGetPartData() {
        $parameters = array(
            'foo' => [
                'hello' => 'world', 
                'namepace' => ['sub' => 'i am here']
            ]
        );
        $bag = new Bag($parameters);

        $this->assertEquals($bag->get('foo\hello'), 'world');
        $this->assertEquals($bag->get('foo\namepace.sub'), 'i am here');
        $this->assertEquals($bag->get('foo\namepace.sub|substr=2'), 'am here');
    }
}

function custom_func($arg1, $arg2, $arg3) {
    return $arg1 . '-' . $arg2 . '-' . $arg3;
}

if (! defined('MY_CONST')) {
    define('MY_CONST', 'hello const');
}
