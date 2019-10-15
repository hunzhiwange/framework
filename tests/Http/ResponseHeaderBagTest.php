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

namespace Tests\Http;

use Leevel\Http\ResponseHeaderBag;
use Leevel\Support\Arr;
use Tests\TestCase;

/**
 * ResponseHeaderBag test.
 *
 * - This class borrows heavily from the Symfony4 Framework and is part of the symfony package.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.03.14
 *
 * @version 1.0
 *
 * @see Symfony\Component\HttpFoundation (https://github.com/symfony/symfony)
 */
class ResponseHeaderBagTest extends TestCase
{
    public function testAll(): void
    {
        $headers = [
            'fOo'              => 'BAR',
            'ETag'             => 'xyzzy',
            'Content-MD5'      => 'Q2hlY2sgSW50ZWdyaXR5IQ==',
            'P3P'              => 'CP="CAO PSA OUR"',
            'WWW-Authenticate' => 'Basic realm="WallyWorld"',
            'X-UA-Compatible'  => 'IE=edge,chrome=1',
            'X-XSS-Protection' => '1; mode=block',
        ];

        $bag = new ResponseHeaderBag($headers);

        $all = $bag->all();

        foreach (array_keys($headers) as $headerName) {
            $this->assertArrayHasKey(strtolower($headerName), $all, '->all() gets all input keys in strtolower case');
        }
    }

    public function testCookie(): void
    {
        $headers = [
            'foo'              => 'bar',
        ];
        $bag = new ResponseHeaderBag($headers);
        $bag->cookie('hello', 'world');
        $cookie = $bag->getCookies();
        foreach ($cookie as &$v) {
            $v = Arr::except($v, [2]);
        }

        $data = <<<'eot'
            {
                "hello": {
                    "0": "hello",
                    "1": "world",
                    "3": "\/",
                    "4": "",
                    "5": false,
                    "6": false
                }
            }
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $cookie
            )
        );
    }

    public function testSetCookie(): void
    {
        $headers = [
            'foo'              => 'bar',
        ];
        $bag = new ResponseHeaderBag($headers);
        $bag->setCookie('hello', 'world');
        $cookie = $bag->getCookies();
        foreach ($cookie as &$v) {
            $v = Arr::except($v, [2]);
        }

        $data = <<<'eot'
            {
                "hello": {
                    "0": "hello",
                    "1": "world",
                    "3": "\/",
                    "4": "",
                    "5": false,
                    "6": false
                }
            }
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $cookie
            )
        );
    }

    public function testWithCookies(): void
    {
        $headers = [
            'foo'              => 'bar',
        ];
        $bag = new ResponseHeaderBag($headers);
        $bag->withCookies(['hello' => 'world', 'foo' => 'bar']);
        $cookie = $bag->getCookies();
        foreach ($cookie as &$v) {
            $v = Arr::except($v, [2]);
        }

        $data = <<<'eot'
            {
                "hello": {
                    "0": "hello",
                    "1": "world",
                    "3": "\/",
                    "4": "",
                    "5": false,
                    "6": false
                },
                "foo": {
                    "0": "foo",
                    "1": "bar",
                    "3": "\/",
                    "4": "",
                    "5": false,
                    "6": false
                }
            }
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $cookie
            )
        );
    }

    public function testCall(): void
    {
        $headers = [
            'foo'              => 'bar',
        ];
        $bag = new ResponseHeaderBag($headers);
        $bag->withCookies(['hello' => 'world', 'foo' => 'bar']);
        $bag->delete('hello');
        $cookie = $bag->getCookies();
        foreach ($cookie as &$v) {
            $v = Arr::except($v, [2]);
        }

        $data = <<<'eot'
            {
                "hello": {
                    "0": "hello",
                    "1": null,
                    "3": "\/",
                    "4": "",
                    "5": false,
                    "6": false
                },
                "foo": {
                    "0": "foo",
                    "1": "bar",
                    "3": "\/",
                    "4": "",
                    "5": false,
                    "6": false
                }
            }
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $cookie
            )
        );
    }
}
