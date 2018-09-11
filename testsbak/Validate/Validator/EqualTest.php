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
 * Equal test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.09
 *
 * @version 1.0
 */
class EqualTest extends TestCase
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
                'name' => $value,
            ],
            [
                'name'     => 'equal:'.$parameter,
            ]
        );

        $this->assertTrue($validate->success());
    }

    public function baseUseProvider()
    {
        return [
            [(string) (3), 3],
            ['1.5', '1.5'],
            ['1', true],
            ['', false],
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
                'name'     => 'equal:'.$parameter,
            ]
        );

        $this->assertFalse($validate->success());
    }

    public function badProvider()
    {
        return [
            [2, 3],
            ['1.1', '1.5'],
            ['1.5', '2'],
            ['1.5', '3'],
            ['1.5', '4'],
            ['1.5', '4.5'],
            ['a', 'b'],
            ['a', 'c'],
            ['bar', 'foo'],
            [1, '1'],
            [23, '23'],
        ];
    }
}
