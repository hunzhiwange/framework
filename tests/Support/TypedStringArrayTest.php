<?php

declare(strict_types=1);

namespace Tests\Support;

use Leevel\Support\TypedStringArray;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="字符串集合 collection",
 *     path="component/collection/typedstring",
 *     zh-CN:description="",
 * )
 *
 * @internal
 *
 * @coversNothing
 */
final class TypedStringArrayTest extends TestCase
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
            'h', 'l', 'w', 'd',
        ];

        $collection = new TypedStringArray($data);
        static::assertSame($collection[0], 'h');
        static::assertSame($collection[1], 'l');
        static::assertSame($collection[2], 'w');
        static::assertSame($collection[3], 'd');
        static::assertTrue(isset($collection[0]));
        static::assertTrue(isset($collection[1]));
        static::assertTrue(isset($collection[2]));
        static::assertTrue(isset($collection[3]));
    }

    public function testError(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('The value of a collection value type requires the following types `string`.');

        $data = [
            1, 'string', 3, 4,
        ];

        new TypedStringArray($data);
    }

    public function testError2(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('The value of a collection key type requires the following types `int`.');

        $data = [
            'hello' => 'world',
        ];

        new TypedStringArray($data);
    }
}
