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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Validate\Validator;

use Leevel\Validate\Validator;
use stdClass;
use Tests\TestCase;

/**
 * denyIp test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.11
 *
 * @version 1.0
 */
class DenyIpTest extends TestCase
{
    /**
     * @dataProvider baseUseProvider
     *
     * @param mixed  $value
     * @param string $param
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
     * @param mixed  $value
     * @param string $param
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
