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

namespace Tests\Collection;

use JsonSerializable;
use Leevel\Collection\Collection;
use Leevel\Support\IArray;
use Leevel\Support\IJson;
use stdClass;
use Tests\TestCase;

/**
 * collection test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.03
 *
 * @version 1.0
 *
 * @api(
 *     title="集合 collection",
 *     path="component/collection",
 *     description="集合 `collection` 提供了一些实用方法，数据库查询的数据列表也会转换为集合数据类型。",
 * )
 */
class CollectionTest extends TestCase
{
    /**
     * @api(
     *     title="基本使用",
     *     description="集合实现了 `\IteratorAggregate` 可以像普通数组一样遍历，也实现了 `\ArrayAccess` 接口，可以当做普通数组一样使用。",
     *     note="",
     * )
     */
    public function testBaseUse(): void
    {
        $data = [
            'hello', 'world', 'foo', 'bar',
        ];

        $collection = new Collection($data);

        foreach ($collection as $key => $val) {
            switch ($key) {
                case 0:
                    $this->assertSame($val, 'hello');

                    break;
                case 1:
                    $this->assertSame($val, 'world');

                    break;
                case 2:
                    $this->assertSame($val, 'foo');

                    break;
                case 3:
                    $this->assertSame($val, 'bar');

                    break;
            }
        }

        $this->assertSame($collection[0], 'hello');
        $this->assertSame($collection[1], 'world');
        $this->assertSame($collection[2], 'foo');
        $this->assertSame($collection[3], 'bar');
        $this->assertTrue(isset($collection[0]));
        $this->assertTrue(isset($collection[1]));
        $this->assertTrue(isset($collection[2]));
        $this->assertTrue(isset($collection[3]));
        $this->assertFalse(isset($collection[4]));
    }

    /**
     * @api(
     *     title="静态方法 make 创建集合",
     *     description="可以使用 `make` 方法创建一个集合对象。",
     *     note="",
     * )
     */
    public function testMake(): void
    {
        $data = [
            'hello', 'world', 'foo', 'bar',
        ];

        $collection = Collection::make($data);

        foreach ($collection as $key => $val) {
            switch ($key) {
                case 0:
                    $this->assertSame($val, 'hello');

                    break;
                case 1:
                    $this->assertSame($val, 'world');

                    break;
                case 2:
                    $this->assertSame($val, 'foo');

                    break;
                case 3:
                    $this->assertSame($val, 'bar');

                    break;
            }
        }

        $this->assertSame($collection[0], 'hello');
        $this->assertSame($collection[1], 'world');
        $this->assertSame($collection[2], 'foo');
        $this->assertSame($collection[3], 'bar');
        $this->assertTrue(isset($collection[0]));
        $this->assertTrue(isset($collection[1]));
        $this->assertTrue(isset($collection[2]));
        $this->assertTrue(isset($collection[3]));
        $this->assertFalse(isset($collection[4]));
    }

    /**
     * @api(
     *     title="集合支持迭代器",
     *     description="集合 `collection` 是一个标准的迭代器，支持迭代器的用法。",
     *     note="",
     * )
     */
    public function testIterator(): void
    {
        $data = [
            'hello', 'world', 'foo', 'bar',
        ];

        $collection = new Collection($data);

        $this->assertSame('hello', $collection->current());
        $this->assertSame(0, $collection->key());

        $this->assertNull($collection->next());

        $this->assertSame('world', $collection->current());
        $this->assertSame(1, $collection->key());

        $this->assertNull($collection->next());
        $this->assertNull($collection->next());

        $this->assertSame('bar', $collection->current());
        $this->assertSame(3, $collection->key());

        $collection->next();

        $this->assertFalse($collection->current());
        $this->assertNull($collection->key());

        $collection->rewind();
        $this->assertSame(0, $collection->key());
        $this->assertSame('hello', $collection->current());
    }

