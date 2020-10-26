<?php

declare(strict_types=1);

/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Validate\Validator;

use Leevel\Validate\Validator;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="Validator.not_between_equal",
 *     zh-CN:title="验证器.未处于 betweenEqual 范围，包含等于",
 *     path="validate/validator/notbetweenequal",
 *     zh-CN:description="",
 * )
 */
class NotBetweenEqualTest extends TestCase
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
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\NotBetweenEqualTest::class, 'baseUseProvider')]}
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
                'name'     => 'not_between_equal:'.$param,
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
            ['a', 'a,z'],
            ['z', 'a,z'],
            ['1', '1,5'],
            ['5', '1,5'],
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
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\NotBetweenEqualTest::class, 'badProvider')]}
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
                'name'     => 'not_between_equal:'.$param,
            ]
        );

        $this->assertFalse($validate->success());
    }

    public function badProvider(): array
    {
        return [
            ['1.1', '1,5'],
            ['2', '1,5'],
            ['3', '1,5'],
            ['4', '1,5'],
            ['4.5', '1,5'],
            ['b', 'a,z'],
            ['c', 'a,z'],
        ];
    }

    /**
     * @api(
     *     zh-CN:title="not_between_equal 参数缺失",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testMissParam(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Missing the first or second element of param.'
        );

        $validate = new Validator(
            [
                'name' => '',
            ],
            [
                'name'     => 'not_between_equal',
            ]
        );

        $validate->success();
    }

    public function testMissParam2(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Missing the first or second element of param.'
        );

        $validate = new Validator(
            [
                'name' => '',
            ],
            [
                'name'     => 'not_between_equal:1',
            ]
        );

        $validate->success();
    }
}
