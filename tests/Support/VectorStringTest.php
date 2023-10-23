<?php

declare(strict_types=1);

namespace Tests\Support;

use Leevel\Kernel\Utils\Api;
use Leevel\Support\VectorString;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => 'VectorString 动态数组',
    'path' => 'component/collection/vectorstring',
])]
final class VectorStringTest extends TestCase
{
    #[Api([
        'zh-CN:title' => '基本使用',
    ])]
    public function testBaseUse(): void
    {
        $data = [
            'h', 'l', 'w', 'd',
        ];

        $collection = new VectorString($data);
        static::assertSame($collection[0], 'h');
        static::assertSame($collection[1], 'l');
        static::assertSame($collection[2], 'w');
        static::assertSame($collection[3], 'd');
        static::assertTrue(isset($collection[0]));
        static::assertTrue(isset($collection[1]));
        static::assertTrue(isset($collection[2]));
        static::assertTrue(isset($collection[3]));
    }

    #[Api([
        'zh-CN:title' => 'VectorString 支持 fromRequest 数组',
    ])]
    public function test1(): void
    {
        $data = [
            'h', 1, 'w', 'd',
        ];

        $collection = VectorString::fromRequest($data);
        static::assertSame($collection[0], 'h');
        static::assertSame($collection[1], '1');
        static::assertSame($collection[2], 'w');
        static::assertSame($collection[3], 'd');
        static::assertTrue(isset($collection[0]));
        static::assertTrue(isset($collection[1]));
        static::assertTrue(isset($collection[2]));
        static::assertTrue(isset($collection[3]));
    }

    #[Api([
        'zh-CN:title' => 'VectorString 支持 fromRequest 字符串',
    ])]
    public function test2(): void
    {
        $data = 'h,1,w,d';

        $collection = VectorString::fromRequest($data);
        static::assertSame($collection[0], 'h');
        static::assertSame($collection[1], '1');
        static::assertSame($collection[2], 'w');
        static::assertSame($collection[3], 'd');
        static::assertTrue(isset($collection[0]));
        static::assertTrue(isset($collection[1]));
        static::assertTrue(isset($collection[2]));
        static::assertTrue(isset($collection[3]));
    }

    public function testError(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('The value of a collection value type requires the following types `string`.');

        $data = [
            1, 'string', 3, 4,
        ];

        new VectorString($data);
    }

    public function testError2(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('The value of a collection key type requires the following types `int`.');

        $data = [
            'hello' => 'world',
        ];

        new VectorString($data);
    }
}
