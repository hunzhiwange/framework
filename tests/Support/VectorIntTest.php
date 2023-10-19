<?php

declare(strict_types=1);

namespace Tests\Support;

use Leevel\Support\VectorInt;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => 'VectorInt 动态数组',
    'path' => 'component/collection/vectorint',
])]
final class VectorIntTest extends TestCase
{
    #[Api([
        'zh-CN:title' => '基本使用',
    ])]
    public function testBaseUse(): void
    {
        $data = [
            1, 2, 3, 4,
        ];

        $collection = new VectorInt($data);
        static::assertSame($collection[0], 1);
        static::assertSame($collection[1], 2);
        static::assertSame($collection[2], 3);
        static::assertSame($collection[3], 4);
        static::assertTrue(isset($collection[0]));
        static::assertTrue(isset($collection[1]));
        static::assertTrue(isset($collection[2]));
        static::assertTrue(isset($collection[3]));
    }

    #[Api([
        'zh-CN:title' => '从 HTTP 请求创建整数索引数组',
    ])]
    public function testFromRequest(): void
    {
        $collection = VectorInt::fromRequest([
            1, 2, 3, 4,
        ]);
        static::assertSame($collection[0], 1);
        static::assertSame($collection[1], 2);
        static::assertSame($collection[2], 3);
        static::assertSame($collection[3], 4);
        static::assertTrue(isset($collection[0]));
        static::assertTrue(isset($collection[1]));
        static::assertTrue(isset($collection[2]));
        static::assertTrue(isset($collection[3]));

        $collection = VectorInt::fromRequest('1,2,3,4');
        static::assertSame($collection[0], 1);
        static::assertSame($collection[1], 2);
        static::assertSame($collection[2], 3);
        static::assertSame($collection[3], 4);
        static::assertTrue(isset($collection[0]));
        static::assertTrue(isset($collection[1]));
        static::assertTrue(isset($collection[2]));
        static::assertTrue(isset($collection[3]));
    }

    public function testError(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('The value of a collection value type requires the following types `int`.');

        $data = [
            1, 'string', 3, 4,
        ];

        new VectorInt($data);
    }

    public function testError2(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('The value of a collection key type requires the following types `int`.');

        $data = [
            'hello' => 'world',
        ];

        new VectorInt($data);
    }
}
