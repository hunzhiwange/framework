<?php

declare(strict_types=1);

namespace Tests\Validate\Validator;

use Leevel\Validate\Validator;
use stdClass;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="Validator.before",
 *     zh-CN:title="验证器.验证在给定日期之前",
 *     path="validate/validator/before",
 *     zh-CN:description="",
 * )
 */
class BeforeTest extends TestCase
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
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\BeforeTest::class, 'baseUseProvider')]}
     * ```
     *
     * 上面的数据是测试的数据提供者。
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse($value, string $param): void
    {
        $validate = new Validator(
            [
                'name'  => $value,
                'name2' => '2018-08-10',
            ],
            [
                'name'     => 'before:'.$param,
            ]
        );

        $this->assertTrue($validate->success());
    }

    public function baseUseProvider(): array
    {
        return [
            ['2018-08-11', '2018-08-14'],
            ['2018-08-09', 'name2'],
            ['2018-08-13', '2018-08-14|date_format:Y-m-d'],
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
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\BeforeTest::class, 'badProvider')]}
     * ```
     *
     * 上面的数据是测试的数据提供者。
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testBad($value, string $param): void
    {
        $validate = new Validator(
            [
                'name' => $value,
            ],
            [
                'name'     => 'before:'.$param,
            ]
        );

        $this->assertFalse($validate->success());
    }

    public function badProvider(): array
    {
        return [
            ['2018-08-17', '2018-08-17'],
            ['2018-08-17', '2018-08-15'],
            ['2018-08-15', 'name2'],
            ['2018-08-15', '2018-08-14|date_format:Y-m'],
            [new stdClass(), '1.1'],
            [[], '2018-08-15'],
            [true, '2018-08-15'],
            [false, '2018-08-15'],
        ];
    }

    /**
     * @api(
     *     zh-CN:title="日期格式化不一致无法通过验证",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testMakeDateTimeFormatWithNewDateTimeExceptionError(): void
    {
        $validate = new Validator(
            [
                'name'  => '2018-08-10',
            ],
            [
                'name'     => 'before:foobar|date_format:y',
            ]
        );

        $this->assertFalse($validate->success());
    }

    /**
     * @api(
     *     zh-CN:title="before 参数缺失",
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
                'name'     => 'before',
            ]
        );

        $validate->success();
    }
}
