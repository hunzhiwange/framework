<?php

declare(strict_types=1);

namespace Tests\Support;

use Leevel\Kernel\Utils\Api;
use Leevel\Support\Tuple;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => 'Tuple 元组',
    'path' => 'component/collection/tuple',
])]
final class TupleTest extends TestCase
{
    #[Api([
        'zh-CN:title' => '基本使用',
    ])]
    public function testBaseUse(): void
    {
        $data = [
            'hello', 2, 'world',
        ];

        $collection = new Tuple($data, 'string', 'int', 'string');
        static::assertSame($collection[0], 'hello');
        static::assertSame($collection[1], 2);
        static::assertSame($collection[2], 'world');
        static::assertTrue(isset($collection[0]));
        static::assertTrue(isset($collection[1]));
        static::assertTrue(isset($collection[2]));
    }

    #[Api([
        'zh-CN:title' => '类型不满足直接抛出异常',
    ])]
    public function testError(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('The value with index 1 of a collection value type requires the following types `int`.');

        $data = [
            'hello', 'hello', 'world',
        ];

        new Tuple($data, 'string', 'int', 'string');
    }

    #[Api([
        'zh-CN:title' => '类型和值数量不匹配直接抛出异常',
    ])]
    public function testError1(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The number of elements does not match the number of types.');

        $data = [
            'hello', 'hello', 'world',
        ];

        new Tuple($data, 'string', 'string', 'string', 'int');
    }

    public function testError2(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value type cannot be empty.');

        $data = [];

        new Tuple($data);
    }

    public function testError3(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('The value with index 2 of a collection value type requires the following types `string`.');

        $data = [
            'hello', 'hello', 'world',
        ];

        $tuple = new Tuple($data, 'string', 'string', 'string');
        $tuple[2] = 1;
    }
}
