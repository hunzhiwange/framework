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
 * maxLength test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.11
 *
 * @version 1.0
 */
class MaxLengthTest extends TestCase
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
                'name'     => 'max_length:'.$parameter,
            ]
        );

        $this->assertTrue($validate->success());
    }

    public function baseUseProvider()
    {
        return [
            [2, 1],
            ['中国', 2],
            ['中国', 3],
            ['成都', 2],
            ['hello', 5],
            ['hello', 6],
            ['foo', 3],
            ['world', 5],
            ['中国no1', 5],
            [true, 1],
            [false, 0],
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
                'name'     => 'max_length:'.$parameter,
            ]
        );

        $this->assertFalse($validate->success());
    }

    public function badProvider()
    {
        return [
            [2, 0],
            ['中国', 1],
            ['成都', 1],
            ['hello', 4],
            ['foo', 2],
            ['world', 4],
            ['中国no1', 4],
            [new stdClass(), 0],
            [['foo', 'bar'], 0],
            [[1, 2], 0],
            [1, 0],
            [[[], []], 0],
            [true, 0],
        ];
    }
}
