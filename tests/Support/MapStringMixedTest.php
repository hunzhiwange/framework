<?php

declare(strict_types=1);

namespace Tests\Support;

use Leevel\Kernel\Utils\Api;
use Leevel\Support\MapStringMixed;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => 'MapStringMixed 映射是键值为 string 的混合型有序键值数据结构',
    'path' => 'component/collection/map_string_mixed',
])]
final class MapStringMixedTest extends TestCase
{
    #[Api([
        'zh-CN:title' => '混合型',
    ])]
    public function test1(): void
    {
        $data = [
            'h' => 'hello',
            'w' => 1,
        ];

        $collection = new MapStringMixed($data);
        static::assertSame($collection['h'], 'hello');
        static::assertSame($collection['w'], 1);
        static::assertTrue(isset($collection['h']));
        static::assertTrue(isset($collection['w']));
    }

    #[Api([
        'zh-CN:title' => '字符串',
    ])]
    public function test2(): void
    {
        $data = [
            'h' => 'hello',
            'w' => 'world',
        ];

        $collection = new MapStringMixed($data);
        static::assertSame($collection['h'], 'hello');
        static::assertSame($collection['w'], 'world');
        static::assertTrue(isset($collection['h']));
        static::assertTrue(isset($collection['w']));
    }

    #[Api([
        'zh-CN:title' => '整形',
    ])]
    public function test3(): void
    {
        $data = [
            'h' => 1,
            'w' => 2,
        ];

        $collection = new MapStringMixed($data);
        static::assertSame($collection['h'], 1);
        static::assertSame($collection['w'], 2);
        static::assertTrue(isset($collection['h']));
        static::assertTrue(isset($collection['w']));
    }

    public function testError(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('The value of a collection key type requires the following types `string`.');

        $data = [
            1, 'string', 3, 4,
        ];

        new MapStringMixed($data);
    }
}
