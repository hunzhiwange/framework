<?php

declare(strict_types=1);

namespace Tests\Validate\Validator;

use Leevel\Validate\Validator;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="Validator.chinese_alpha_dash",
 *     zh-CN:title="验证器.是否为中文、数字、下划线、短横线和字母",
 *     path="validate/validator/chinesealphadash",
 *     zh-CN:description="",
 * )
 */
final class ChineseAlphaDashTest extends TestCase
{
    /**
     * @dataProvider baseUseProvider
     *
     * @param mixed $value
     *
     * @api(
     *     zh-CN:title="验证通过的数据",
     *     zh-CN:description="
     * 以下是通过的校验数据示例。
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\ChineseAlphaDashTest::class, 'baseUseProvider')]}
     * ```
     *
     * 上面的数据是测试的数据提供者。
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse($value): void
    {
        $validate = new Validator(
            [
                'name' => $value,
            ],
            [
                'name' => 'chinese_alpha_dash',
            ]
        );

        static::assertTrue($validate->success());
    }

    public static function baseUseProvider(): array
    {
        return [
            ['abc'],
            ['ABC'],
            ['12国际3abc'],
            ['4ABC'],
            ['A44bc'],
            ['ab1c'],
            ['AB中国2C'],
            ['Ab3c'],
            ['--abc'],
            ['A_BC'],
            ['123a_bc'],
            ['4A--BC'],
            ['A__成都____---44bc'],
            ['ab1c'],
            ['A111B2C'],
            ['Ab--3c'],
            [123],
        ];
    }

    /**
     * @dataProvider badProvider
     *
     * @param mixed $value
     *
     * @api(
     *     zh-CN:title="未验证通过的数据",
     *     zh-CN:description="
     * 以下是未通过的校验数据示例。
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\ChineseAlphaDashTest::class, 'badProvider')]}
     * ```
     *
     * 上面的数据是测试的数据提供者。
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testBad($value): void
    {
        $validate = new Validator(
            [
                'name' => $value,
            ],
            [
                'name' => 'chinese_alpha_dash',
            ]
        );

        static::assertFalse($validate->success());
    }

    public static function badProvider(): array
    {
        return [
            [' '],
            ['not numeric'],
            [new \stdClass()],
            [['foo', 'bar']],
            [[1, 2]],
            ['this is a string'],
            [true],
            [[[], []]],
            ['not/numeric'],
            ['not\ numeric'],
            ['not?numeric'],
        ];
    }
}
