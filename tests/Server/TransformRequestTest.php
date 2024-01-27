<?php

declare(strict_types=1);

namespace Tests\Server;

use Leevel\Http\Request;
use Leevel\Server\TransformRequest;
use Swoole\Http\Request as SwooleHttpRequest;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="转换请求对象",
 *     path="server/transformrequest",
 *     zh-CN:description="Swoole 请求转换 Leevel 的请求后，然后传递给 Kernel 完成请求到响应的过程。",
 * )
 */
class TransformRequestTest extends TestCase
{
    protected function setUp(): void
    {
        if (!\extension_loaded('swoole')) {
            static::markTestSkipped('Swoole extension must be loaded before use.');
        }
    }

    /**
     * @api(
     *     zh-CN:title="转换 Swoole 请求的 header",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testSwooleRequestWithHeader(): void
    {
        $swoole2Leevel = new TransformRequest();
        $wooleRequest = new SwooleHttpRequest();
        $wooleRequest->header = [
            'HOST' => '127.0.0.1',
            'REFERER' => 'https://www.queryphp.com',
            'foo' => 'bar',
        ];
        $request = $swoole2Leevel->createRequest($wooleRequest);
        static::assertInstanceOf(Request::class, $request);

        $data = <<<'eot'
            {
                "HTTP_HOST": "127.0.0.1",
                "HTTP_REFERER": "https:\/\/www.queryphp.com",
                "HTTP_FOO": "bar"
            }
            eot;

        static::assertSame(
            $data,
            $this->varJson(
                $request->server->all()
            )
        );

        $data = <<<'eot'
            {
                "host": [
                    "127.0.0.1"
                ],
                "referer": [
                    "https:\/\/www.queryphp.com"
                ],
                "foo": [
                    "bar"
                ]
            }
            eot;

        static::assertSame(
            $data,
            $this->varJson(
                $this->getFilterHeaders($request->headers->all()),
                1
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="转换 Swoole 请求的 server",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testSwooleRequestWithServer(): void
    {
        $swoole2Leevel = new TransformRequest();
        $wooleRequest = new SwooleHttpRequest();
        $wooleRequest->header = [
            'HOST' => '127.0.0.1',
            'REFERER' => 'https://www.queryphp.com',
            'foo' => 'bar',
        ];
        $wooleRequest->server = [
            'SERVER_ADDR' => '127.0.0.1',
            'SERVER_NAME' => 'localhost',
            'SERVER_SOFTWARE' => 'Apache/2.2.22 (Win64) PHP/7.4.0',
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'REQUEST_METHOD' => 'GET',
        ];
        $request = $swoole2Leevel->createRequest($wooleRequest);
        static::assertInstanceOf(Request::class, $request);

        $data = <<<'eot'
            {
                "HTTP_HOST": "127.0.0.1",
                "HTTP_REFERER": "https:\/\/www.queryphp.com",
                "HTTP_FOO": "bar",
                "SERVER_ADDR": "127.0.0.1",
                "SERVER_NAME": "localhost",
                "SERVER_SOFTWARE": "Apache\/2.2.22 (Win64) PHP\/7.4.0",
                "SERVER_PROTOCOL": "HTTP\/1.1",
                "REQUEST_METHOD": "GET"
            }
            eot;

        static::assertSame(
            $data,
            $this->varJson(
                $request->server->all()
            )
        );

        $data = <<<'eot'
            {
                "host": [
                    "127.0.0.1"
                ],
                "referer": [
                    "https:\/\/www.queryphp.com"
                ],
                "foo": [
                    "bar"
                ]
            }
            eot;

        static::assertSame(
            $data,
            $this->varJson(
                $this->getFilterHeaders($request->headers->all()),
                1
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="转换 Swoole 请求的其它属性",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testSwooleRequestWithOther(): void
    {
        $swoole2Leevel = new TransformRequest();
        $wooleRequest = new SwooleHttpRequest();
        $wooleRequest->get = [
            'foo' => 'bar',
        ];
        $wooleRequest->post = [
            'hello' => 'world',
        ];
        $request = $swoole2Leevel->createRequest($wooleRequest);
        static::assertInstanceOf(Request::class, $request);

        $data = <<<'eot'
            {
                "foo": "bar"
            }
            eot;

        static::assertSame(
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

        static::assertSame(
            $data,
            $this->varJson(
                $request->request->all(),
                1
            )
        );
    }

    protected function getFilterHeaders(array $headers): array
    {
        if (isset($headers['date'])) {
            unset($headers['date']);
        }

        if (isset($headers['cache-control'])) {
            unset($headers['cache-control']);
        }

        return $headers;
    }
}
