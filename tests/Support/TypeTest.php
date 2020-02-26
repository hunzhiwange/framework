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
        $this->assertTrue(Type::type('foo', 'string'));
        $this->assertFalse(Type::type(1, 'string'));
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
        $this->assertTrue(Type::type(true, 'bool'));
        $this->assertTrue(Type::type(false, 'bool'));
        $this->assertFalse(Type::type(4, 'bool'));
    }

    /**
     * @api(
     *     title="判断是否为数字",
     *     description="",
     *     note="",
     * )
     */
    public function testTypeNumeric(): void
    {
        $this->assertTrue(Type::type(1.2, 'numeric'));
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
    public function testTypeScalar(): void
    {
        $this->assertTrue(Type::type(1, 'scalar'));
        $this->assertTrue(Type::type('hello world', 'scalar'));
        $this->assertTrue(Type::type(0, 'scalar'));
        $this->assertTrue(Type::type(false, 'scalar'));
        $this->assertTrue(Type::type(1.1, 'scalar'));
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
        $testFile = __DIR__.'/test.txt';
        file_put_contents($testFile, 'foo');
        $resource = fopen($testFile, 'r');
        $this->assertTrue(Type::type($resource, 'resource'));
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
        $this->assertTrue(Type::type(function () {
        }, 'Closure'));
        $this->assertFalse(Type::type(true, 'Closure'));
    }

    /**
     * @api(
     *     title="判断是否为数组",
     *     description="
     * 格式支持
     *
     *  * 支持 PHP 内置或者自定义的 is_array,is_int,is_custom 等函数
     *  * 数组支持 array:int,string 格式，值类型
     *  * 数组支持 array:int:string,string:array 格式，键类型:值类型
     *  * 数组支持 array:string:array:string:array:string:int 无限层级格式，键类型:值类型:键类型:值类型...(值类型|键类型:值类型)
     * ",
     *     note="",
     * )
     */
    public function testTypeArray(): void
    {
        $this->assertTrue(Type::type([], 'array'));
        $this->assertTrue(Type::type([1, 2], 'array:int'));
        $this->assertFalse(Type::type([1, 2], 'array:'));
        $this->assertTrue(Type::type([1, 2], 'array:int:int'));
        $this->assertTrue(Type::type(['foo' => 1, 'bar' => 2], 'array:string:int'));
        $this->assertTrue(Type::type(['foo' => [], 'bar' => []], 'array:string:array'));
        $this->assertTrue(Type::type(['foo' => [1, 2, 3], 'bar' => [4, 5, 6]], 'array:string:array:int'));
        $this->assertFalse(Type::type(['foo' => [1, 2, 3], 'bar' => [4, 5, 6]], 'array:string:array:string'));
        $this->assertTrue(Type::type(['foo' => ['hello' => 1], 'bar' => ['hello' => 4]], 'array:string:array:string:int'));
        $this->assertTrue(Type::type(['foo' => ['hello' => ['foo' => 2]], 'bar' => ['hello' => ['foo' => 2]]], 'array:string:array:string:array:string:int'));
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
        $this->assertTrue(Type::type(function () {
        }, 'callable'));
        $this->assertTrue(Type::type('md5', 'callable'));
        $this->assertTrue(Type::type([new Callback1(), 'test'], 'callable'));
        $this->assertTrue(Type::type([Callback1::class, 'test2'], 'callable'));
        $this->assertFalse(Type::type(1, 'callable'));
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
        $this->assertTrue(Type::type(new stdClass(), stdClass::class));
        $this->assertTrue(Type::type(new Callback1(), Callback1::class));
        $this->assertTrue(Type::type(new Callback2(), IInterface::class));
        $this->assertFalse(Type::type(1, 'callback'));
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
        $this->assertTrue(Type::these('foo', ['string']));
        $this->assertTrue(Type::these(1, ['string', 'int']));
    }

    public function testTypeTheseException(): void
    {
        $this->expectException(\TypeError::class);

        $this->assertTrue(Type::these('foo', [[]]));
    }

    /**
     * @api(
     *     title="判断是否为数组元素类型",
     *     description="
     * 格式支持
     *
     *  * 数组支持 array:int,string 格式，值类型
     *  * 数组支持 array:int:string,string:array 格式，键类型:值类型
     *  * 数组支持 array:string:array:string:array:string:int 无限层级格式，键类型:值类型:键类型:值类型...(值类型|键类型:值类型)
     * ",
     *     note="",
     * )
     */
    public function testTypeStrictArray(): void
    {
        $this->assertTrue(Type::arr(['foo'], ['string']));
        $this->assertFalse(Type::arr([1, 2], ['string']));
        $this->assertTrue(Type::arr(['bar', 'foo'], ['string']));
        $this->assertTrue(Type::arr(['bar', 2], ['string', 'int']));
        $this->assertTrue(Type::arr(['hello' => 'bar', 2], ['string:string', 'int']));
        $this->assertTrue(Type::arr(['hello' => 'bar', 'foo' => 'bar'], ['string:string']));
        $this->assertFalse(Type::arr(['hello' => 'bar', 2], ['string:string']));
        $this->assertFalse(Type::arr(['foo' => [1, 2, 3], 'bar' => [4, 5, 6]], ['string:array:string']));
        $this->assertTrue(Type::arr(['foo' => ['hello' => 1], 'bar' => ['hello' => 4]], ['string:array:string:int']));
        $this->assertTrue(Type::arr(['foo' => ['hello' => ['foo' => 2]], 'bar' => ['hello' => ['foo' => 2]]], ['string:array:string:array:string:int']));
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
