<?php

declare(strict_types=1);

namespace Tests\Validate\Validator;

use Leevel\Validate\Validator;
use stdClass;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="Validator.json",
 *     zh-CN:title="验证器.验证是否为正常的 JSON 数据",
 *     path="validate/validator/json",
 *     zh-CN:description="",
 * )
 */
class JsonTest extends TestCase
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
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\JsonTest::class, 'baseUseProvider')]}
     * ```
     *
     * `\Tests\Validate\Validator\TestJson` 声明如下
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Validate\Validator\TestJson::class)]}
     * ```
     *
     * 上面的数据是测试的数据提供者。
     *
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
                'name'     => 'json',
            ]
        );

        $this->assertTrue($validate->success());
    }

    public function baseUseProvider(): array
    {
        return [
            ['"abc"'],
            ['{"foo":"bar"}'],
            [new TestJson()],
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
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\JsonTest::class, 'badProvider')]}
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
                'name'     => 'json',
            ]
        );

        $this->assertFalse($validate->success());
    }

    public function badProvider(): array
    {
        return [
            ['not numeric'],
            [[]],
            [new stdClass()],
            [['foo', 'bar']],
            [[1, 2]],
            ['Foo'],
            ['hEllo'],
            [null],
        ];
    }
}

class TestJson
{
    public function __toString()
    {
        return '{"hello":"world"}';
    }
}