    /**
     * @api(
     *     title="集合可统计",
     *     description="集合实现了 `\Countable` 可以像普通数组一样统计元素的个数。",
     *     note="",
     * )
     */
    public function testCountable(): void
    {
        $data = [
            'hello', 'world', 'foo', 'bar',
        ];

        $collection = new Collection($data);

        $this->assertCount(4, $collection);
    }

    public function testArrayAccess(): void
    {
        $data = [
            'hello',
        ];

        $collection = new Collection($data);

        $this->assertSame($collection->toArray(), $data);

        $collection[1] = 'world';
        $data[1] = 'world';
        $this->assertSame($collection->toArray(), $data);

        $collection[2] = 'foo';
        $data[2] = 'foo';
        $this->assertSame($collection->toArray(), $data);

        unset($collection[1]);

        $this->assertSame($collection->toArray(), [
            0 => 'hello',
            2 => 'foo',
        ]);
    }

    public function testGetArrayElements(): void
    {
        $data = [
            'hello',
            'world',
        ];

        $collection = new Collection(new Collection($data));

        $this->assertSame($collection->toArray(), $data);
    }

    /**
     * @api(
     *     title="集合数据支持实现 \Leevel\Support\IArray 的对象",
     *     description="对象实现了 `\Leevel\Support\IArray` 可以转化为集合数据。
     *
     * **例子**
     *
     * ``` php
     * ".\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Collection\TestIArray::class)."
     * ```
     *
     * > 实现了 `\Leevel\Support\IArray` 的对象的方法 `toArray` 返回集合的数据。
     * ",
     *     note="",
     * )
     */
    public function testGetArrayElements2(): void
    {
        $data = [
            'hello',
            'world',
        ];

        $collection = new Collection(new TestIArray());

        $this->assertSame($collection->toArray(), $data);
    }

    /**
     * @api(
     *     title="集合数据支持实现 \Leevel\Support\IJson 的对象",
     *     description="对象实现了 `\Leevel\Support\IJson` 可以转化为集合数据。
     *
     * **例子**
     *
     * ``` php
     * ".\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Collection\TestIJson::class)."
     * ```
     *
     * > 实现了 `\Leevel\Support\IJson` 的对象的方法 `toJson` 返回集合的数据。
     * ",
     *     note="",
     * )
     */
    public function testGetArrayElements3(): void
    {
        $data = [
            'hello',
            'world',
        ];

        $collection = new Collection(new TestIJson());

        $this->assertSame($collection->toArray(), $data);
    }

    /**
     * @api(
     *     title="集合数据支持实现 \JsonSerializable 的对象",
     *     description="对象实现了 `\JsonSerializable` 可以转化为集合数据。
     *
     * **例子**
     *
     * ``` php
     * ".\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Collection\TestJsonSerializable::class)."
     * ```
     *
     * > 实现了 `\JsonSerializable` 的对象的方法 `jsonSerialize` 返回集合的数据。
     * ",
     *     note="",
     * )
     */
    public function testGetArrayElements4(): void
    {
        $data = [
            'hello',
            'world',
        ];

        $collection = new Collection(new TestJsonSerializable());

        $this->assertSame($collection->toArray(), $data);
    }

    public function testGetArrayElements5(): void
    {
        $data = [
            'hello',
        ];

        $collection = new Collection('hello');

        $this->assertSame($collection->toArray(), $data);
    }

    public function testGetArrayElementsWithStdClass(): void
    {
        $data = [
            'hello' => 'world',
            'foo'   => 'bar',
        ];

        $std = new stdClass();
        $std->hello = 'world';
        $std->foo = 'bar';

        $collection = new Collection($std);

        $this->assertSame($collection->toArray(), $data);
    }

    public function testTypeValidate(): void
    {
        $data = [
            'hello',
            'world',
        ];

        $collection = new Collection($data, ['string']);

        $this->assertSame($collection->toArray(), $data);
    }

