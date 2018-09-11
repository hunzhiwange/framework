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
 * url test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.10
 *
 * @version 1.0
 */
class UrlTest extends TestCase
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
                'name'     => 'url',
            ]
        );

        $this->assertTrue($validate->success());
    }

    public function baseUseProvider()
    {
        // http://php.net/manual/en/filter.filters.validate.php#110411
        return [
            ['http://www.google.com'],
            ['http://queryphp.com'],
            ['http://baidu.com'],
            ['ftp://ftp.is.co.za.example.org/rfc/rfc1808.txt'],
            ['gopher://spinaltap.micro.umn.example.edu/00/Weather/California/Los%20Angeles'],
            ['http://www.math.uio.no.example.net/faq/compression-faq/part1.html'],
            ['mailto:mduerst@ifi.unizh.example.gov'],
            ['news:comp.infosystems.www.servers.unix'],
            ['telnet://melvyl.ucop.example.edu/'],
            ['http://www.ietf.org/rfc/rfc2396.txt'],
            ['ldap://[2001:db8::7]/c=GB?objectClass?one'],
            ['mailto:John.Doe@example.com'],
            ['news:comp.infosystems.www.servers.unix'],
            ['telnet://192.0.2.16:80/'],
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
                'name'     => 'url',
            ]
        );

        $this->assertFalse($validate->success());
    }

    public function badProvider()
    {
        return [
            ['not numeric'],
            [[]],
            [new stdClass()],
            [['foo', 'bar']],
            [[1, 2]],
            ['tel:+1-816-555-1212'],
            ['foo'],
            ['bar'],
            ['urn:oasis:names:specification:docbook:dtd:xml:4.1.2'],
            ['world'],
            [null],
        ];
    }
}
