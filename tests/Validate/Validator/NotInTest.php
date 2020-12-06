<?php

declare(strict_types=1);

namespace Tests\Validate\Validator;

use Leevel\Validate\Validator;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="Validator.not_in",
 *     zh-CN:title="验证器.是否不处于某个范围",
 *     path="validate/validator/notin",
 *     zh-CN:description="",
 * )
 */
class NotInTest extends TestCase
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
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\NotInTest::class, 'baseUseProvider')]}
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
                'name'     => 'not_in:'.$param,
            ]
        );

        $this->assertTrue($validate->success());
    }

    public function baseUseProvider(): array
    {
        return [
            ['0.1', '1,5'],
            ['5.5', '1,5'],
            ['8', '1,5'],
            ['c', 'h,t'],
            ['w', 'h,t'],
            ['c', 'foo,bar'],
            ['w', 'h,hello,world,t'],
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
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\NotInTest::class, 'badProvider')]}
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
                'name'     => 'not_in:'.$param,
            ]
        );

        $this->assertFalse($validate->success());
    }

    public function badProvider(): array
    {
        return [
            [1, '1,5'],
            [2, '1,2,5'],
            [3, '1,3,5'],
            [4, '1,4,5'],
            [4.5, '1,4.5,5'],
            ['b', 'a,b,z'],
            ['c', 'a,c,z'],
            [1, '1,5'],
            [5, '1,5'],
            ['a', 'a,z'],
            ['z', 'a,z'],
        ];
    }

    /**
     * @api(
     *     zh-CN:title="not_in 参数缺失",
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
                'name'     => 'not_in',
            ]
        );

        $validate->success();
    }
}
