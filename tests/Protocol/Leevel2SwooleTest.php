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

use Leevel\Http\RedirectResponse;
use Leevel\Http\Response;
use Leevel\Protocol\Leevel2Swoole;
use Swoole\Http\Response as SwooleHttpResponse;
use Tests\Protocol\Fixtures\SwooleHttpResponseDemo;
use Tests\TestCase;

/**
 * Leevel 响应转 Swoole 响应测试.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.08.12
 *
 * @version 1.0
 *
 * @api(
 *     title="Leevel 响应转 Swoole 响应",
 *     path="protocol/leevel2swoole",
 *     description="Leevel 响应转 Swoole 响应后，然后传递给 Swoole 完成响应给用户。",
 * )
 */
class Leevel2SwooleTest extends TestCase
{
    protected function setUp(): void
    {
        if (isset($GLOBALS['swoole.response'])) {
            unset($GLOBALS['swoole.response']);
        }
    }

    protected function tearDown(): void
    {
        $this->setUp();
    }

    /**
     * @api(
     *     title="转换 Leevel 响应的 header",
     *     description="",
     *     note="",
     * )
     */
    public function testResponseWithHeader(): void
    {
        $leevel2Swoole = new Leevel2Swoole();
        $swooleResponse = $this->createMock(SwooleHttpResponse::class);
        $response = new Response('hello');
        $response->withHeaders([
            'foo'   => 'bar',
            'hello' => 'world',
        ]);

        $leevel2Swoole->createResponse($response, $swooleResponse);
        $this->assertInstanceOf(SwooleHttpResponse::class, $swooleResponse);
    }

    public function testResponseWithHeaderDemo(): void
    {
        $leevel2Swoole = new Leevel2Swoole();
        $swooleResponse = new SwooleHttpResponseDemo();
        $response = new Response('hello');
        $response->withHeaders([
            'foo'   => 'bar',
            'hello' => 'world',
        ]);

        $leevel2Swoole->createResponse($response, $swooleResponse);
        $this->assertInstanceOf(SwooleHttpResponse::class, $swooleResponse);

        $result = <<<'eot'
            {
                "header": [
                    [
                        "foo",
                        "bar"
                    ],
                    [
                        "hello",
                        "world"
                    ]
                ],
                "status": [
                    200
                ],
                "write": [
                    "hello"
                ]
            }
            eot;

        $this->assertSame(
            $result,
            $this->varJson($GLOBALS['swoole.response'])
        );
    }

    /**
     * @api(
     *     title="转换 Leevel 响应的 cookie",
     *     description="",
     *     note="",
     * )
     */
    public function testResponseWithCookie(): void
    {
        $leevel2Swoole = new Leevel2Swoole();
        $swooleResponse = $this->createMock(SwooleHttpResponse::class);
        $response = new Response('hello');
        $response->withCookies([
            'foo'   => 'bar',
            'hello' => 'world',
        ]);

        $leevel2Swoole->createResponse($response, $swooleResponse);
        $this->assertInstanceOf(SwooleHttpResponse::class, $swooleResponse);
    }

    public function testResponseWithCookieDemo(): void
    {
        $leevel2Swoole = new Leevel2Swoole();
        $swooleResponse = new SwooleHttpResponseDemo();
        $response = new Response('hello');
        $response->withCookies([
            'foo'   => 'bar',
            'hello' => 'world',
        ]);
        $time = time() + 86400;

        $leevel2Swoole->createResponse($response, $swooleResponse);
        $this->assertInstanceOf(SwooleHttpResponse::class, $swooleResponse);

        $result = <<<'eot'
            {
                "cookie": [
                    [
                        "foo",
                        "bar",
                        %d,
                        "\/",
                        "",
                        false,
                        false
                    ],
                    [
                        "hello",
                        "world",
                        %d,
                        "\/",
                        "",
                        false,
                        false
                    ]
                ],
                "status": [
                    200
                ],
                "write": [
                    "hello"
                ]
            }
            eot;

        $this->assertSame(
            sprintf($result, $time, $time),
            $this->varJson($GLOBALS['swoole.response'])
        );
    }

    /**
     * @api(
     *     title="转换 Leevel 跳转响应",
     *     description="",
     *     note="",
     * )
     */
    public function testRedirectResponse(): void
    {
        $leevel2Swoole = new Leevel2Swoole();
        $swooleResponse = $this->createMock(SwooleHttpResponse::class);
        $response = new RedirectResponse('https://queryphp.com');

        $leevel2Swoole->createResponse($response, $swooleResponse);
        $this->assertInstanceOf(SwooleHttpResponse::class, $swooleResponse);
    }

    public function testRedirectResponseDemo(): void
    {
        $leevel2Swoole = new Leevel2Swoole();
        $swooleResponse = new SwooleHttpResponseDemo();
        $response = new RedirectResponse('https://queryphp.com');

        $leevel2Swoole->createResponse($response, $swooleResponse);
        $this->assertInstanceOf(SwooleHttpResponse::class, $swooleResponse);

        $result = <<<'eot'
            {
                "header": [
                    [
                        "location",
                        "https:\/\/queryphp.com"
                    ]
                ],
                "status": [
                    302
                ],
                "write": [
                    "<!DOCTYPE html>\n<html>\n    <head>\n        <meta charset=\"UTF-8\" \/>\n        <meta http-equiv=\"refresh\" content=\"0;url=https:\/\/queryphp.com\" \/>\n        <title>Redirecting to https:\/\/queryphp.com<\/title>\n    <\/head>\n    <body>\n        Redirecting to <a href=\"https:\/\/queryphp.com\">https:\/\/queryphp.com<\/a>.\n    <\/body>\n<\/html>"
                ]
            }
            eot;

        $this->assertSame(
            $result,
            $this->varJson($GLOBALS['swoole.response'])
        );
    }
}
