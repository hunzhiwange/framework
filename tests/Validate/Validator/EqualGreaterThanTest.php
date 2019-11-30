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
 * equalGreaterThan test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.09
 *
 * @version 1.0
 *
 * @api(
 *     title="Validator.equal_greater_than",
 *     zh-CN:title="验证器.大于或者全等",
 *     path="component/validate/validator/equalgreaterthan",
 *     description="",
 * )
 */
class EqualGreaterThanTest extends TestCase
{
    /**
     * @dataProvider baseUseProvider
     *
     * @param mixed $value
     * @param mixed $param
     *
     * @api(
     *     title="验证通过的数据",
     *     description="
     * 以下是通过的校验数据示例。
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\EqualGreaterThanTest::class, 'baseUseProvider')]}
     * ```
     *
     * 上面的数据是测试的数据提供者。
     * ",
     *     note="",
     * )
     */
    public function testBaseUse($value, $param): void
    {
        $validate = new Validator(
            [
                'name' => $value,
            ],
            [
                'name'     => 'equal_greater_than:'.$param,
            ]
        );

        $this->assertTrue($validate->success());
    }

    public function baseUseProvider(): array
    {
        return [
            [3, 2],
            [1.5, '1.1'],
            [2, '1.5'],
            [3, '1.5'],
            [4, '1.5'],
            [4.5, '1.5'],
            ['b', 'a'],
            ['c', 'a'],
            ['foo', 'bar'],
            [1.1, '1.1'],
            [0, '0'],
            [0, 0],
        ];
    }

    /**
     * @dataProvider badProvider
     *
     * @param mixed $value
     * @param mixed $param
     *
     * @api(
     *     title="未验证通过的数据",
     *     description="
     * 以下是未通过的校验数据示例。
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\EqualGreaterThanTest::class, 'badProvider')]}
     * ```
     *
     * 上面的数据是测试的数据提供者。
     * ",
     *     note="",
     * )
     */
    public function testBad($value, $param): void
    {
        $validate = new Validator(
            [
                'name' => $value,
            ],
            [
                'name'     => 'equal_greater_than:'.$param,
            ]
        );

        $this->assertFalse($validate->success());
    }

    public function badProvider(): array
    {
        return [
            [2, 3],
            [1.1, '1.5'],
            [1.5, '2'],
            [1.5, '3'],
            [1.5, '4'],
            [1.5, '4.5'],
            ['a', 'b'],
            ['a', 'c'],
            ['bar', 'foo'],
            ['0', '0'],
        ];
    }

    /**
     * @api(
     *     title="特殊例子的数据校验",
     *     description="特别注意字符串和数字的严格区分。",
     *     note="",
     * )
     */
    public function testSpecial(): void
    {
        $validate = new Validator();
        $this->assertTrue($validate->equalGreaterThan('0', '0'));
        $this->assertFalse($validate->equalGreaterThan(0, '0'));
        $this->assertFalse($validate->equalGreaterThan('0', 0));
    }

    /**
     * @api(
     *     title="equal_greater_than 参数缺失",
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
                'name'     => 'equal_greater_than',
            ]
        );

        $validate->success();
    }
}
