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
 * equalLessThan test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.09
 *
 * @version 1.0
 */
class EqualLessThanTest extends TestCase
{
    /**
     * @dataProvider baseUseProvider
     *
     * @param mixed $value
     * @param mixed $param
     */
    public function testBaseUse($value, $param)
    {
        $validate = new Validator(
            [
                'name' => $value,
            ],
            [
                'name'     => 'equal_less_than:'.$param,
            ]
        );

        $this->assertTrue($validate->success());
    }

    public function baseUseProvider(): array
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
            ['1.1', '1.1'],
            ['0', '0'],
            ['0', 0],
        ];
    }

    /**
     * @dataProvider badProvider
     *
     * @param mixed $value
     * @param mixed $param
     */
    public function testBad($value, $param)
    {
        $validate = new Validator(
            [
                'name' => $value,
            ],
            [
                'name'     => 'equal_less_than:'.$param,
            ]
        );

        $this->assertFalse($validate->success());
    }

    public function badProvider(): array
    {
        return [
            [3, 2],
            ['1.5', '1.1'],
            ['2', '1.5'],
            ['3', '1.5'],
            ['4', '1.5'],
            ['4.5', '1.5'],
            ['b', 'a'],
            ['c', 'a'],
            ['foo', 'bar'],
            [0, '0'],
        ];
    }

    public function testSpecial(): void
    {
        $validate = new Validator();

        $this->assertTrue($validate->equalLessThan('0', '0'));
        $this->assertFalse($validate->equalLessThan(0, '0'));
        $this->assertFalse($validate->equalLessThan('0', 0));
    }
}
