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
 * regex test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.11
 *
 * @version 1.0
 */
class RegexTest extends TestCase
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
                'name'     => 'regex:'.$parameter,
            ]
        );

        $this->assertTrue($validate->success());
    }

    public function baseUseProvider()
    {
        return [
            [2, '/^[0-9]+$/'],
            ['2', '/^[0-9]+$/'],
            ['zB99', '/^[A-Za-z0-9]+$/'],
            ['ABC', '/^[A-Z]+$/'],
            ['abc', '/^[a-z]+$/'],
            ['中国', '/^[\x{4e00}-\x{9fa5}]+$/u'],
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
                'name'     => 'regex:'.$parameter,
            ]
        );

        $this->assertFalse($validate->success());
    }

    public function badProvider()
    {
        return [
            ['中国', '/^[0-9]+$/'],
            ['成都', '/^[0-9]+$/'],
            [new stdClass(), 0],
            [['foo', 'bar'], 0],
            [[1, 2], 0],
            [[[], []], 0],
        ];
    }
}
