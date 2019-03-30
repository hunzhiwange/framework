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

namespace Tests\Support;

use Leevel\Support\Arr;
use Tests\TestCase;

/**
 * arr test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.15
 *
 * @version 1.0
 *
 * @api(
 *     title="数组",
 *     path="component/support/arr",
 *     description="这里为系统提供的数组使用的功能文档说明。",
 * )
 */
class ArrTest extends TestCase
{
    /**
     * @api(
     *     title="基础格式化",
     *     description="",
     *     note="",
     * )
     */
    public function testBaseUse()
    {
        $this->assertTrue(Arr::normalize(true));

        $this->assertSame(['a', 'b'], Arr::normalize('a,b'));

        $this->assertSame(['a', 'b'], Arr::normalize(['a', 'b']));

        $this->assertSame(['a'], Arr::normalize(['a', '']));

        $this->assertSame(['a'], Arr::normalize(['a', ''], ',', true));

        $this->assertSame(['a', ' 0 '], Arr::normalize(['a', ' 0 '], ',', true));

        $this->assertSame(['a', '0'], Arr::normalize(['a', ' 0 '], ','));
    }

    /**
     * @api(
     *     title="格式化字符串",
     *     description="",
     *     note="",
     * )
     */
    public function testNormalize(): void
    {
        $result = Arr::normalize('hello');

        $json = <<<'eot'
[
    "hello"
]
eot;

        $this->assertSame(
            $json,
            $this->varJson(
                $result
            )
        );
    }

    /**
     * @api(
     *     title="格式化分隔字符串",
     *     description="",
     *     note="",
     * )
     */
    public function testNormalizeSplitString(): void
    {
        $result = Arr::normalize('hello,world');

        $json = <<<'eot'
[
    "hello",
    "world"
]
eot;

        $this->assertSame(
            $json,
            $this->varJson(
                $result
            )
        );
    }

    /**
     * @api(
     *     title="格式化数组",
     *     description="",
     *     note="",
     * )
     */
    public function testNormalizeArr(): void
    {
        $result = Arr::normalize(['hello', 'world']);

        $json = <<<'eot'
[
    "hello",
    "world"
]
eot;

        $this->assertSame(
            $json,
            $this->varJson(
                $result
            )
        );
    }

    /**
     * @api(
     *     title="格式化数组过滤空格",
     *     description="",
     *     note="",
     * )
     */
    public function testNormalizeArrFilterEmpty(): void
    {
        $result = Arr::normalize(['hello', 'world', ' ', '0']);

        $json = <<<'eot'
[
    "hello",
    "world"
]
eot;

        $this->assertSame(
            $json,
            $this->varJson(
                $result
            )
        );
    }

    /**
     * @api(
     *     title="格式化数组不过滤空格",
     *     description="",
     *     note="",
     * )
     */
    public function testNormalizeArrNotFilterEmpty(): void
    {
        $result = Arr::normalize(['hello', 'world', ' ', '0'], ',', true);

        $json = <<<'eot'
[
    "hello",
    "world",
    " "
]
eot;

        $this->assertSame(
            $json,
            $this->varJson(
                $result
            )
        );
    }

    /**
     * @api(
     *     title="格式化数据即不是数组也不是字符串",
     *     description="",
     *     note="",
     * )
     */
    public function testNormalizeNotArrAndNotString(): void
    {
        $result = Arr::normalize(false);

        $this->assertFalse($result);
    }

    /**
     * @api(
     *     title="允许特定 Key 通过",
     *     description="相当于白名单。",
     *     note="",
     * )
     */
    public function testOnly(): void
    {
        $result = Arr::only(['input' => 'test', 'foo' => 'bar', 'hello' => 'world'], ['input', 'hello', 'notfound']);

        $json = <<<'eot'
{
    "input": "test",
    "hello": "world"
}
eot;

        $this->assertSame(
            $json,
            $this->varJson(
                $result
            )
        );
    }

    /**
     * @api(
     *     title="排除特定 Key 通过",
     *     description="相当于黑名单。",
     *     note="",
     * )
     */
    public function testExcept(): void
    {
        $result = Arr::except(['input' => 'test', 'foo' => 'bar', 'hello' => 'world'], ['input', 'hello', 'notfound']);

        $json = <<<'eot'
{
    "foo": "bar"
}
eot;

        $this->assertSame(
            $json,
            $this->varJson(
                $result
            )
        );
    }

    /**
     * @api(
     *     title="数据过滤",
     *     description="基本的字符串会执行一次清理工作。",
     *     note="",
     * )
     */
    public function testFilter(): void
    {
        $sourceData = ['foo' => 'bar', 'hello' => 'world ', 'i' => '5'];
        $rule = [];

        $result = Arr::filter($sourceData, $rule);

        $json = <<<'eot'
{
    "foo": "bar",
    "hello": "world",
    "i": "5"
}
eot;

        $this->assertSame(
            $json,
            $this->varJson(
                $result
            )
        );
    }

    /**
     * @api(
     *     title="数据过滤待规则",
     *     description="",
     *     note="",
     * )
     */
    public function testFilterWithRule(): void
    {
        $sourceData = ['foo' => 'bar', 'hello' => 'world ', 'i' => '5'];
        $rule = [
            'i'   => ['intval'],
            'foo' => ['md5'],
            'bar' => [function ($v) {
                return $v.' php';
            }],
        ];

        $result = Arr::filter($sourceData, $rule);

        $json = <<<'eot'
{
    "foo": "37b51d194a7513e45b56f6524f2d51f2",
    "hello": "world",
    "i": 5
}
eot;

        $this->assertSame(
            $json,
            $this->varJson(
                $result
            )
        );
    }

    /**
     * @api(
     *     title="数据过滤待规则必须是数组",
     *     description="",
     *     note="",
     * )
     */
    public function testFilterRuleIsNotArr(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Rule of `i` must be an array.'
        );

        $sourceData = ['foo' => 'bar', 'hello' => 'world ', 'i' => '5'];
        $rule = [
            'i' => 'intval',
        ];

        Arr::filter($sourceData, $rule);
    }

    /**
     * @api(
     *     title="数据过滤待规则不是一个回调",
     *     description="",
     *     note="",
     * )
     */
    public function testFilterRuleItemIsNotACallback(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Rule item of `i` must be a callback type.'
        );

        $sourceData = ['foo' => 'bar', 'hello' => 'world ', 'i' => '5'];
        $rule = [
            'i' => ['notcallback'],
        ];

        Arr::filter($sourceData, $rule);
    }
}
