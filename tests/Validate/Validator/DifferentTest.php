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
 * different test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.11
 *
 * @version 1.0
 *
 * @api(
 *     title="Validator.different",
 *     zh-CN:title="验证器.两个字段是否不同",
 *     path="component/validate/validator/different",
 *     description="",
 * )
 */
class DifferentTest extends TestCase
{
    /**
     * @dataProvider baseUseProvider
     *
     * @param mixed  $value
     * @param mixed  $valueCompare
     * @param string $param
     *
     * @api(
     *     title="验证通过的数据",
     *     description="
     * 以下是通过的校验数据示例。
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\DifferentTest::class, 'baseUseProvider')]}
     * ```
     *
     * 上面的数据是测试的数据提供者。
     * ",
     *     note="",
     * )
     */
    public function testBaseUse($value, $valueCompare, string $param): void
    {
        $validate = new Validator(
            [
                'name'  => $value,
                'name2' => $valueCompare,
                'name3' => new stdClass(),
            ],
            [
                'name'     => 'different:'.$param,
            ]
        );

        $this->assertTrue($validate->success());
    }

    public function baseUseProvider(): array
    {
        return [
            ['foo', 'foo2', 'name2'],
            ['bar', 'bar2', 'name2'],
            [new stdClass(), new stdClass(), 'name2'], // 非全等
            [new stdClass(), '', 'name3'], // 非全等
            [['foo', 'bar'], '', 'name3'],
        ];
    }

    /**
     * @dataProvider badProvider
     *
     * @param mixed  $value
     * @param mixed  $valueCompare
     * @param string $param
     *
     * @api(
     *     title="未验证通过的数据",
     *     description="
     * 以下是未通过的校验数据示例。
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\DifferentTest::class, 'badProvider')]}
     * ```
     *
     * 上面的数据是测试的数据提供者。
     * ",
     *     note="",
     * )
     */
    public function testBad($value, $valueCompare, string $param): void
    {
        $validate = new Validator(
            [
                'name'  => $value,
                'name2' => $valueCompare,
                'name3' => 'test',
            ],
            [
                'name'     => 'different:'.$param,
            ]
        );

        $this->assertFalse($validate->success());
    }

    public function badProvider(): array
    {
        return [
            ['foo', 'foo', 'name2'],
            ['bar', 'bar', 'name2'],
            ['test', '', 'name3'],
        ];
    }

    /**
     * @api(
     *     title="different 参数缺失",
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
                'name'     => 'different',
            ]
        );

        $validate->success();
    }
}
