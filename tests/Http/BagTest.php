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

use Leevel\Http\Bag;
use stdClass;
use Tests\TestCase;

/**
 * - This class borrows heavily from the Symfony4 Framework and is part of the symfony package.
 *
 * @see Symfony\Component\HttpFoundation (https://github.com/symfony/symfony)
 *
 * @api(
 *     title="Bag",
 *     path="component/http/bag",
 *     description="Http 组件的一些基本数据有一个基础的包装 `\Leevel\Http\Bag`。",
 * )
 */
class BagTest extends TestCase
{
    /**
     * @api(
     *     title="all 取回所有元素",
     *     description="",
     *     note="",
     * )
     */
    public function testAll(): void
    {
        $bag = new Bag(['foo' => 'bar']);
        $this->assertSame(['foo' => 'bar'], $bag->all(), '->all() gets all the input');
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
        $bag = new Bag(['foo' => 'bar']);
        $this->assertSame(['foo'], $bag->keys());
    }

    /**
     * @api(
     *     title="add 新增元素",
     *     description="",
     *     note="",
     * )
     */
    public function testAdd(): void
    {
        $bag = new Bag(['foo' => 'bar']);
        $bag->add(['bar' => 'bas']);
        $this->assertSame(['foo' => 'bar', 'bar' => 'bas'], $bag->all());
    }

    /**
     * @api(
     *     title="remove 删除元素值",
     *     description="",
     *     note="",
     * )
     */
    public function testRemove(): void
    {
        $bag = new Bag(['foo' => 'bar']);
        $bag->add(['bar' => 'bas']);
        $this->assertSame(['foo' => 'bar', 'bar' => 'bas'], $bag->all());
        $bag->remove('bar');
        $this->assertSame(['foo' => 'bar'], $bag->all());
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
        $bag = new Bag(['foo' => 'bar']);
        $bag->replace(['FOO' => 'BAR']);
        $this->assertSame(['FOO' => 'BAR'], $bag->all(), '->replace() replaces the input with the argument');
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
        $bag = new Bag(['foo' => 'bar', 'null' => null]);
        $this->assertSame('bar', $bag->get('foo'), '->get() gets the value of a param');
        $this->assertSame('default', $bag->get('unknown', 'default'), '->get() returns second argument as default if a param is not defined');
        $this->assertNull($bag->get('null', 'default'), '->get() returns null if null is set');
    }

    /**
     * @api(
     *     title="set 设置元素值",
     *     description="",
     *     note="",
     * )
     */
    public function testSet(): void
    {
        $bag = new Bag([]);
        $bag->set('foo', 'bar');
        $this->assertSame('bar', $bag->get('foo'), '->set() sets the value of param');
        $bag->set('foo', 'baz');
        $this->assertSame('baz', $bag->get('foo'), '->set() overrides previously set param');
    }

    /**
     * @api(
     *     title="has 判断是否存在元素值",
     *     description="",
     *     note="",
     * )
     */
    public function testHas(): void
    {
        $bag = new Bag(['foo' => 'bar']);
        $this->assertTrue($bag->has('foo'), '->has() returns true if a param is defined');
        $this->assertFalse($bag->has('unknown'), '->has() return false if a param is not defined');
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
        $params = ['foo' => 'bar', 'hello' => 'world'];
        $bag = new Bag($params);

        $i = 0;
        foreach ($bag as $key => $val) {
            $i++;
            $this->assertSame($params[$key], $val);
        }

        $this->assertSame(count($params), $i);
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
        $params = ['foo' => 'bar', 'hello' => 'world'];
        $bag = new Bag($params);
        $this->assertCount(count($params), $bag);
    }

    /**
     * @api(
     *     title="toJson 实现 \Leevel\Support\IJson",
     *     description="",
     *     note="",
     * )
     */
    public function testToJson(): void
    {
        $params = ['foo' => 'bar', 'hello' => 'world'];
        $bag = new Bag($params);
        $this->assertSame($bag->toJson(), '{"foo":"bar","hello":"world"}');
    }

    /**
     * @api(
     *     title="get.filter 支持获取过滤变量，支持函数处理",
     *     description="",
     *     note="",
     * )
     */
    public function testFilter(): void
    {
        $params = ['foo' => '- 1234', 'hello' => 'world', 'number' => ' 12.11'];
        $bag = new Bag($params);

        $this->assertSame($bag->filter('foo|intval'), 0);
        $this->assertSame($bag->get('number|intval'), 12);
    }

