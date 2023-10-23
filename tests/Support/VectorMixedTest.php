<?php

declare(strict_types=1);

namespace Tests\Support;

use Leevel\Kernel\Utils\Api;
use Leevel\Support\VectorMixed;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => 'VectorMixed 动态数组',
    'path' => 'component/collection/vectormixed',
])]
final class VectorMixedTest extends TestCase
{
    #[Api([
        'zh-CN:title' => '基本使用',
    ])]
    public function testBaseUse(): void
    {
        $data = [
            1, 'hello', 3, 4,
        ];

        $collection = new VectorMixed($data);
        static::assertSame($collection[0], 1);
        static::assertSame($collection[1], 'hello');
        static::assertSame($collection[2], 3);
        static::assertSame($collection[3], 4);
        static::assertTrue(isset($collection[0]));
        static::assertTrue(isset($collection[1]));
        static::assertTrue(isset($collection[2]));
        static::assertTrue(isset($collection[3]));
    }
}
