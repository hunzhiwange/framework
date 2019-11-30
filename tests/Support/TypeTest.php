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
 *
 * @api(
 *     title="类型",
 *     path="component/support/type",
 *     description="QueryPHP 提供了增加 PHP 自身类型的辅助方法。",
 * )
 */
class TypeTest extends TestCase
{
    /**
     * @api(
     *     title="判断是否为字符串",
     *     description="",
     *     note="",
     * )
     */
    public function testTypeString(): void
    {
        // string
        $this->assertTrue(Type::type('foo', 'str'));
        $this->assertTrue(Type::type('foo', 'string'));
        $this->assertFalse(Type::type(1, 'str'));
    }

    /**
     * @api(
     *     title="判断是否为整型",
     *     description="",
     *     note="",
     * )
     */
    public function testTypeInt(): void
    {
        // int
        $this->assertTrue(Type::type(1, 'int'));
        $this->assertTrue(Type::type(3, 'integer'));
        $this->assertFalse(Type::type(true, 'int'));
    }

    /**
     * @api(
     *     title="判断是否为浮点数",
     *     description="",
     *     note="",
     * )
     */
    public function testTypeFloat(): void
    {
        // float
        $this->assertTrue(Type::type(1.1, 'float'));
        $this->assertTrue(Type::type(3.14, 'double'));
        $this->assertFalse(Type::type(true, 'double'));
    }

    /**
     * @api(
     *     title="判断是否为布尔值",
     *     description="",
     *     note="",
     * )
     */
    public function testTypeBool(): void
    {
        // bool
        $this->assertTrue(Type::type(true, 'bool'));
        $this->assertTrue(Type::type(false, 'boolean'));
        $this->assertFalse(Type::type(4, 'boolean'));
    }

    /**
     * @api(
     *     title="判断是否为数字",
     *     description="",
     *     note="",
     * )
     */
    public function testTypeNum(): void
    {
        // num
        $this->assertTrue(Type::type(1.2, 'num'));
        $this->assertTrue(Type::type(2, 'numeric'));
        $this->assertTrue(Type::type('2.5', 'numeric'));
        $this->assertFalse(Type::type(false, 'numeric'));
    }

    /**
     * @api(
     *     title="判断是否为标量",
     *     description="",
     *     note="",
     * )
     */
    public function testTypeBase(): void
    {
        // base
        $this->assertTrue(Type::type(1, 'base'));
        $this->assertTrue(Type::type('hello world', 'scalar'));
        $this->assertTrue(Type::type(0, 'base'));
        $this->assertTrue(Type::type(false, 'scalar'));
        $this->assertTrue(Type::type(1.1, 'base'));
        $this->assertTrue(Type::type(false, 'scalar'));
        $this->assertFalse(Type::type([], 'scalar'));
        $this->assertFalse(Type::type(null, 'scalar'));
    }

    /**
     * @api(
     *     title="判断是否为资源",
     *     description="",
     *     note="",
     * )
     */
    public function testTypeResource(): void
    {
        // resource
        $testFile = __DIR__.'/test.txt';
        file_put_contents($testFile, 'foo');
        $resource = fopen($testFile, 'r');
        $this->assertTrue(Type::type($resource, 'handle'));
        $this->assertFalse(Type::type(4, 'resource'));
        fclose($resource);
        unlink($testFile);
    }

    /**
     * @api(
     *     title="判断是否为闭包",
     *     description="",
     *     note="",
     * )
     */
    public function testTypeClosure(): void
    {
        // closure
        $this->assertTrue(Type::type(function () {
        }, 'closure'));
        $this->assertFalse(Type::type(true, 'closure'));
    }

    /**
     * @api(
     *     title="判断是否为数组",
     *     description="",
     *     note="",
     * )
     */
    public function testTypeArray(): void
    {
        // array
        $this->assertTrue(Type::type([], 'arr'));
        $this->assertTrue(Type::type([], 'array'));
        $this->assertFalse(Type::type(null, 'arr'));
        $this->assertFalse(Type::type(null, 'arr:int'));
        $this->assertTrue(Type::type([1, 2], 'arr:int'));
    }

