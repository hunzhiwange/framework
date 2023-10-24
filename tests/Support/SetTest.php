<?php

declare(strict_types=1);

namespace Tests\Support;

use Leevel\Kernel\Utils\Api;
use Leevel\Support\Set;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => 'Set 没有重复项的字符串或者整型有序数据结构',
    'path' => 'component/collection/set',
])]
final class SetTest extends TestCase
{
    #[Api([
        'zh-CN:title' => '整形类型',
    ])]
    public function test1(): void
    {
        $data = [
            1, 2, 3,
        ];

        $collection = new Set($data, 'int');
        static::assertSame($collection[0], 1);
        static::assertSame($collection[1], 2);
        static::assertSame($collection[2], 3);
        static::assertTrue(isset($collection[0]));
        static::assertTrue(isset($collection[1]));
        static::assertTrue(isset($collection[2]));
    }

    #[Api([
        'zh-CN:title' => '字符串类型',
    ])]
    public function test2(): void
    {
        $data = [
            'hello', 't', 'world',
        ];

        $collection = new Set($data, 'string');
        static::assertSame($collection[0], 'hello');
        static::assertSame($collection[1], 't');
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
        $this->expectExceptionMessage('The value of a collection value type requires the following types `int`.');

        $data = [
            'hello', 'world',
        ];

        new Set($data, 'int');
    }

    public function testError1(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('KeySet value type must be string or int, int2 given.');

        $data = [
            'hello', 'world',
        ];

        new Set($data, 'int2');
    }
}
