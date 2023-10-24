<?php

declare(strict_types=1);

namespace Tests\Support;

use Leevel\Kernel\Utils\Api;
use Leevel\Support\SetString;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => 'SetString 没有重复项的字符串有序数据结构',
    'path' => 'component/collection/set_string',
])]
final class SetStringTest extends TestCase
{
    #[Api([
        'zh-CN:title' => '基本使用',
    ])]
    public function testBaseUse(): void
    {
        $data = [
            'hello', 't', 'world',
        ];

        $collection = new SetString($data, 'string', 'int', 'string');
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
        $this->expectExceptionMessage('The value of a collection value type requires the following types `string`.');

        $data = [
            'hello', 2, 'world',
        ];

        new SetString($data);
    }
}
