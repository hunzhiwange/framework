<?php

declare(strict_types=1);

namespace Tests\Support;

use Leevel\Kernel\Utils\Api;
use Leevel\Support\MapStringMixed;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => 'MapStringMixed 映射是键值为 string 的混合型有序键值数据结构',
    'path' => 'component/collection/map-string-mixed',
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
        self::assertSame($collection['h'], 'hello');
        self::assertSame($collection['w'], 1);
        self::assertTrue(isset($collection['h']));
        self::assertTrue(isset($collection['w']));
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
        self::assertSame($collection['h'], 'hello');
        self::assertSame($collection['w'], 'world');
        self::assertTrue(isset($collection['h']));
        self::assertTrue(isset($collection['w']));
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
        self::assertSame($collection['h'], 1);
        self::assertSame($collection['w'], 2);
        self::assertTrue(isset($collection['h']));
        self::assertTrue(isset($collection['w']));
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
