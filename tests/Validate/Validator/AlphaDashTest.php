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
use stdClass;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="Validator.alpha_dash",
 *     zh-CN:title="验证器.符串是否为数字、下划线、短横线和字母",
 *     path="validate/validator/alphadash",
 *     zh-CN:description="",
 * )
 */
class AlphaDashTest extends TestCase
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
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\AlphaDashTest::class, 'baseUseProvider')]}
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
                'name'     => 'alpha_dash',
            ]
        );

        $this->assertTrue($validate->success());
    }

    public function baseUseProvider(): array
    {
        return [
            ['abc'],
            ['ABC'],
            ['123abc'],
            ['4ABC'],
            ['A44bc'],
            ['ab1c'],
            ['AB2C'],
            ['Ab3c'],
            ['--abc'],
            ['A_BC'],
            ['123a_bc'],
            ['4A--BC'],
            ['A______---44bc'],
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
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\AlphaDashTest::class, 'badProvider')]}
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
                'name'     => 'alpha_dash',
            ]
        );

        $this->assertFalse($validate->success());
    }

    public function badProvider(): array
    {
        return [
            [' '],
            ['not numeric'],
            [new stdClass()],
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
