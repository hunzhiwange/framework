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
 * ip test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.09
 *
 * @version 1.0
 */
class IpTest extends TestCase
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
                'name'     => 'ip',
            ]
        );

        $this->assertTrue($validate->success());
    }

    public function baseUseProvider()
    {
        return [
            ['2001:3CA1:010F:001A:121B:0000:0000:0010'],
            ['2001:0000:0000:001A:0000:0000:0000:0010'],
            ['8.8.8.8'],
            ['8.8.4.4'],
            ['127.0.0.1'],
            ['2001:4860:4860::8888'],
            ['2001:4860:4860::8844'],
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
                'name'     => 'ip',
            ]
        );

        $this->assertFalse($validate->success());
    }

    public function badProvider()
    {
        return [
            ['2022222201:3CA1:010F:001A:121B:0000:0000:0010'],
            ['2001:000000:0000:001A:0000:0000:0000:0010'],
            ['8.8999.8.8'],
            ['8.2228.4.4'],
            [' 127.0.0.1 '],
            ['20222222201:4860:4860::8888'],
            ['9999999:4860:4860::8844'],
            [false],
            [1.1],
            ['0.0'],
            ['-0.0'],
        ];
    }
}
