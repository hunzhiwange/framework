<?php

declare(strict_types=1);

namespace Tests\Support;

use Leevel\Support\MapStringMixed;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="MapStringMixed 映射",
 *     path="component/collection/map_string_mixed",
 *     zh-CN:description="",
 * )
 *
 * @internal
 */
final class MapStringMixedTest extends TestCase
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

        $collection = new MapStringMixed($data);
        static::assertSame($collection['h'], 'hello');
        static::assertSame($collection['w'], 'world');
        static::assertTrue(isset($collection['h']));
        static::assertTrue(isset($collection['w']));
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
