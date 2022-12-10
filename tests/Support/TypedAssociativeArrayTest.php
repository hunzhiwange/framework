<?php

declare(strict_types=1);

namespace Tests\Support;

use Leevel\Support\TypedAssociativeArray;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="关联数组集合 collection",
 *     path="component/collection/typedassociative",
 *     zh-CN:description="",
 * )
 */
class TypedAssociativeArrayTest extends TestCase
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
            'h' => 'hello',
            'w' => 'world',
        ];

        $collection = new TypedAssociativeArray($data);
        $this->assertSame($collection['h'], 'hello');
        $this->assertSame($collection['w'], 'world');
        $this->assertTrue(isset($collection['h']));
        $this->assertTrue(isset($collection['w']));
    }

    public function testError(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('The value of a collection key type requires the following types `string`.');

        $data = [
            1, 'string', 3, 4,
        ];

        new TypedAssociativeArray($data);
    }
}
