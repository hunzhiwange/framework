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
 */
class CollectionTest extends TestCase
{
    public function testBaseUse()
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

    public function testMake()
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

    public function testIterator()
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

    public function testCountable()
    {
        $data = [
            'hello', 'world', 'foo', 'bar',
        ];

        $collection = new Collection($data);

        $this->assertSame(4, count($collection));
    }

    public function testArrayAccess()
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

    public function testGetArrayElements()
    {
        $data = [
            'hello',
            'world',
        ];

        $collection = new Collection(new Collection($data));

        $this->assertSame($collection->toArray(), $data);
    }

    public function testGetArrayElements2()
    {
        $data = [
            'hello',
            'world',
        ];

        $collection = new Collection(new TestIArray());

        $this->assertSame($collection->toArray(), $data);
    }

    public function testGetArrayElements3()
    {
        $data = [
            'hello',
            'world',
        ];

        $collection = new Collection(new TestIJson());

        $this->assertSame($collection->toArray(), $data);
    }

    public function testGetArrayElements4()
    {
        $data = [
            'hello',
            'world',
        ];

        $collection = new Collection(new TestJsonSerializable());

        $this->assertSame($collection->toArray(), $data);
    }

    public function testGetArrayElements5()
    {
        $data = [
            'hello',
        ];

        $collection = new Collection('hello');

        $this->assertSame($collection->toArray(), $data);
    }

    public function testGetArrayElementsWithStdClass()
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

    public function testTypeValidate()
    {
        $data = [
            'hello',
            'world',
        ];

        $collection = new Collection($data, ['string']);

        $this->assertSame($collection->toArray(), $data);
    }

    public function testTypeValidateException()
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

    public function testEach()
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

    public function testEachAndBreak()
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

    public function testToJson()
    {
        $data = [
            'hello',
            'world',
        ];

        $collection = new Collection($data);

        $data = '["hello","world"]';

        $this->assertSame($data, $collection->toJson());
    }

    public function testToJsonWithCn()
    {
        $data = [
            '我',
            '成都',
        ];

        $collection = new Collection($data);

        $data = '["我","成都"]';

        $this->assertSame($data, $collection->toJson());
    }

    public function testToJsonWithCnEncode()
    {
        $data = [
            '我',
            '成都',
        ];

        $collection = new Collection($data);

        $data = '["\u6211","\u6210\u90fd"]';

        $this->assertSame($data, $collection->toJson(JSON_HEX_TAG));
    }

    public function testJsonSerialize()
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

    public function testGetSetString()
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

    public function testGetSetString2()
    {
        $data = [
            '我' => '成都',
            '们' => '中国',
        ];

        $collection = new Collection($data);
        $this->assertSame((string) $collection, '{"我":"成都","们":"中国"}');
    }

    public function testValid()
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
    public function toArray()
    {
        return [
            'hello',
            'world',
        ];
    }
}

class TestIJson implements IJson
{
    public function toJson($option = null)
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
