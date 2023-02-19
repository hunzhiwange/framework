<?php

declare(strict_types=1);

namespace Tests\Validate\Validator;

use Leevel\Validate\Validator;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="Validator.equal_to",
 *     zh-CN:title="验证器.两个字段是否相同",
 *     path="validate/validator/equalto",
 *     zh-CN:description="",
 * )
 */
final class EqualToTest extends TestCase
{
    /**
     * @dataProvider baseUseProvider
     *
     * @param mixed $value
     * @param mixed $valueCompare
     *
     * @api(
     *     zh-CN:title="验证通过的数据",
     *     zh-CN:description="
     * 以下是通过的校验数据示例。
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\EqualToTest::class, 'baseUseProvider')]}
     * ```
     *
     * 上面的数据是测试的数据提供者。
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse($value, $valueCompare, string $param): void
    {
        $validate = new Validator(
            [
                'name' => $value,
                'name2' => $valueCompare,
                'name3' => 'test',
            ],
            [
                'name' => 'equal_to:'.$param,
            ]
        );

        static::assertTrue($validate->success());
    }

    public static function baseUseProvider(): array
    {
        return [
            ['foo', 'foo', 'name2'],
            ['bar', 'bar', 'name2'],
            ['test', '', 'name3'],
        ];
    }

    /**
     * @dataProvider badProvider
     *
     * @param mixed $value
     * @param mixed $valueCompare
     *
     * @api(
     *     zh-CN:title="未验证通过的数据",
     *     zh-CN:description="
     * 以下是未通过的校验数据示例。
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\EqualToTest::class, 'badProvider')]}
     * ```
     *
     * 上面的数据是测试的数据提供者。
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testBad($value, $valueCompare, string $param): void
    {
        $validate = new Validator(
            [
                'name' => $value,
                'name2' => $valueCompare,
                'name3' => new \stdClass(),
            ],
            [
                'name' => 'equal_to:'.$param,
            ]
        );

        static::assertFalse($validate->success());
    }

    public static function badProvider(): array
    {
        return [
            ['foo', 'foo2', 'name2'],
            ['bar', 'bar2', 'name2'],
            [new \stdClass(), new \stdClass(), 'name2'], // 非全等
            [new \stdClass(), '', 'name3'], // 非全等
            [['foo', 'bar'], '', 'name3'],
        ];
    }

    /**
     * @api(
     *     zh-CN:title="equal_to 参数缺失",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testMissParam(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Missing the first element of param.'
        );

        $validate = new Validator(
            [
                'name' => '',
            ],
            [
                'name' => 'equal_to',
            ]
        );

        $validate->success();
    }
}
