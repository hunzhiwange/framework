<?php

declare(strict_types=1);

namespace Tests\Validate\Validator;

use Leevel\Validate\Validator;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="Validator.timezone",
 *     zh-CN:title="验证器.是否为正确的时区",
 *     path="validate/validator/timezone",
 *     zh-CN:description="",
 * )
 *
 * @internal
 */
final class TimezoneTest extends TestCase
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
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\TimezoneTest::class, 'baseUseProvider')]}
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
                'name' => 'timezone',
            ]
        );

        static::assertTrue($validate->success());
    }

    public static function baseUseProvider(): array
    {
        return [
            ['-0400'],
            ['EDT'],
            ['Asia/Shanghai'], // 上海
            ['Asia/Hong_Kong'], // 香港
            ['Asia/Chongqing'], // 重庆
            ['Asia/Urumqi'], // 乌鲁木齐
            ['Asia/Macao'], // 澳门
            ['Asia/Taipei'], // 台北
            ['Asia/Singapore'], //  新加坡
            ['PRC'], // 设置中国时区
            ['Etc/GMT'], // 格林威治标准时间
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
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\TimezoneTest::class, 'badProvider')]}
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
                'name' => 'timezone',
            ]
        );

        static::assertFalse($validate->success());
    }

    public static function badProvider(): array
    {
        return [
            ['Asia/foo'],
            ['Asia/bar'],
            ['foo'],
            ['bar'],
            ['2018'],
            ['2018-44-22'],
            ['2018-12-42'],
            [''],
            [[1, 2]],
        ];
    }
}
