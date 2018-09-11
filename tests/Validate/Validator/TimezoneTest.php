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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Validate\Validator;

use Leevel\Validate\Validate;
use Tests\TestCase;

/**
 * timezone test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.08
 *
 * @version 1.0
 */
class TimezoneTest extends TestCase
{
    /**
     * @dataProvider baseUseProvider
     *
     * @param mixed $value
     */
    public function testBaseUse($value)
    {
        $validate = new Validate(
            [
                'name' => $value,
            ],
            [
                'name'     => 'timezone',
            ]
        );

        $this->assertTrue($validate->success());
    }

    public function baseUseProvider()
    {
        return [
            ['-0400'],
            ['EDT'],
            ['Asia/Shanghai'], // 上海
            ['Asia/Hong_Kong'], // 香港
            ['Asia/Chongqing'], // 重庆
            ['Asia/Urumqi'], // 乌鲁木齐
            ['Asia/Macao'], // 澳门
            ['Asia/Taipei'], // 台北
            ['Asia/Singapore'], //  新加坡
            ['PRC'], // 设置中国时区
            ['Etc/GMT'], // 格林威治标准时间
            ['Etc/GMT+8'], // 比林威治标准时间慢 8 小时
            ['Etc/GMT-8'], // 比林威治标准时间快 8 小时
        ];
    }

    /**
     * @dataProvider badProvider
     *
     * @param mixed $value
     */
    public function testBad($value)
    {
        $validate = new Validate(
            [
                'name' => $value,
            ],
            [
                'name'     => 'timezone',
            ]
        );

        $this->assertFalse($validate->success());
    }

    public function badProvider()
    {
        return [
            ['Asia/foo'],
            ['Asia/bar'],
            ['foo'],
            ['bar'],
            ['2018'],
            ['2018-44-22'],
            ['2018-12-42'],
            [''],
            [[1, 2]],
        ];
    }
}
