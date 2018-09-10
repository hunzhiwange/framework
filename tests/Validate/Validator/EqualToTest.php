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
 * equalTo test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.11
 *
 * @version 1.0
 */
class EqualToTest extends TestCase
{
    /**
     * @dataProvider baseUseProvider
     *
     * @param mixed  $value
     * @param mixed  $valueCompare
     * @param string $parameter
     */
    public function testBaseUse($value, $valueCompare, string $parameter)
    {
        $validate = new Validate(
            [
                'name'  => $value,
                'name2' => $valueCompare,
                'name3' => 'test',
            ],
            [
                'name'     => 'equal_to:'.$parameter,
            ]
        );

        $this->assertTrue($validate->success());
    }

    public function baseUseProvider()
    {
        return [
            ['foo', 'foo', 'name2'],
            ['bar', 'bar', 'name2'],
            ['test', '', 'name3'],
        ];
    }

    /**
     * @dataProvider badProvider
     *
     * @param mixed  $value
     * @param mixed  $valueCompare
     * @param string $parameter
     */
    public function testBad($value, $valueCompare, string $parameter)
    {
        $validate = new Validate(
            [
                'name'  => $value,
                'name2' => $valueCompare,
                'name3' => new stdClass(),
            ],
            [
                'name'     => 'equal_to:'.$parameter,
            ]
        );

        $this->assertFalse($validate->success());
    }

    public function badProvider()
    {
        return [
            ['foo', 'foo2', 'name2'],
            ['bar', 'bar2', 'name2'],
            [new stdClass(), new stdClass(), 'name2'], // 非全等
            [new stdClass(), '', 'name3'], // 非全等
            [['foo', 'bar'], '', 'name3'],
        ];
    }
}
