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
        self::assertSame($collection['h'], 'hello');
        self::assertSame($collection['w'], 1);
        self::assertTrue(isset($collection['h']));
        self::assertTrue(isset($collection['w']));
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
        self::assertSame($collection['h'], 'hello');
        self::assertSame($collection['w'], 'world');
        self::assertTrue(isset($collection['h']));
        self::assertTrue(isset($collection['w']));
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
        self::assertSame($collection['h'], 1);
        self::assertSame($collection['w'], 2);
        self::assertTrue(isset($collection['h']));
        self::assertTrue(isset($collection['w']));
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
        self::assertSame($collection[0], 'hello');
        self::assertSame($collection[1], 1);
        self::assertTrue(isset($collection[0]));
        self::assertTrue(isset($collection[1]));
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
        self::assertSame($collection[0], 'hello');
        self::assertSame($collection[1], 'world');
        self::assertTrue(isset($collection[0]));
        self::assertTrue(isset($collection[1]));
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
        self::assertSame($collection[0], 1);
        self::assertSame($collection[1], 2);
        self::assertTrue(isset($collection[0]));
        self::assertTrue(isset($collection[1]));
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