    /**
     * @api(
     *     title="filter 支持获取过滤变量，错误的 filter_id 过滤规则例子",
     *     description="",
     *     note="",
     * )
     */
    public function testFilterWithInvalidFilterIdRule(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            '`not_callable_and_not_filter` is not a correct filter rule listed below `int,boolean,float,validate_regexp,validate_domain,validate_url,validate_email,validate_ip,validate_mac,string,stripped,encoded,special_chars,full_special_chars,unsafe_raw,email,url,number_int,number_float,magic_quotes,add_slashes,callback` provided by filter_list().'
        );

        $params = ['hello' => 'world'];
        $bag = new Bag($params);

        $bag->filter('hello|not_callable_and_not_filter');
    }

    /**
     * @api(
     *     title="get 支持获取过滤变量 filter_id 例子",
     *     description="",
     *     note="",
     * )
     */
    public function testFilterValueWithFilterId(): void
    {
        $params = ['foo' => 'bar', 'hello' => 'world'];
        $bag = new Bag($params);

        // @see https://www.php.net/manual/en/function.filter-id.php
        // email = 517
        $this->assertSame($bag->get('foo|email'), 'bar');
    }

    /**
     * @api(
     *     title="get 支持获取过滤变量验证成功例子",
     *     description="",
     *     note="",
     * )
     */
    public function testFilterValueWithFilterIdReturnFalse(): void
    {
        $params = ['foo' => 'bar', 'hello' => 'world'];
        $bag = new Bag($params);

        $this->assertSame($bag->get('foo|validate_email', 'hello'), 'hello');
    }

    /**
     * @api(
     *     title="get 支持获取过滤变量验证成功例子",
     *     description="",
     *     note="",
     * )
     */
    public function testFilterValueWithFilterIdReturnTrue(): void
    {
        $params = ['foo' => 'hello@foo.com', 'hello' => 'world'];
        $bag = new Bag($params);

        $this->assertSame($bag->get('foo|validate_email', 'hello'), 'hello@foo.com');
    }

    /**
     * @api(
     *     title="filter 获取过滤变量 Email 例子",
     *     description="",
     *     note="",
     * )
     */
    public function testFilterValueWithEmail(): void
    {
        $params = ['foo' => 'hello@foo.com', 'hello' => 'world'];
        $bag = new Bag($params);

        $this->assertSame($bag->filter('foo', 'hello', [FILTER_VALIDATE_EMAIL]), 'hello@foo.com');
    }

    /**
     * @api(
     *     title="filter 获取过滤变量支持配置",
     *     description="",
     *     note="",
     * )
     */
    public function testFilterValueWithOption(): void
    {
        $params = ['foo' => '0755'];
        $bag = new Bag($params);

        $options = [
            'options' => [
                'default'   => 3, // value to return if the filter fails
                'min_range' => 0, // other options here
            ],
            'flags' => FILTER_FLAG_ALLOW_OCTAL, // 8 进制转 10 进制
        ];

        $this->assertSame(493, filter_var('0755', FILTER_VALIDATE_INT, $options));
        $this->assertSame($bag->filter('foo', 'hello', [FILTER_VALIDATE_INT], $options), 493);
    }

    /**
     * @api(
     *     title="filter 支持获取过滤变量，错误的过滤规则例子",
     *     description="",
     *     note="",
     * )
     */
    public function testFilterWithInvalidFilterRule(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Filter item only supports callable,int or string,but gives `object`.'
        );

        $params = ['hello' => 'world'];
        $bag = new Bag($params);

        $bag->filter('hello', null, [new stdClass(['hello' => 'world'])]);
    }

    /**
     * @api(
     *     title="get 支持数组子元素访问",
     *     description="",
     *     note="",
     * )
     */
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
    }

    /**
     * @api(
     *     title="get 支持数组子元素访问，如果值本身为非数组则返回默认值",
     *     description="",
     *     note="",
     * )
     */
    public function testGetPartDataButNotArray(): void
    {
        $params = [
            'bar' => 'helloworld',
        ];
        $bag = new Bag($params);

        $this->assertSame($bag->get('bar\\hello'), null);
    }

    /**
     * @api(
     *     title="get 支持数组子元素访问，如果无法找到则返回默认值",
     *     description="",
     *     note="",
     * )
     */
    public function testGetPartDataButSubNotFoundInArray(): void
    {
        $params = [
            'bar' => ['hello'    => 'world'],
        ];
        $bag = new Bag($params);

        $this->assertSame($bag->get('bar\\hello.world.sub', 'defaults'), 'defaults');
    }

    /**
     * @api(
     *     title="实现了 __toString 魔术方法",
     *     description="",
     *     note="",
     * )
     */
    public function testToString(): void
    {
        $params = ['foo' => 'bar', 'hello' => 'world'];
        $bag = new Bag($params);

        $this->assertSame($bag->__toString(), '{"foo":"bar","hello":"world"}');
        $this->assertSame((string) ($bag), '{"foo":"bar","hello":"world"}');
    }
}
