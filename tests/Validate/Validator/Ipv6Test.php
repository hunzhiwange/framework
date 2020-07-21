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
 *     title="Validator.ipv6",
 *     zh-CN:title="验证器.是否为 ipv6",
 *     path="validate/validator/ipv6",
 *     description="",
 * )
 */
class Ipv6Test extends TestCase
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
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\Ipv6Test::class, 'baseUseProvider')]}
     * ```
     *
     * 上面的数据是测试的数据提供者。
     * ",
     *     note="",
     * )
     */
    public function testBaseUse($value): void
    {
        $validate = new Validator(
            [
                'name' => $value,
            ],
            [
                'name'     => 'ipv6',
            ]
        );

        $this->assertTrue($validate->success());
    }

    public function baseUseProvider(): array
    {
        return [
            ['2001:3CA1:010F:001A:121B:0000:0000:0010'],
            ['2001:0000:0000:001A:0000:0000:0000:0010'],
            ['2001:4860:4860::8888'],
            ['2001:4860:4860::8844'],
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
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\Ipv6Test::class, 'badProvider')]}
     * ```
     *
     * 上面的数据是测试的数据提供者。
     * ",
     *     note="",
     * )
     */
    public function testBad($value): void
    {
        $validate = new Validator(
            [
                'name' => $value,
            ],
            [
                'name'     => 'ipv6',
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
