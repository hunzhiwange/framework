<?php

declare(strict_types=1);

namespace Tests\Support;

use Leevel\Kernel\Utils\Api;
use Leevel\Support\Map;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => 'Map 映射是有序键值数据结构',
    'path' => 'component/collection/map',
])]
final class MapTest extends TestCase
{
    #[Api([
        'zh-CN:title' => '字符串键-值混合型',
    ])]
    public function test1(): void
    {
        $data = [
            'h' => 'hello',
            'w' => 1,
        ];

        $collection = new Map($data, 'string', '');
        static::assertSame($collection['h'], 'hello');
        static::assertSame($collection['w'], 1);
        static::assertTrue(isset($collection['h']));
        static::assertTrue(isset($collection['w']));
    }

    #[Api([
        'zh-CN:title' => '字符串键-值字符串',
    ])]
    public function test2(): void
    {
        $data = [
            'h' => 'hello',
            'w' => 'world',
        ];

        $collection = new Map($data, 'string', 'string');
        static::assertSame($collection['h'], 'hello');
        static::assertSame($collection['w'], 'world');
        static::assertTrue(isset($collection['h']));
        static::assertTrue(isset($collection['w']));
    }

    #[Api([
        'zh-CN:title' => '字符串键-值整形',
    ])]
    public function test3(): void
    {
        $data = [
            'h' => 1,
            'w' => 2,
        ];

        $collection = new Map($data, 'string', 'int');
        static::assertSame($collection['h'], 1);
        static::assertSame($collection['w'], 2);
        static::assertTrue(isset($collection['h']));
        static::assertTrue(isset($collection['w']));
    }

    #[Api([
        'zh-CN:title' => '整形键-值混合型',
    ])]
    public function test4(): void
    {
        $data = [
            'hello',
            1,
        ];

        $collection = new Map($data, 'int', '');
        static::assertSame($collection[0], 'hello');
        static::assertSame($collection[1], 1);
        static::assertTrue(isset($collection[0]));
        static::assertTrue(isset($collection[1]));
    }

    #[Api([
        'zh-CN:title' => '整形键-值字符串',
    ])]
    public function test5(): void
    {
        $data = [
            'hello',
            'world',
        ];

        $collection = new Map($data, 'int', 'string');
        static::assertSame($collection[0], 'hello');
        static::assertSame($collection[1], 'world');
        static::assertTrue(isset($collection[0]));
        static::assertTrue(isset($collection[1]));
    }

    #[Api([
        'zh-CN:title' => '整形键-值整形',
    ])]
    public function test6(): void
    {
        $data = [
            1,
            2,
        ];

        $collection = new Map($data, 'int', 'int');
        static::assertSame($collection[0], 1);
        static::assertSame($collection[1], 2);
        static::assertTrue(isset($collection[0]));
        static::assertTrue(isset($collection[1]));
    }

    public function testError(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Key type must be int or string but `not_found` given.');

        $data = [
            'hello',
            'world',
        ];

        new Map($data, 'not_found', '');
    }
}