    public function testTypeValidateException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Collection type int validation failed.'
        );

        $data = [
            'hello',
            'world',
        ];

        $collection = new Collection($data, ['int']);
    }

    public function testEach(): void
    {
        $data = [
            'hello',
            'world',
        ];

        $collection = new Collection($data);

        $i = 0;

        $collection->each(function ($item, $key) use (&$i) {
            $this->assertSame($i, $key);

            if (0 === $i) {
                $this->assertSame($item, 'hello');
            } else {
                $this->assertSame($item, 'world');
            }

            $i++;
        });
    }

    public function testEachAndBreak(): void
    {
        $data = [
            'hello',
            'world',
        ];

        $collection = new Collection($data);

        $i = 0;

        $collection->each(function ($item, $key) use (&$i) {
            $this->assertSame($i, $key);

            if (0 === $i) {
                $this->assertSame($item, 'hello');

                return false;
            }

            $i++;
        });

        $this->assertSame($i, 0);
    }

    public function testToJson(): void
    {
        $data = [
            'hello',
            'world',
        ];

        $collection = new Collection($data);

        $data = '["hello","world"]';

        $this->assertSame($data, $collection->toJson());
    }

    public function testToJsonWithCn(): void
    {
        $data = [
            '我',
            '成都',
        ];

        $collection = new Collection($data);

        $data = '["我","成都"]';

        $this->assertSame($data, $collection->toJson());
    }

    public function testToJsonWithCnEncode(): void
    {
        $data = [
            '我',
            '成都',
        ];

        $collection = new Collection($data);

        $data = '["\u6211","\u6210\u90fd"]';

        $this->assertSame($data, $collection->toJson(JSON_HEX_TAG));
    }

    public function testJsonSerialize(): void
    {
        $data = [
            new TestJsonSerializable(),
            new TestIArray(),
            new TestIJson(),
            'foo',
            'bar',
        ];

        $collection = new Collection($data);

        $data = <<<'eot'
            [
                [
                    "hello",
                    "world"
                ],
                [
                    "hello",
                    "world"
                ],
                [
                    "hello",
                    "world"
                ],
                "foo",
                "bar"
            ]
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $collection->jsonSerialize()
            )
        );
    }

    public function testGetSetString(): void
    {
        $data = [
            'hello' => 'world',
            'foo'   => 'bar',
        ];

        $collection = new Collection($data);

        $this->assertSame($collection->hello, 'world');
        $this->assertSame($collection->foo, 'bar');
        $collection->hello = 'new world';
        $collection->foo = 'new bar';
        $this->assertSame($collection->hello, 'new world');
        $this->assertSame($collection->foo, 'new bar');
        $this->assertSame((string) $collection, '{"hello":"new world","foo":"new bar"}');
    }

    public function testGetSetString2(): void
    {
        $data = [
            '我' => '成都',
            '们' => '中国',
        ];

        $collection = new Collection($data);
        $this->assertSame((string) $collection, '{"我":"成都","们":"中国"}');
    }

    public function testValid(): void
    {
        $data = [
            'hello', 'world',
        ];

        $collection = new Collection($data);

        $this->assertTrue($collection->valid());
        $collection->next();
        $this->assertTrue($collection->valid());
        $collection->next();
        $this->assertFalse($collection->valid());
        $collection->rewind();
        $this->assertTrue($collection->valid());
    }
}

class TestIArray implements IArray
{
    public function toArray(): array
    {
        return [
            'hello',
            'world',
        ];
    }
}

class TestIJson implements IJson
{
    public function toJson($option = null): string
    {
        if (null === $option) {
            $option = JSON_UNESCAPED_UNICODE;
        }

        return json_encode([
            'hello',
            'world',
        ], $option);
    }
}

class TestJsonSerializable implements JsonSerializable
{
    public function jsonSerialize()
    {
        return [
            'hello',
            'world',
        ];
    }
}
