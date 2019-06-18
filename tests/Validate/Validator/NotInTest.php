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
use Tests\TestCase;

/**
 * notIn test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.09
 *
 * @version 1.0
 */
class NotInTest extends TestCase
{
    /**
     * @dataProvider baseUseProvider
     *
     * @param mixed  $value
     * @param string $param
     */
    public function testBaseUse($value, string $param)
    {
        $validate = new Validator(
            [
                'name' => $value,
            ],
            [
                'name'     => 'not_in:'.$param,
            ]
        );

        $this->assertTrue($validate->success());
    }

    public function baseUseProvider()
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
     * @dataProvider badProvider
     *
     * @param mixed  $value
     * @param string $param
     */
    public function testBad($value, string $param)
    {
        $validate = new Validator(
            [
                'name' => $value,
            ],
            [
                'name'     => 'not_in:'.$param,
            ]
        );

        $this->assertFalse($validate->success());
    }

    public function badProvider()
    {
        return [
            ['1', '1,5'],
            ['2', '1,2,5'],
            ['3', '1,3,5'],
            ['4', '1,4,5'],
            ['4.5', '1,4.5,5'],
            ['b', 'a,b,z'],
            ['c', 'a,c,z'],
            ['1', '1,5'],
            ['5', '1,5'],
            ['a', 'a,z'],
            ['z', 'a,z'],
        ];
    }
}
