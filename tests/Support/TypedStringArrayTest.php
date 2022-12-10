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
 */
class TypedStringArrayTest extends TestCase
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
        $this->assertSame($collection[0], 'h');
        $this->assertSame($collection[1], 'l');
        $this->assertSame($collection[2], 'w');
        $this->assertSame($collection[3], 'd');
        $this->assertTrue(isset($collection[0]));
        $this->assertTrue(isset($collection[1]));
        $this->assertTrue(isset($collection[2]));
        $this->assertTrue(isset($collection[3]));
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