    /**
     * @api(
     *     title="判断是否为对象",
     *     description="",
     *     note="",
     * )
     */
    public function testTypeObject(): void
    {
        // object
        $this->assertTrue(Type::type(new stdClass(), 'obj'));
        $this->assertTrue(Type::type(new stdClass(), 'object'));
        $this->assertFalse(Type::type(null, 'object'));
    }

    /**
     * @api(
     *     title="判断是否为 NULL",
     *     description="",
     *     note="",
     * )
     */
    public function testTypeNull(): void
    {
        // null
        $this->assertTrue(Type::type(null, 'null'));
        $this->assertFalse(Type::type(1, 'null'));
    }

    /**
     * @api(
     *     title="判断是否为回调",
     *     description="
     * **\Tests\Support\Callback1 定义**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Support\Callback1::class)]}
     * ```
     * ",
     *     note="",
     * )
     */
    public function testTypeCallback(): void
    {
        // callback
        $this->assertTrue(Type::type(function () {
        }, 'callback'));
        $this->assertTrue(Type::type('md5', 'callback'));
        $this->assertTrue(Type::type([new Callback1(), 'test'], 'callback'));
        $this->assertTrue(Type::type([Callback1::class, 'test2'], 'callback'));
        $this->assertFalse(Type::type(1, 'callback'));
    }

    /**
     * @api(
     *     title="判断是否为对象实例",
     *     description="
     * **\Tests\Support\Callback2 定义**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Support\Callback2::class)]}
     * ```
     *
     * **\Tests\Support\IInterface 定义**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Support\IInterface::class)]}
     * ```
     * ",
     *     note="",
     * )
     */
    public function testTypeInstance(): void
    {
        // instance
        $this->assertTrue(Type::type(new stdClass(), stdClass::class));
        $this->assertTrue(Type::type(new Callback1(), Callback1::class));
        $this->assertTrue(Type::type(new Callback2(), IInterface::class));
        $this->assertFalse(Type::type(1, 'callback'));
    }

    /**
     * @api(
     *     title="判断是否为数字或者字符串数字",
     *     description="包括短横线、英文逗号组成，比如日期、千分位等。",
     *     note="",
     * )
     */
    public function testTypeNumeric(): void
    {
        $this->assertTrue(Type::typeNumeric(2.2));
        $this->assertTrue(Type::typeNumeric(4));
        $this->assertTrue(Type::typeNumeric('2.5'));
        $this->assertTrue(Type::typeNumeric('2,111,500'));
        $this->assertTrue(Type::typeNumeric('2018-06-10'));
        $this->assertTrue(Type::typeNumeric('2,111,500-200'));
    }

    /**
     * @api(
     *     title="判断是否为整型或者字符串整型",
     *     description="",
     *     note="",
     * )
     */
    public function testTypeStringInt(): void
    {
        $this->assertTrue(Type::typeInt(1));
        $this->assertTrue(Type::typeInt('4'));
    }

    /**
     * @api(
     *     title="判断是否为指定的几种类型",
     *     description="",
     *     note="",
     * )
     */
    public function testTypeThese(): void
    {
        $this->assertTrue(Type::typeThese('foo', ['string']));
        $this->assertTrue(Type::typeThese(1, ['string', 'int']));
        $this->assertTrue(Type::typeThese('foo', 'string'));
    }

    public function testTypeTheseException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The param must be string or an array of string elements.');

        $this->assertTrue(Type::typeThese('foo', [[]]));
    }

    /**
     * @api(
     *     title="判断是否为数组元素类型",
     *     description="",
     *     note="",
     * )
     */
    public function testTypeStrictArray(): void
    {
        $this->assertFalse(Type::typeArray('foo', ['string']));
        $this->assertTrue(Type::typeArray(['foo'], ['string']));
        $this->assertFalse(Type::typeArray([1, 2], ['string']));
        $this->assertTrue(Type::typeArray(['bar', 'foo'], ['string']));
        $this->assertTrue(Type::typeArray(['bar', 2], ['string', 'int']));
    }
}

class Callback1
{
    public function test(): void
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
