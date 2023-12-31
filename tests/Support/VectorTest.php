<?php

declare(strict_types=1);

namespace Tests\Support;

use Leevel\Kernel\Utils\Api;
use Leevel\Support\Vector;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => 'Vector 动态数组',
    'path' => 'component/collection/vector',
])]
final class VectorTest extends TestCase
{
    #[Api([
        'zh-CN:title' => '基本使用',
    ])]
    public function testBaseUse(): void
    {
        $data = [
            1, 'hello', 3, 4,
        ];

        $collection = new Vector($data);
        static::assertSame($collection[0], 1);
        static::assertSame($collection[1], 'hello');
        static::assertSame($collection[2], 3);
        static::assertSame($collection[3], 4);
        static::assertTrue(isset($collection[0]));
        static::assertTrue(isset($collection[1]));
        static::assertTrue(isset($collection[2]));
        static::assertTrue(isset($collection[3]));
    }

    public function test2(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Collection elements must be list.'
        );

        $data = [
            1, 'hello', 3, 4,
        ];

        $collection = new Vector($data);
        static::assertSame($collection[0], 1);
        static::assertSame($collection[1], 'hello');
        static::assertSame($collection[2], 3);
        static::assertSame($collection[3], 4);

        $collection[5] = 'hello';
    }
}
