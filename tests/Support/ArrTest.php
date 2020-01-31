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

use Exception;
use JsonSerializable;
use Leevel\Support\Arr;
use Leevel\Support\IArray;
use Leevel\Support\IJson;
use Tests\TestCase;

/**
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
     *     title="normalize 基础格式化",
     *     description="",
     *     note="",
     * )
     */
    public function testBaseUse(): void
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
     *     title="normalize 格式化字符串",
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
     *     title="normalize 格式化分隔字符串",
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
     *     title="normalize 格式化数组",
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
     *     title="normalize 格式化数组过滤空格",
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
     *     title="normalize 格式化数组不过滤空格",
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
     *     title="normalize 格式化数据即不是数组也不是字符串",
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
     *     title="only 允许特定 Key 通过",
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
                "hello": "world",
                "notfound": null
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
     *     title="except 排除特定 Key 通过",
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
     *     title="filter 数据过滤",
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
     *     title="filter 数据过滤待规则",
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
     *     title="filter 数据过滤待规则必须是数组",
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
     *     title="filter 数据过滤待规则不是一个回调",
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

    /**
     * @api(
     *     title="filter 数据过滤默认不处理 NULL 值",
     *     description="",
     *     note="",
     * )
     */
    public function testFilterWithoutMust(): void
    {
        $sourceData = ['foo' => null];
        $rule = ['foo' => ['intval']];

        $result = Arr::filter($sourceData, $rule);

        $json = <<<'eot'
            {
                "foo": null
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
     *     title="filter 数据过滤强制处理 NULL 值",
     *     description="",
     *     note="",
     * )
     */
    public function testFilterWithMust(): void
    {
        $sourceData = ['foo' => null];
        $rule = ['foo' => ['intval', 'must']];

        $result = Arr::filter($sourceData, $rule);

        $json = <<<'eot'
            {
                "foo": 0
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
     *     title="shouldJson 数据过滤强制处理 NULL 值",
     *     description="
     * 测试实现了 `\Leevel\Support\IArray` 的对象
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Http\ArrMyArray::class)]}
     * ```
     *
     * 测试实现了 `\Leevel\Support\IJson` 的对象
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Http\ArrMyJson::class)]}
     * ```
     *
     * 测试实现了 `\JsonSerializable` 的对象
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Http\ArrMyJsonSerializable::class)]}
     * ```
     * ",
     *     note="",
     * )
     */
    public function testShouldJson(): void
    {
        $this->assertTrue(Arr::shouldJson(['foo' => 'bar']));
        $this->assertTrue(Arr::shouldJson(new ArrMyArray()));
        $this->assertTrue(Arr::shouldJson(new ArrMyJson()));
        $this->assertTrue(Arr::shouldJson(new ArrMyJsonSerializable()));
    }

    /**
     * @api(
     *     title="convertJson 转换 JSON 数据",
     *     description="",
     *     note="",
     * )
     */
    public function testConvertJson(): void
    {
        $this->assertSame('{"foo":"bar"}', Arr::convertJson(['foo' => 'bar']));
        $this->assertSame('{"foo":"bar"}', Arr::convertJson(['foo' => 'bar'], JSON_THROW_ON_ERROR));
        $this->assertSame('{"hello":"IArray"}', Arr::convertJson(new ArrMyArray()));
        $this->assertSame('{"hello":"IJson"}', Arr::convertJson(new ArrMyJson()));
        $this->assertSame('{"hello":"JsonSerializable"}', Arr::convertJson(new ArrMyJsonSerializable()));
        $this->assertSame('{"成":"都"}', Arr::convertJson(['成' => '都']));
        $this->assertSame('{"\u6210":"\u90fd"}', Arr::convertJson(['成' => '都'], 0));
    }

    public function testConvertJsonWithInvalidUtf8Characters(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Malformed UTF-8 characters, possibly incorrectly encoded'
        );

        Arr::convertJson("\xB1\x31");
    }

    public function testConvertJsonWithInvalidUtf8CharactersAndThrowJsonException(): void
    {
        $this->expectException(\JsonException::class);
        $this->expectExceptionMessage(
            'Malformed UTF-8 characters, possibly incorrectly encoded'
        );

        Arr::convertJson("\xB1\x31", JSON_THROW_ON_ERROR);
    }

    public function testConvertJsonJsonSerializeThrowException(): void
    {
        $this->expectException(ArrMyException::class);
        $this->expectExceptionMessage(
            'json exception'
        );

        Arr::convertJson(new ArrMyJsonSerializableWithException(), JSON_THROW_ON_ERROR);
    }
}

class ArrMyArray implements IArray
{
    public function toArray(): array
    {
        return ['hello' => 'IArray'];
    }
}

class ArrMyJson implements IJson
{
    public function toJson(?int $option = null): string
    {
        if (null === $option) {
            $option = JSON_UNESCAPED_UNICODE;
        }

        return json_encode(['hello' => 'IJson'], $option);
    }
}

class ArrMyJsonSerializable implements JsonSerializable
{
    public function jsonSerialize()
    {
        return ['hello' => 'JsonSerializable'];
    }
}

class ArrMyException extends Exception
{
}

class ArrMyJsonSerializableWithException implements JsonSerializable
{
    public function jsonSerialize()
    {
        throw new ArrMyException('json exception');
    }
}
