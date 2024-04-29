<?php

declare(strict_types=1);

namespace Tests\Support;

use Leevel\Kernel\Utils\Api;
use Leevel\Support\Collection;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => '集合 collection',
    'path' => 'component/collection',
    'zh-CN:description' => <<<'EOT'
集合 `collection` 提供了一些实用方法，数据库查询的数据列表也会转换为集合数据类型。
EOT,
])]
final class CollectionTest extends TestCase
{
    #[Api([
        'zh-CN:title' => '基本使用',
        'zh-CN:description' => <<<'EOT'
集合实现了 `\IteratorAggregate` 可以像普通数组一样遍历，也实现了 `\ArrayAccess` 接口，可以当做普通数组一样使用。
EOT,
    ])]
    public function testBaseUse(): void
    {
        $data = [
            'hello', 'world', 'foo', 'bar',
        ];

        $collection = new Collection($data);

        foreach ($collection as $key => $val) {
            switch ($key) {
                case 0:
                    self::assertSame($val, 'hello');

                    break;

                case 1:
                    self::assertSame($val, 'world');

                    break;

                case 2:
                    self::assertSame($val, 'foo');

                    break;

                case 3:
                    self::assertSame($val, 'bar');

                    break;
            }
        }

        self::assertSame($collection[0], 'hello');
        self::assertSame($collection[1], 'world');
        self::assertSame($collection[2], 'foo');
        self::assertSame($collection[3], 'bar');
        self::assertTrue(isset($collection[0]));
        self::assertTrue(isset($collection[1]));
        self::assertTrue(isset($collection[2]));
        self::assertTrue(isset($collection[3]));
        self::assertFalse(isset($collection[4]));
    }

    #[Api([
        'zh-CN:title' => '批量添加元素',
    ])]
    public function testBatchSet(): void
    {
        $data = [
            'hello' => 'world',
        ];

        $collection = new Collection($data);
        $collection->batchSet([
            'hello2' => 'world2',
            'hello3' => 'world3',
        ]);

        self::assertSame($collection['hello'], 'world');
        self::assertSame($collection['hello2'], 'world2');
        self::assertSame($collection['hello3'], 'world3');
    }

    #[Api([
        'zh-CN:title' => '静态方法 make 创建集合',
        'zh-CN:description' => <<<'EOT'
可以使用 `make` 方法创建一个集合对象。
EOT,
    ])]
    public function testMake(): void
    {
        $data = [
            'hello', 'world', 'foo', 'bar',
        ];

        $collection = Collection::make($data);

        foreach ($collection as $key => $val) {
            switch ($key) {
                case 0:
                    self::assertSame($val, 'hello');

                    break;

                case 1:
                    self::assertSame($val, 'world');

                    break;

                case 2:
                    self::assertSame($val, 'foo');

                    break;

                case 3:
                    self::assertSame($val, 'bar');

                    break;
            }
        }

        self::assertSame($collection[0], 'hello');
        self::assertSame($collection[1], 'world');
        self::assertSame($collection[2], 'foo');
        self::assertSame($collection[3], 'bar');
        self::assertTrue(isset($collection[0]));
        self::assertTrue(isset($collection[1]));
        self::assertTrue(isset($collection[2]));
        self::assertTrue(isset($collection[3]));
        self::assertFalse(isset($collection[4]));
    }

    #[Api([
        'zh-CN:title' => '集合支持迭代器',
        'zh-CN:description' => <<<'EOT'
集合 `collection` 是一个标准的迭代器，支持迭代器的用法。
EOT,
    ])]
    public function testIterator(): void
    {
        $data = [
            'hello', 'world', 'foo', 'bar',
        ];

        $collection = new Collection($data);

        self::assertSame('hello', $collection->current());
        self::assertSame(0, $collection->key());

        self::assertNull($collection->next());

        self::assertSame('world', $collection->current());
        self::assertSame(1, $collection->key());

        self::assertNull($collection->next());
        self::assertNull($collection->next());

        self::assertSame('bar', $collection->current());
        self::assertSame(3, $collection->key());

        $collection->next();

        self::assertFalse($collection->current());
        self::assertNull($collection->key());

        $collection->rewind();
        self::assertSame(0, $collection->key());
        self::assertSame('hello', $collection->current());
    }

    #[Api([
        'zh-CN:title' => '集合可统计',
        'zh-CN:description' => <<<'EOT'
