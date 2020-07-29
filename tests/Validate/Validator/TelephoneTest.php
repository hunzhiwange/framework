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
 *     zh-CN:title="Validator.telephone",
 *     zh-CN:title="验证器.值是否为电话号码",
 *     path="validate/validator/telephone",
 *     zh-CN:description="",
 * )
 */
class TelephoneTest extends TestCase
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
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\TelephoneTest::class, 'baseUseProvider')]}
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
                'name'     => 'telephone',
            ]
        );

        $this->assertTrue($validate->success());
    }

    public function baseUseProvider(): array
    {
        return [
            ['028-8301444'],
            ['0818-8301111'],
            ['0818-83011113'],
            ['08188301111'],
            ['081883011113'],
            ['0818-830111355'],
            ['1733332444'],
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
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\TelephoneTest::class, 'badProvider')]}
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
                'name'     => 'telephone',
            ]
        );

        $this->assertFalse($validate->success());
    }

    public function badProvider(): array
    {
        return [
            ['130222000333311'],
            ['1533333333332222'],
            ['181222100003333'],
            ['143311222333444'],
            ['17333322222444'],
            [' '],
            [new stdClass()],
            [['foo', 'bar']],
            [[1, 2]],
            [true],
            [[[], []]],
            ['02228-8301444'],
            ['08128-8301111'],
            ['173111223332444'],
        ];
    }
}
