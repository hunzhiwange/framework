<?php

declare(strict_types=1);

namespace Tests\Support;

use Leevel\Kernel\Utils\Api;
use Leevel\Support\SetInt;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => 'SetInt 没有重复项的整型有序数据结构',
    'path' => 'component/collection/set_int',
])]
final class SetIntTest extends TestCase
{
    #[Api([
        'zh-CN:title' => '基本使用',
    ])]
    public function testBaseUse(): void
    {
        $data = [
            1, 2, 3,
        ];

        $collection = new SetInt($data, 'string', 'int', 'string');
        static::assertSame($collection[0], 1);
        static::assertSame($collection[1], 2);
        static::assertSame($collection[2], 3);
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
        $this->expectExceptionMessage('The value of a collection value type requires the following types `int`.');

        $data = [
            'hello', 'world',
        ];

        new SetInt($data);
    }
}
