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
 * (c) 2010-2020 http://queryphp.com All rights reserved.
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
 * @api(
 *     zh-CN:title="Leevel 响应转 Swoole 响应",
 *     path="protocol/leevel2swoole",
 *     zh-CN:description="Leevel 响应转 Swoole 响应后，然后传递给 Swoole 完成响应给用户。",
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
     *     zh-CN:title="转换 Leevel 响应的 header",
     *     zh-CN:description="",
     *     zh-CN:note="",
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
                        "Foo",
                        "bar"
                    ],
                    [
                        "Hello",
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

        $GLOBALS['swoole.response']['header'] = $this->getFilterHeaders($GLOBALS['swoole.response']['header']);
        $this->assertSame(
            $result,
            $this->varJson($GLOBALS['swoole.response'])
        );
    }

    /**
     * @api(
     *     zh-CN:title="转换 Leevel 响应的 cookie",
     *     zh-CN:description="",
     *     zh-CN:note="",
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
                "status": [
                    200
                ],
                "write": [
                    "hello"
                ]
            }
            eot;

        $headers = $GLOBALS['swoole.response']['header'];
        unset($GLOBALS['swoole.response']['header']);

        $this->assertSame(
            sprintf($result, $time, $time),
            $this->varJson($GLOBALS['swoole.response'])
        );
        $this->assertSame(['Cache-Control', 'no-cache, private'], $headers[0]);
        $this->assertSame('Date', $headers[1][0]);
        $this->assertSame('Set-Cookie', $headers[2][0]);
        $this->assertStringContainsString('foo=bar;', $headers[2][1]);
        $this->assertSame('Set-Cookie', $headers[3][0]);
        $this->assertStringContainsString('hello=world;', $headers[3][1]);
    }

    /**
     * @api(
     *     zh-CN:title="转换 Leevel 跳转响应",
     *     zh-CN:description="",
     *     zh-CN:note="",
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
                "redirect": [
                    "https:\/\/queryphp.com"
                ],
                "header": [
                    [
                        "Location",
                        "https:\/\/queryphp.com"
                    ]
                ],
                "status": [
                    302
                ],
                "write": [
                    "<!DOCTYPE html>\n<html>\n    <head>\n        <meta charset=\"UTF-8\" \/>\n        <meta http-equiv=\"refresh\" content=\"0;url='https:\/\/queryphp.com'\" \/>\n\n        <title>Redirecting to https:\/\/queryphp.com<\/title>\n    <\/head>\n    <body>\n        Redirecting to <a href=\"https:\/\/queryphp.com\">https:\/\/queryphp.com<\/a>.\n    <\/body>\n<\/html>"
                ]
            }
            eot;

        $GLOBALS['swoole.response']['header'] = $this->getFilterHeaders($GLOBALS['swoole.response']['header']);
        $this->assertSame(
            $result,
            $this->varJson($GLOBALS['swoole.response'])
        );
    }

    protected function getFilterHeaders(array $headers): array
    {
        foreach ($headers as $i => $v) {
            if (in_array('Cache-Control', $v, true) || in_array('Date', $v, true)) {
                unset($headers[$i]);
            }
        }

        return array_values($headers);
    }
}
