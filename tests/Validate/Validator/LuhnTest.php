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
 * luhn test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.10
 *
 * @version 1.0
 */
class LuhnTest extends TestCase
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
                'name'     => 'luhn',
            ]
        );

        $this->assertTrue($validate->success());
    }

    public function baseUseProvider()
    {
        return [
            [6214830286655765],
            ['6214830286655765'],
            ['6214850285711047'],
            ['6225365271562822'],
            ['181222100003333'],
            ['143311222333444'],
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
                'name'     => 'luhn',
            ]
        );

        $this->assertFalse($validate->success());
    }

    public function badProvider()
    {
        return [
            ['130222000333311'],
            ['1533333333332222'],
            [' '],
            [new stdClass()],
            [['foo', 'bar']],
            [[1, 2]],
            [true],
            [[[], []]],
            ['173111223332444'],
        ];
    }
}
