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
use Leevel\Http\HeaderBag;
    
/**
 * HeaderBagTest test
 * This class borrows heavily from the Symfony4 Framework and is part of the symfony package
 * 
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2018.03.24
 * @version 1.0
 * @see Symfony\Component\HttpFoundation (https://github.com/symfony/symfony)
 */
class HeaderBagTest extends TestCase
{
    public function testConstructor()
    {
        $bag = new HeaderBag(array('foo' => 'bar')); 
        $this->assertTrue($bag->has('foo'));
    }
    
    public function testToStringNull()
    {
        $bag = new HeaderBag();
        $this->assertEquals('', $bag->__toString());
    }

    public function testToStringNotNull()
    {
        $bag = new HeaderBag(array('foo' => 'bar'));
        $this->assertEquals("Foo: bar\r\n", $bag->__toString());
    }    
    
    public function testKeys()
    {
        $bag = new HeaderBag(array('foo' => 'bar'));
        $keys = $bag->keys();
        $this->assertEquals('foo', $keys[0]);
    }

    public function testAll()
    {
        $bag = new HeaderBag(array('foo' => 'bar'));
        $this->assertEquals(array('foo' => 'bar'), $bag->all(), '->all() gets all the input');
        
        $bag = new HeaderBag(array('FOO' => 'BAR'));
        $this->assertEquals(array('foo' => 'BAR'), $bag->all(), '->all() gets all the input key are lower case');
    }    
    
    public function testReplace()
    {
        $bag = new HeaderBag(array('foo' => 'bar'));
        $bag->replace(array('NOPE' => 'BAR'));
        $this->assertEquals(array('nope' => 'BAR'), $bag->all(), '->replace() replaces the input with the argument');
        $this->assertFalse($bag->has('foo'), '->replace() overrides previously set the input');
    }    
    
    public function testGet()
    {
        $bag = new HeaderBag(array('foo' => 'bar', 'fuzz' => 'bizz'));
        $this->assertEquals('bar', $bag->get('foo'), '->get return current value');
        $this->assertEquals('bar', $bag->get('FoO'), '->get key in case insensitive');
        $this->assertEquals('bar', $bag->get('foo', 'nope'), '->get return the value');

        // defaults
        $this->assertNull($bag->get('none'), '->get unknown values returns null');
        $this->assertEquals('default', $bag->get('none', 'default'), '->get unknown values returns default');
        
        $bag->set('foo', 'bor');
        $this->assertEquals('bor', $bag->get('foo'), '->get return a new value');
        $this->assertEquals('bor', $bag->get('foo', 'nope'), '->get return');
    }   
   
    public function testGetIterator()
    {
        $headers = array('foo' => 'bar', 'hello' => 'world', 'third' => 'charm');
        
        $headerBag = new HeaderBag($headers);
        
        $i = 0;
        foreach ($headerBag as $key => $val) {
            ++$i;
            $this->assertEquals($headers[$key], $val);
        }

        $this->assertEquals(count($headers), $i);
    }

    public function testCount()
    {
        $headers = array('foo' => 'bar', 'HELLO' => 'WORLD');
        $headerBag = new HeaderBag($headers);
        $this->assertCount(count($headers), $headerBag);
    }
}

