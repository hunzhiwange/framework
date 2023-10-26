<?php

declare(strict_types=1);

namespace Tests\Support;

use Leevel\Kernel\Utils\Api;
use Leevel\Support\MapInt;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => 'MapInt 映射是键值为 int 的有序键值数据结构',
    'path' => 'component/collection/map_int',
])]
final class MapIntTest extends TestCase
{
    #[Api([
        'zh-CN:title' => '字符串',
    ])]
    public function testBaseUse(): void
    {
        $data = [
            'hello',
            'world',
        ];

        $collection = new MapInt($data, 'string');
        static::assertSame($collection[0], 'hello');
        static::assertSame($collection[1], 'world');
        static::assertTrue(isset($collection[0]));
        static::assertTrue(isset($collection[1]));
    }

    #[Api([
        'zh-CN:title' => '整形',
    ])]
    public function test1(): void
    {
        $data = [
            1,
            2,
        ];

        $collection = new MapInt($data, 'int');
        static::assertSame($collection[0], 1);
        static::assertSame($collection[1], 2);
        static::assertTrue(isset($collection[0]));
        static::assertTrue(isset($collection[1]));
    }

    public function testError(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('The value of a collection value type requires the following types `string`.');

        $data = [
            1, 'string', 3, 4,
        ];

        new MapInt($data, 'string');
    }

    public function testError1(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('The value of a collection key type requires the following types `int`.');

        $data = [
            'h' => 1,
        ];

        new MapInt($data, 'string');
    }
}
