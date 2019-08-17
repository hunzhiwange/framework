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

namespace Tests\Protocol;

use Leevel\Http\IRequest;
use Leevel\Http\Request;
use Leevel\Protocol\Swoole2Leevel;
use Swoole\Http\Request as SwooleHttpRequest;
use Tests\TestCase;

/**
 * Swoole 请求转 Leevel 请求测试.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.08.11
 *
 * @version 1.0
 *
 * @api(
 *     title="Swoole 请求转 Leevel 请求",
 *     path="protocol/swoole2Leevel",
 *     description="Swoole 请求转换 Leevel 的请求后，然后传递给 Kernel 完成请求到响应的过程。",
 * )
 */
class Swoole2LeevelTest extends TestCase
{
    /**
     * @api(
     *     title="转换 Swoole 请求的 header",
     *     description="",
     *     note="",
     * )
     */
    public function testSwooleRequestWithHeader(): void
    {
        $swoole2Leevel = new Swoole2Leevel();
        $wooleRequest = new SwooleHttpRequest();
        $wooleRequest->header = [
            'HOST'    => '127.0.0.1',
            'REFERER' => 'https://www.queryphp.com',
            'foo'     => 'bar',
        ];
        $request = $swoole2Leevel->createRequest($wooleRequest);
        $this->assertInstanceOf(IRequest::class, $request);
        $this->assertInstanceOf(Request::class, $request);

        $data = <<<'eot'
            {
                "HTTP_HOST": "127.0.0.1",
                "HTTP_REFERER": "https:\/\/www.queryphp.com",
                "HTTP_FOO": "bar"
            }
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $request->server->all()
            )
        );

        $data = <<<'eot'
            {
                "host": "127.0.0.1",
                "referer": "https:\/\/www.queryphp.com",
                "foo": "bar"
            }
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $request->headers->all(),
                1
            )
        );
    }

    /**
     * @api(
     *     title="转换 Swoole 请求的 server",
     *     description="",
     *     note="",
     * )
     */
    public function testSwooleRequestWithServer(): void
    {
        $swoole2Leevel = new Swoole2Leevel();
        $wooleRequest = new SwooleHttpRequest();
        $wooleRequest->header = [
            'HOST'    => '127.0.0.1',
            'REFERER' => 'https://www.queryphp.com',
            'foo'     => 'bar',
        ];
        $wooleRequest->server = [
            'SERVER_ADDR'     => '127.0.0.1',
            'SERVER_NAME'     => 'localhost',
            'SERVER_SOFTWARE' => 'Apache/2.2.22 (Win64) PHP/7.3.2',
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'REQUEST_METHOD'  => 'GET',
        ];
        $request = $swoole2Leevel->createRequest($wooleRequest);
        $this->assertInstanceOf(IRequest::class, $request);
        $this->assertInstanceOf(Request::class, $request);

        $data = <<<'eot'
            {
                "HTTP_HOST": "127.0.0.1",
                "HTTP_REFERER": "https:\/\/www.queryphp.com",
                "HTTP_FOO": "bar",
                "SERVER_ADDR": "127.0.0.1",
                "SERVER_NAME": "localhost",
                "SERVER_SOFTWARE": "Apache\/2.2.22 (Win64) PHP\/7.3.2",
                "SERVER_PROTOCOL": "HTTP\/1.1",
                "REQUEST_METHOD": "GET"
            }
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $request->server->all()
            )
        );

        $data = <<<'eot'
            {
                "host": "127.0.0.1",
                "referer": "https:\/\/www.queryphp.com",
                "foo": "bar"
            }
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $request->headers->all(),
                1
            )
        );
    }

    /**
     * @api(
     *     title="转换 Swoole 请求的其它属性",
     *     description="",
     *     note="",
     * )
     */
    public function testSwooleRequestWithOther(): void
    {
        $swoole2Leevel = new Swoole2Leevel();
        $wooleRequest = new SwooleHttpRequest();
        $wooleRequest->get = [
            'foo' => 'bar',
        ];
        $wooleRequest->post = [
            'hello' => 'world',
        ];
        $request = $swoole2Leevel->createRequest($wooleRequest);
        $this->assertInstanceOf(IRequest::class, $request);
        $this->assertInstanceOf(Request::class, $request);

        $data = <<<'eot'
            {
                "foo": "bar"
            }
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $request->query->all()
            )
        );

        $data = <<<'eot'
            {
                "hello": "world"
            }
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $request->request->all(),
                1
            )
        );
    }
}
