<?php

declare(strict_types=1);

namespace Tests\Support;

use Leevel\Kernel\Utils\Api;
use Leevel\Support\Struct;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => 'Struct 结构体',
    'path' => 'component/collection/struct',
])]
final class StructTest extends TestCase
{
    #[Api([
        'zh-CN:title' => '基本使用',
    ])]
    public function testBaseUse(): void
    {
        $data = [
            'a' => 'hello', 'b' => 2, 'c' => 'world',
        ];

        $collection = new Struct($data, 'string', 'int', 'string');
        self::assertSame($collection['a'], 'hello');
        self::assertSame($collection['b'], 2);
        self::assertSame($collection['c'], 'world');
        self::assertTrue(isset($collection['a']));
        self::assertTrue(isset($collection['b']));
        self::assertTrue(isset($collection['c']));
    }

    #[Api([
        'zh-CN:title' => '类型不满足直接抛出异常',
    ])]
    public function testError(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('The value with offset b of a collection value type requires the following types `int`.');

        $data = [
            'a' => 'hello', 'b' => 'hello', 'c' => 'world',
        ];

        new Struct($data, 'string', 'int', 'string');
    }

    #[Api([
        'zh-CN:title' => '类型和值数量不匹配直接抛出异常',
    ])]
    public function testError1(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The number of elements does not match the number of types.');

        $data = [
            'a' => 'hello', 'b' => 'hello', 'c' => 'world',
        ];

        new Struct($data, 'string', 'string', 'string', 'int');
    }

    public function testError2(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value type cannot be empty.');

        $data = [];

        new Struct($data);
    }

    public function testError3(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('The value of a collection key type requires the following types `string`.');

        $data = [
            'a' => 'hello', 'b' => 'hello', 'c' => 'world',
        ];

        $tuple = new Struct($data, 'string', 'string', 'string');
        $tuple[2] = 1;
    }

    public function testError4(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The number of elements does not match the number of types.');

        $data = [
            'a' => 'hello', 'b' => 'hello', 'c' => 'world',
        ];

        $tuple = new Struct($data, 'string', 'string', 'string');
        $tuple['d'] = 'hello';
    }
}
