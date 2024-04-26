<?php

declare(strict_types=1);

namespace Tests\Server;

use Leevel\Http\RedirectResponse;
use Leevel\Http\Response;
use Leevel\Kernel\Utils\Api;
use Leevel\Server\TransformResponse;
use Swoole\Http\Response as SwooleHttpResponse;
use Tests\Server\Fixtures\SwooleHttpResponseDemo;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => '转换响应对象',
    'path' => 'server/transformresponse',
    'zh-CN:description' => <<<'EOT'
Leevel 响应转 Swoole 响应后，然后传递给 Swoole 完成响应给用户。
EOT,
])]
class TransformResponseTest extends TestCase
{
    #[\Override]
    protected function setUp(): void
    {
        if (!\extension_loaded('swoole')) {
            static::markTestSkipped('Swoole extension must be loaded before use.');
        }

        if (isset($GLOBALS['swoole.response'])) {
            unset($GLOBALS['swoole.response']);
        }
    }

    #[\Override]
    protected function tearDown(): void
    {
        if (isset($GLOBALS['swoole.response'])) {
            unset($GLOBALS['swoole.response']);
        }
    }

    #[Api([
        'zh-CN:title' => '转换 Leevel 响应的 header',
    ])]
    public function testResponseWithHeader(): void
    {
        $leevel2Swoole = new TransformResponse();
        $swooleResponse = $this->createMock(SwooleHttpResponse::class);
        $response = new Response('hello');
        $response->withHeaders([
            'foo' => 'bar',
            'hello' => 'world',
        ]);

        $leevel2Swoole->createResponse($response, $swooleResponse);
        static::assertInstanceOf(SwooleHttpResponse::class, $swooleResponse);
    }

    public function testResponseWithHeaderDemo(): void
    {
        $leevel2Swoole = new TransformResponse();
        $swooleResponse = new SwooleHttpResponseDemo();
        $response = new Response('hello');
        $response->withHeaders([
            'foo' => 'bar',
            'hello' => 'world',
        ]);

        $leevel2Swoole->createResponse($response, $swooleResponse);
        static::assertInstanceOf(SwooleHttpResponse::class, $swooleResponse);

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
        static::assertSame(
            $result,
            $this->varJson($GLOBALS['swoole.response'])
        );
    }

    #[Api([
        'zh-CN:title' => '转换 Leevel 响应的 cookie',
    ])]
    public function testResponseWithCookie(): void
    {
        $leevel2Swoole = new TransformResponse();
        $swooleResponse = $this->createMock(SwooleHttpResponse::class);
        $response = new Response('hello');
        $response->withCookies([
            'foo' => 'bar',
            'hello' => 'world',
        ]);

        $leevel2Swoole->createResponse($response, $swooleResponse);
        static::assertInstanceOf(SwooleHttpResponse::class, $swooleResponse);
    }

    public function testResponseWithCookieDemo(): void
    {
        $leevel2Swoole = new TransformResponse();
        $swooleResponse = new SwooleHttpResponseDemo();
        $response = new Response('hello');
        $response->withCookies([
            'foo' => 'bar',
            'hello' => 'world',
        ]);
        $time = time() + 86400;

        $leevel2Swoole->createResponse($response, $swooleResponse);
        static::assertInstanceOf(SwooleHttpResponse::class, $swooleResponse);

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

        static::assertSame(
            sprintf($result, $time, $time),
            $this->varJson($GLOBALS['swoole.response'])
        );
        static::assertSame(['Cache-Control', 'no-cache, private'], $headers[0]);
        static::assertSame('Date', $headers[1][0]);
        static::assertSame('Set-Cookie', $headers[2][0]);
        static::assertStringContainsString('foo=bar;', $headers[2][1]);
        static::assertSame('Set-Cookie', $headers[3][0]);
        static::assertStringContainsString('hello=world;', $headers[3][1]);
    }

    #[Api([
        'zh-CN:title' => '转换 Leevel 跳转响应',
    ])]
    public function testRedirectResponse(): void
    {
        $leevel2Swoole = new TransformResponse();
        $swooleResponse = $this->createMock(SwooleHttpResponse::class);
        $response = new RedirectResponse('https://queryphp.com');

        $leevel2Swoole->createResponse($response, $swooleResponse);
        static::assertInstanceOf(SwooleHttpResponse::class, $swooleResponse);
    }

    public function testRedirectResponseDemo(): void
    {
        $leevel2Swoole = new TransformResponse();
        $swooleResponse = new SwooleHttpResponseDemo();
        $response = new RedirectResponse('https://queryphp.com');

        $leevel2Swoole->createResponse($response, $swooleResponse);
        static::assertInstanceOf(SwooleHttpResponse::class, $swooleResponse);

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
        static::assertSame(
            $result,
            $this->varJson($GLOBALS['swoole.response'])
        );
    }

    protected function getFilterHeaders(array $headers): array
    {
        foreach ($headers as $i => $v) {
            if (\in_array('Cache-Control', $v, true) || \in_array('Date', $v, true)) {
                unset($headers[$i]);
            }
        }

        return array_values($headers);
    }
}
