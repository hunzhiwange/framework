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

namespace Tests\Support;

use Leevel\Support\Type;
use stdClass;
use Tests\TestCase;

/**
 * type test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.10
 *
 * @version 1.0
 * @coversNothing
 */
class TypeTest extends TestCase
{
    public function testBaseUse()
    {
        // string
        $this->assertTrue(Type::vars('foo', 'str'));
        $this->assertTrue(Type::vars('foo', 'string'));
        $this->assertFalse(Type::vars(1, 'str'));

        // int
        $this->assertTrue(Type::vars(1, 'int'));
        $this->assertTrue(Type::vars(3, 'integer'));
        $this->assertFalse(Type::vars(true, 'int'));

        // float
        $this->assertTrue(Type::vars(1.1, 'float'));
        $this->assertTrue(Type::vars(3.14, 'double'));
        $this->assertFalse(Type::vars(true, 'double'));

        // bool
        $this->assertTrue(Type::vars(true, 'bool'));
        $this->assertTrue(Type::vars(false, 'boolean'));
        $this->assertFalse(Type::vars(4, 'boolean'));

        // num
        $this->assertTrue(Type::vars(1.2, 'num'));
        $this->assertTrue(Type::vars(2, 'numeric'));
        $this->assertTrue(Type::vars('2.5', 'numeric'));
        $this->assertFalse(Type::vars(false, 'numeric'));

        // base
        $this->assertTrue(Type::vars(1, 'base'));
        $this->assertTrue(Type::vars('hello world', 'scalar'));
        $this->assertTrue(Type::vars(0, 'base'));
        $this->assertTrue(Type::vars(false, 'scalar'));
        $this->assertTrue(Type::vars(1.1, 'base'));
        $this->assertTrue(Type::vars(false, 'scalar'));
        $this->assertFalse(Type::vars([], 'scalar'));
        $this->assertFalse(Type::vars(null, 'scalar'));

        // resource
        $resource = fopen(__DIR__.'/test.txt', 'r');
        $this->assertTrue(Type::vars($resource, 'handle'));
        $this->assertFalse(Type::vars(4, 'resource'));
        fclose($resource);

        // closure
        $this->assertTrue(Type::vars(function () {
        }, 'closure'));
        $this->assertFalse(Type::vars(true, 'closure'));

        // array
        $this->assertTrue(Type::vars([], 'arr'));
        $this->assertTrue(Type::vars([], 'array'));
        $this->assertFalse(Type::vars(null, 'arr'));

        // object
        $this->assertTrue(Type::vars(new stdClass(), 'obj'));
        $this->assertTrue(Type::vars(new stdClass(), 'object'));
        $this->assertFalse(Type::vars(null, 'object'));

        // null
        $this->assertTrue(Type::vars(null, 'null'));
        $this->assertFalse(Type::vars(1, 'null'));

        // callback
        $this->assertTrue(Type::vars(function () {
        }, 'callback'));
        $this->assertTrue(Type::vars('md5', 'callback'));
        $this->assertTrue(Type::vars([new Callback1(), 'test'], 'callback'));
        $this->assertTrue(Type::vars([Callback1::class, 'test2'], 'callback'));
        $this->assertFalse(Type::vars(1, 'callback'));

        // instance
        $this->assertTrue(Type::vars(new stdClass(), stdClass::class));
        $this->assertTrue(Type::vars(new Callback1(), Callback1::class));
        $this->assertTrue(Type::vars(new Callback2(), IInterface::class));
        $this->assertFalse(Type::vars(1, 'callback'));
    }

    public function testNum()
    {
        $this->assertTrue(Type::num(2.2));
        $this->assertTrue(Type::num(4));
        $this->assertTrue(Type::num('2.5'));
        $this->assertTrue(Type::num('2,111,500'));
        $this->assertTrue(Type::num('2018-06-10'));
        $this->assertTrue(Type::num('2,111,500-200'));
    }

    public function testInts()
    {
        $this->assertTrue(Type::ints(1));
        $this->assertTrue(Type::ints('4'));
    }

    public function testThese()
    {
        $this->assertTrue(Type::these('foo', ['string']));
        $this->assertTrue(Type::these(1, ['string', 'int']));
    }

    public function testTheseException()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->assertTrue(Type::these('foo', [[]]));
    }

    public function testArr()
    {
        $this->assertFalse(Type::arr('foo', ['string']));
        $this->assertTrue(Type::arr(['foo'], ['string']));
        $this->assertFalse(Type::arr([1, 2], ['string']));
        $this->assertTrue(Type::arr(['bar', 'foo'], ['string']));
        $this->assertTrue(Type::arr(['bar', 2], ['string', 'int']));
    }
}

class Callback1
{
    public function test()
    {
    }

    public static function test2()
    {
    }
}

interface IInterface
{
}

class Callback2 implements IInterface
{
}