集合实现了 `\Countable` 可以像普通数组一样统计元素的个数。
EOT,
    ])]
    public function testCountable(): void
    {
        $data = [
            'hello', 'world', 'foo', 'bar',
        ];

        $collection = new Collection($data);

        self::assertCount(4, $collection);
    }

    public function testArrayAccess(): void
    {
        $data = [
            'hello',
        ];

        $collection = new Collection($data);

        self::assertSame($collection->toArray(), $data);

        $collection[1] = 'world';
        $data[1] = 'world';
        self::assertSame($collection->toArray(), $data);

        $collection[2] = 'foo';
        $data[2] = 'foo';
        self::assertSame($collection->toArray(), $data);

        unset($collection[1]);

        self::assertSame($collection->toArray(), [
            0 => 'hello',
            2 => 'foo',
        ]);
    }

    public function test1(): void
    {
        $data = [
            'hello' => 'world',
        ];

        $collection = new Collection($data);

        self::assertSame($collection->toArray(), $data);

        $collection->hello = 'world new';
        $data['hello'] = 'world new';
        self::assertSame($collection->toArray(), $data);

        $collection->foo = 'foo';
        $data['foo'] = 'foo';
        self::assertSame($collection->toArray(), $data);

        $collection->foo = null;

        self::assertSame($collection->toArray(), [
            'hello' => 'world new',
            'foo' => null,
        ]);

        $collection->fooNotFound = null;
    }

    public function testGetArrayElements(): void
    {
        $data = [
            'hello',
            'world',
        ];

        $collection = new Collection(new Collection($data));

        self::assertSame($collection->toArray(), $data);
    }

    #[Api([
        'zh-CN:title' => '集合数据支持实现 \Leevel\Support\IArray 的对象',
        'zh-CN:description' => <<<'EOT'
对象实现了 `\Leevel\Support\IArray` 可以转化为集合数据。

**例子**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Support\TestArray::class)]}
```

> 实现了 `\Leevel\Support\IArray` 的对象的方法 `toArray` 返回集合的数据。
EOT,
    ])]
    public function testGetArrayElements2(): void
    {
        $data = [
            'hello',
            'world',
        ];

        $collection = new Collection(new TestArray());

        self::assertSame($collection->toArray(), $data);
    }

    #[Api([
        'zh-CN:title' => '集合数据支持实现 \Leevel\Support\IJson 的对象',
        'zh-CN:description' => <<<'EOT'
对象实现了 `\Leevel\Support\IJson` 可以转化为集合数据。

**例子**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Support\TestJson::class)]}
```

> 实现了 `\Leevel\Support\IJson` 的对象的方法 `toJson` 返回集合的数据。
EOT,
    ])]
    public function testGetArrayElements3(): void
    {
        $data = [
            'hello',
            'world',
        ];

        $collection = new Collection(new TestJson());

        self::assertSame($collection->toArray(), $data);
    }

    #[Api([
        'zh-CN:title' => '集合数据支持实现 \JsonSerializable 的对象',
        'zh-CN:description' => <<<'EOT'
对象实现了 `\JsonSerializable` 可以转化为集合数据。

