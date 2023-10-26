<?php

declare(strict_types=1);

namespace Tests\Support;

use Leevel\Kernel\Utils\Api;
use Leevel\Support\MapIntMixed;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => 'MapIntMixed 映射是键值为 int 的混合型有序键值数据结构',
    'path' => 'component/collection/map_int_mixed',
])]
final class MapIntMixedTest extends TestCase
{
    #[Api([
        'zh-CN:title' => '混合型',
    ])]
    public function test1(): void
    {
        $data = [
            'hello',
            1,
        ];

        $collection = new MapIntMixed($data);
        static::assertSame($collection[0], 'hello');
        static::assertSame($collection[1], 1);
        static::assertTrue(isset($collection[0]));
        static::assertTrue(isset($collection[1]));
    }

    #[Api([
        'zh-CN:title' => '字符串',
    ])]
    public function test2(): void
    {
        $data = [
            'hello',
            'world',
        ];

        $collection = new MapIntMixed($data);
        static::assertSame($collection[0], 'hello');
        static::assertSame($collection[1], 'world');
        static::assertTrue(isset($collection[0]));
        static::assertTrue(isset($collection[1]));
    }

    #[Api([
        'zh-CN:title' => '整形',
    ])]
    public function test3(): void
    {
        $data = [
            1,
            2,
        ];

        $collection = new MapIntMixed($data);
        static::assertSame($collection[0], 1);
        static::assertSame($collection[1], 2);
        static::assertTrue(isset($collection[0]));
        static::assertTrue(isset($collection[1]));
    }

    public function testError(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('The value of a collection key type requires the following types `int`.');

        $data = [
            'h' => 1,
        ];

        new MapIntMixed($data);
    }
}
