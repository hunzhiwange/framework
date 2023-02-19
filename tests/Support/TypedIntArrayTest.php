<?php

declare(strict_types=1);

namespace Tests\Support;

use Leevel\Support\TypedIntArray;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="整数集合 collection",
 *     path="component/collection/typedint",
 *     zh-CN:description="",
 * )
 */
final class TypedIntArrayTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="基本使用",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $data = [
            1, 2, 3, 4,
        ];

        $collection = new TypedIntArray($data);
        static::assertSame($collection[0], 1);
        static::assertSame($collection[1], 2);
        static::assertSame($collection[2], 3);
        static::assertSame($collection[3], 4);
        static::assertTrue(isset($collection[0]));
        static::assertTrue(isset($collection[1]));
        static::assertTrue(isset($collection[2]));
        static::assertTrue(isset($collection[3]));
    }

    /**
     * @api(
     *     zh-CN:title="从 HTTP 请求创建整数索引数组",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testFromRequest(): void
    {
        $collection = TypedIntArray::fromRequest([
            1, 2, 3, 4,
        ]);
        static::assertSame($collection[0], 1);
        static::assertSame($collection[1], 2);
        static::assertSame($collection[2], 3);
        static::assertSame($collection[3], 4);
        static::assertTrue(isset($collection[0]));
        static::assertTrue(isset($collection[1]));
        static::assertTrue(isset($collection[2]));
        static::assertTrue(isset($collection[3]));

        $collection = TypedIntArray::fromRequest('1,2,3,4');
        static::assertSame($collection[0], 1);
        static::assertSame($collection[1], 2);
        static::assertSame($collection[2], 3);
        static::assertSame($collection[3], 4);
        static::assertTrue(isset($collection[0]));
        static::assertTrue(isset($collection[1]));
        static::assertTrue(isset($collection[2]));
        static::assertTrue(isset($collection[3]));
    }

    public function testError(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('The value of a collection value type requires the following types `int`.');

        $data = [
            1, 'string', 3, 4,
        ];

        new TypedIntArray($data);
    }

    public function testError2(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('The value of a collection key type requires the following types `int`.');

        $data = [
            'hello' => 'world',
        ];

        new TypedIntArray($data);
    }
}
