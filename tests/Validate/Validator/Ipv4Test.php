<?php

declare(strict_types=1);

namespace Tests\Validate\Validator;

use Leevel\Validate\Validator;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="Validator.ipv4",
 *     zh-CN:title="验证器.是否为 ipv4",
 *     path="validate/validator/ipv4",
 *     zh-CN:description="",
 * )
 */
class Ipv4Test extends TestCase
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
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\Ipv4Test::class, 'baseUseProvider')]}
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
                'name'     => 'ipv4',
            ]
        );

        $this->assertTrue($validate->success());
    }

    public function baseUseProvider(): array
    {
        return [
            ['8.8.8.8'],
            ['8.8.4.4'],
            ['127.0.0.1'],
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
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\Ipv4Test::class, 'badProvider')]}
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
                'name'     => 'ipv4',
            ]
        );

        $this->assertFalse($validate->success());
    }

    public function badProvider(): array
    {
        return [
            ['2022222201:3CA1:010F:001A:121B:0000:0000:0010'],
            ['2001:000000:0000:001A:0000:0000:0000:0010'],
            ['8.8999.8.8'],
            ['8.2228.4.4'],
            [' 127.0.0.1 '],
            ['20222222201:4860:4860::8888'],
            ['9999999:4860:4860::8844'],
            [false],
            [1.1],
            ['0.0'],
            ['-0.0'],
        ];
    }
}
