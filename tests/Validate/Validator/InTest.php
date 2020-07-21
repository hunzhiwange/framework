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
 *     title="Validator.in",
 *     zh-CN:title="验证器.是否处于某个范围",
 *     path="validate/validator/in",
 *     description="",
 * )
 */
class InTest extends TestCase
{
    /**
     * @dataProvider baseUseProvider
     *
     * @param mixed $value
     *
     * @api(
     *     title="验证通过的数据",
     *     description="
     * 以下是通过的校验数据示例。
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\InTest::class, 'baseUseProvider')]}
     * ```
     *
     * 上面的数据是测试的数据提供者。
     * ",
     *     note="",
     * )
     */
    public function testBaseUse($value, string $param): void
    {
        $validate = new Validator(
            [
                'name' => $value,
            ],
            [
                'name'     => 'in:'.$param,
            ]
        );

        $this->assertTrue($validate->success());
    }

    public function baseUseProvider(): array
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
     * @dataProvider badProvider
     *
     * @param mixed $value
     *
     * @api(
     *     title="未验证通过的数据",
     *     description="
     * 以下是未通过的校验数据示例。
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\InTest::class, 'badProvider')]}
     * ```
     *
     * 上面的数据是测试的数据提供者。
     * ",
     *     note="",
     * )
     */
    public function testBad($value, string $param): void
    {
        $validate = new Validator(
            [
                'name' => $value,
            ],
            [
                'name'     => 'in:'.$param,
            ]
        );

        $this->assertFalse($validate->success());
    }

    public function badProvider(): array
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
     * @api(
     *     title="in 参数缺失",
     *     description="",
     *     note="",
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
                'name'     => 'in',
            ]
        );

        $validate->success();
    }
}
