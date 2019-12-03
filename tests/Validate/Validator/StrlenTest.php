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
 * strlen test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.10
 *
 * @version 1.0
 *
 * @api(
 *     title="Validator.strlen",
 *     zh-CN:title="验证器.长度验证",
 *     path="component/validate/validator/strlen",
 *     description="",
 * )
 */
class StrlenTest extends TestCase
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
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\StrlenTest::class, 'baseUseProvider')]}
     * ```
     *
     * 上面的数据是测试的数据提供者。
     * ",
     *     note="",
     * )
     */
    public function testBaseUse($value, int $length): void
    {
        $validate = new Validator(
            [
                'name' => $value,
            ],
            [
                'name'     => 'strlen:'.$length,
            ]
        );

        $this->assertTrue($validate->success());
    }

    public function baseUseProvider(): array
    {
        return [
            ['http://www.google.com', 21],
            ['http://queryphp.com', 19],
            ['foobar', 6],
            ['helloworld', 10],
            ['中国', 6],
            ['成都no1', 9],
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
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\StrlenTest::class, 'badProvider')]}
     * ```
     *
     * 上面的数据是测试的数据提供者。
     * ",
     *     note="",
     * )
     */
    public function testBad($value, int $length): void
    {
        $validate = new Validator(
            [
                'name' => $value,
            ],
            [
                'name'     => 'strlen:'.$length,
            ]
        );

        $this->assertFalse($validate->success());
    }

    public function badProvider(): array
    {
        return [
            ['not numeric', 21],
            [[], 21],
            [new stdClass(), 21],
            [['foo', 'bar'], 21],
            [[1, 2], 21],
            ['tel:+1-816-555-1212', 21],
            ['foo', 21],
            ['bar', 21],
            ['urn:oasis:names:specification:docbook:dtd:xml:4.1.2', 21],
            ['world', 21],
            [null, 21],
        ];
    }

    /**
     * @api(
     *     title="strlen 参数缺失",
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
                'name'     => 'strlen',
            ]
        );

        $validate->success();
    }
}