**例子**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Support\TestJsonSerializable::class)]}
```

> 实现了 `\JsonSerializable` 的对象的方法 `jsonSerialize` 返回集合的数据。
EOT,
    ])]
    public function testGetArrayElements4(): void
    {
        $data = [
            'hello',
            'world',
        ];

        $collection = new Collection(new TestJsonSerializable());

        self::assertSame($collection->toArray(), $data);
    }

    #[Api([
        'zh-CN:title' => '集合数据支持普通数据转化为数组',
    ])]
    public function testGetArrayElements5(): void
    {
        $data = [
            'hello',
        ];

        $collection = new Collection('hello');

        self::assertSame($collection->toArray(), $data);
    }

    #[Api([
        'zh-CN:title' => '集合数据支持 \stdClass 的对象',
        'zh-CN:description' => <<<'EOT'
对象为 `\stdClass` 可以转化为集合数据。

> `\stdClass` 的对象返回转化为数组作为集合的数据。
EOT,
    ])]
    public function testGetArrayElementsWithStdClass(): void
    {
        $data = [
            'hello' => 'world',
            'foo' => 'bar',
        ];

        $std = new \stdClass();
        $std->hello = 'world';
        $std->foo = 'bar';

        $collection = new Collection($std);

        self::assertSame($collection->toArray(), $data);
    }

    #[Api([
        'zh-CN:title' => 'getValueTypes 集合数据支持值类型验证',
        'zh-CN:description' => <<<'EOT'
比如下面的数据类型为 `string`，只有字符串类型才能加入集合。
EOT,
    ])]
    public function testGetValueTypesValidate(): void
    {
        $data = [
            'hello',
            'world',
        ];

        $collection = new Collection($data, ['string']);
        self::assertSame($collection->toArray(), $data);
        self::assertSame(['string'], $collection->getValueTypes());
    }

    #[Api([
        'zh-CN:title' => 'getKeyTypes 集合数据支持键类型验证',
    ])]
    public function testGetKeyTypesValidate(): void
    {
        $data = [
            'hello' => 'world',
            'world' => 'hello',
        ];

        $collection = new Collection($data, ['string'], ['string']);
        self::assertSame($collection->toArray(), $data);
        self::assertSame(['string'], $collection->getKeyTypes());
    }

    #[Api([
        'zh-CN:title' => '集合数据支持类型验证不符合规则示例',
        'zh-CN:description' => <<<'EOT'
比如下面的数据类型为 `int`，字符串类型就会抛出异常。
EOT,
    ])]
    public function testTypeValidateException(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage(
            'The value of a collection value type requires the following types `int`.'
        );

        $data = [
            'hello',
            'world',
        ];

        new Collection($data, ['int']);
    }

    #[Api([
        'zh-CN:title' => 'each 集合数据遍历元素项',
        'zh-CN:description' => <<<'EOT'
使用闭包进行遍历，闭包的第一个参数为元素值，第二为元素键。
EOT,
    ])]
    public function testEach(): void
    {
        $data = [
            'hello',
            'world',
        ];

        $collection = new Collection($data);

        $i = 0;
        $collection->each(function ($item, $key) use (&$i): void {
            $this->assertSame($i, $key);

            if (0 === $i) {
                $this->assertSame($item, 'hello');
            } else {
                $this->assertSame($item, 'world');
            }

            ++$i;
        });
    }

    #[Api([
        'zh-CN:title' => 'each 集合数据遍历元素项支持中断',
        'zh-CN:description' => <<<'EOT'
遍历元素项的时候返回 `false` 将会中断后续遍历操作。
EOT,
    ])]
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

            ++$i;
        });

        self::assertSame($i, 0);
    }

    #[Api([
        'zh-CN:title' => 'toJson 集合数据支持 JSON 输出',
        'zh-CN:description' => <<<'EOT'
集合实现了 `\Leevel\Support\IJson` 接口，可以通过方法 `toJson` 输出 JSON 字符串。
EOT,
    ])]
    public function testToJson(): void
    {
        $data = [
            'hello',
            'world',
        ];

        $collection = new Collection($data);

        $data = '["hello","world"]';

        self::assertSame($data, $collection->toJson());
    }

    #[Api([
        'zh-CN:title' => 'toJson 集合数据支持 JSON 输出默认不要编码 Unicode',
        'zh-CN:description' => <<<'EOT'
JSON_UNESCAPED_UNICODE 可以让有中文的 JSON 字符串更加友好地输出。

``` php
json_encode('中文', JSON_UNESCAPED_UNICODE);
```
EOT,
    ])]
    public function testToJsonWithCn(): void
    {
        $data = [
            '我',
            '成都',
        ];

        $collection = new Collection($data);

        $data = '["我","成都"]';

        self::assertSame($data, $collection->toJson());
    }

    public function testToJsonWithCnEncode(): void
    {
        $data = [
            '我',
            '成都',
        ];

        $collection = new Collection($data);

        $data = '["\u6211","\u6210\u90fd"]';

        self::assertSame($data, $collection->toJson(JSON_HEX_TAG));
    }

    #[Api([
        'zh-CN:title' => 'toJson 集合数据支持 JSON 输出',
        'zh-CN:description' => <<<'EOT'
集合实现了 `\JsonSerializable` 接口，可以通过方法 `toJson` 输出 JSON 字符串。
EOT,
    ])]
    public function testJsonSerialize(): void
    {
        $std = new \stdClass();
        $std->hello = 'world';
        $std->foo = 'bar';

        $data = [
            new TestJsonSerializable(),
            new TestArray(),
            new TestJson(),
            $std,
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
                {
                    "hello": "world",
                    "foo": "bar"
                },
                "foo",
                "bar"
            ]
            eot;

        self::assertSame(
            $data,
            $this->varJson(
                $collection->jsonSerialize()
            )
        );
    }

    #[Api([
        'zh-CN:title' => '__toString 集合数据可以转化为字符串',
        'zh-CN:description' => <<<'EOT'
集合实现了 `__toString` 方法，可以强制转化为字符串。
EOT,
    ])]
    public function testGetSetString(): void
    {
        $data = [
            'hello' => 'world',
            'foo' => 'bar',
        ];

        $collection = new Collection($data);

        self::assertSame($collection->hello, 'world');
        self::assertSame($collection->foo, 'bar');
        $collection->hello = 'new world';
        $collection->foo = 'new bar';
        self::assertSame($collection->hello, 'new world');
        self::assertSame($collection->foo, 'new bar');
        self::assertSame((string) $collection, '{"hello":"new world","foo":"new bar"}');
    }

    public function testGetSetString2(): void
    {
        $data = [
            '我' => '成都',
            '们' => '中国',
        ];

        $collection = new Collection($data);
        self::assertSame((string) $collection, '{"我":"成都","们":"中国"}');
    }

    public function testValid(): void
    {
        $data = [
            'hello', 'world',
        ];

        $collection = new Collection($data);

        self::assertTrue($collection->valid());
        $collection->next();
        self::assertTrue($collection->valid());
        $collection->next();
        self::assertFalse($collection->valid());
        $collection->rewind();
        self::assertTrue($collection->valid());
    }

    #[Api([
        'zh-CN:title' => '__get,__set,__isset,__unset 魔术方法支持',
    ])]
    public function testMagicMethod(): void
    {
        $data = [
            'hello' => 'world',
            'foo' => 'bar',
        ];

        $collection = new Collection($data);

        self::assertSame($collection->hello, 'world');
        self::assertSame($collection->foo, 'bar');
        $collection->hello = 'new world';
        $collection->foo = 'new bar';
        self::assertSame($collection->hello, 'new world');
        self::assertSame($collection->foo, 'new bar');
        self::assertTrue(isset($collection->hello));
        self::assertFalse(isset($collection->hello2));
        $collection->hello = null;
        self::assertFalse(isset($collection->hello));
    }

    #[Api([
        'zh-CN:title' => 'isEmpty 是否为空集合',
    ])]
    public function testIsEmpty(): void
    {
        $data = [
            'hello',
            'world',
        ];

        $collection = new Collection($data);
        self::assertFalse($collection->isEmpty());

        $data = [];
        $collection = new Collection($data);
        self::assertTrue($collection->isEmpty());
    }

    public function test2(): void
    {
        $data = [
            'hello',
            'world',
        ];
        $collection = new Collection($data, ['string'], ['int']);
        $result = $this->invokeTestMethod($collection, 'checkType', ['hello', true, 'not_found']);
        self::assertNull($result);
    }

    public function test3(): void
    {
        $data = [
            'hello',
            'world',
        ];
        $collection = new Collection($data, ['string'], ['int']);
        $result = $this->invokeTestMethod($collection, 'checkType', ['hello', true, 1]);
        self::assertNull($result);
    }
}
