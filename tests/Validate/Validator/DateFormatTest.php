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
 * dateFormat test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.08
 *
 * @version 1.0
 */
class DateFormatTest extends TestCase
{
    /**
     * @dataProvider baseUseProvider
     *
     * @param mixed  $value
     * @param string $format
     */
    public function testBaseUse($value, string $format)
    {
        $validate = new Validate(
            [
                'name' => $value,
            ],
            [
                'name'     => 'date_format:'.$format,
            ]
        );

        $this->assertTrue($validate->success());
    }

    public function baseUseProvider()
    {
        return [
            ['6.1.2018 13:00+01:00', 'j.n.Y H:iP'],
            ['15-Mar-2018', 'j-M-Y'],
        ];
    }

    /**
     * @dataProvider badProvider
     *
     * @param mixed  $value
     * @param string $format
     */
    public function testBad($value, string $format)
    {
        $validate = new Validate(
            [
                'name' => $value,
            ],
            [
                'name'     => 'date_format:'.$format,
            ]
        );

        $this->assertFalse($validate->success());
    }

    public function badProvider()
    {
        return [
            ['2018.6.1 13:00+01:00', 'j.n.Y H:iP'],
            ['29/Feb/23:2018:59:31', 'd/M/Y:H:i:s'],
        ];
    }
}
