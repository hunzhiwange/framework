<?php

declare(strict_types=1);

namespace Tests\Validate\Validator;

use Leevel\Validate\Validator;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="Validator.email",
 *     zh-CN:title="验证器.是否为电子邮件",
 *     path="validate/validator/email",
 *     zh-CN:description="",
 * )
 */
final class EmailTest extends TestCase
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
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\EmailTest::class, 'baseUseProvider')]}
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
                'name' => 'email',
            ]
        );

        static::assertTrue($validate->success());
    }

    public static function baseUseProvider(): array
    {
        return [
            ['635750556@qq.com'],
            ['helloworld@gmail.com'],
            ['foobar@example.com'],
            ['foo+bar/hello-world@example.com'],
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
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\EmailTest::class, 'badProvider')]}
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
                'name' => 'email',
            ]
        );

        static::assertFalse($validate->success());
    }

    public static function badProvider(): array
    {
        return [
            ['hello "@email.com'],
            ['foo bar @test.com'],
            ['foo bar@test.com'],
            ['foobar\t@test.com'],
            [['foo' => 'bar']],
        ];
    }
}
