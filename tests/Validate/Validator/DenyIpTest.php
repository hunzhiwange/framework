<?php

declare(strict_types=1);

namespace Tests\Validate\Validator;

use Leevel\Validate\Validator;
use stdClass;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="Validator.deny_ip",
 *     zh-CN:title="验证器.验证 IP 许可",
 *     path="validate/validator/denyip",
 *     zh-CN:description="",
 * )
 */
class DenyIpTest extends TestCase
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
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\DenyIpTest::class, 'baseUseProvider')]}
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
                'name' => $value,
            ],
            [
                'name'     => 'deny_ip:'.$param,
            ]
        );

        $this->assertTrue($validate->success());
    }

    public function baseUseProvider(): array
    {
        return [
            ['8.8.8.10', '8.8.8.8,127.0.0.1'],
            ['127.0.5.1', '8.8.8.8,127.0.0.1'],
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
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\DenyIpTest::class, 'badProvider')]}
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
                'name'     => 'deny_ip:'.$param,
            ]
        );

        $this->assertFalse($validate->success());
    }

    public function badProvider(): array
    {
        return [
            ['8.8.8.8', '8.8.8.8,127.0.0.1'],
            ['127.0.0.1', '8.8.8.8,127.0.0.1'],
            [new stdClass(), '8.8.8.8,127.0.0.1'],
            [[1, 2], '8.8.8.8,127.0.0.1'],
            [[[], []], '8.8.8.8,127.0.0.1'],
            [['foo', 'bar'], '8.8.8.8,127.0.0.1'],
            [true, '8.8.8.8,127.0.0.1'],
            [false, '8.8.8.8,127.0.0.1'],
        ];
    }

    /**
     * @api(
     *     zh-CN:title="deny_ip 参数缺失",
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
                'name'     => 'deny_ip',
            ]
        );

        $validate->success();
    }
}
