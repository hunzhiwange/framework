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
use stdClass;
use Tests\TestCase;

/**
 * after test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.11
 *
 * @version 1.0
 */
class AfterTest extends TestCase
{
    /**
     * @dataProvider baseUseProvider
     *
     * @param mixed  $value
     * @param string $parameter
     */
    public function testBaseUse($value, string $parameter)
    {
        $validate = new Validate(
            [
                'name'  => $value,
                'name2' => '2018-08-10',
            ],
            [
                'name'     => 'after:'.$parameter,
            ]
        );

        $this->assertTrue($validate->success());
    }

    public function baseUseProvider()
    {
        return [
            ['2018-08-15', '2018-08-14'],
            ['2018-08-15', 'name2'],
            ['2018-08-15', '2018-08-14|date_format:Y-m-d'],
        ];
    }

    /**
     * @dataProvider badProvider
     *
     * @param mixed  $value
     * @param string $parameter
     */
    public function testBad($value, string $parameter)
    {
        $validate = new Validate(
            [
                'name' => $value,
            ],
            [
                'name'     => 'after:'.$parameter,
            ]
        );

        $this->assertFalse($validate->success());
    }

    public function badProvider()
    {
        return [
            ['2018-08-14', '2018-08-14'],
            ['2018-08-14', '2018-08-15'],
            ['2018-08-15', 'name2'],
            ['2018-08-15', '2018-08-14|date_format:Y-m'],
            [new stdClass(), '1.1'],
            [[], '2018-08-15'],
            [true, '2018-08-15'],
            [false, '2018-08-15'],
        ];
    }

    public function testMakeDateTimeFormatWithNewDateTimeExceptionError()
    {
        $validate = new Validate(
            [
                'name'  => '2018-08-10',
            ],
            [
                'name'     => 'after:foobar|date_format:y',
            ]
        );

        $this->assertFalse($validate->success());
    }
}
