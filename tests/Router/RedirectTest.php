<?php

declare(strict_types=1);

namespace Tests\Router;

use Leevel\Http\RedirectResponse;
use Leevel\Http\Request;
use Leevel\Router\IUrl;
use Leevel\Router\Redirect;
use Leevel\Router\Url;
use Leevel\Session\ISession;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class RedirectTest extends TestCase
{
    public function testBaseUse(): void
    {
        $request = $this->makeRequest();
        $url = new Url($request, [
            'domain' => 'queryphp.com',
        ]);
        $redirect = new Redirect($url);

        $this->assertInstanceof(IUrl::class, $redirect->getUrl());
        $this->assertInstanceof(Url::class, $redirect->getUrl());

        $this->assertInstanceof(RedirectResponse::class, $response = $redirect->url('foo/bar'));
        static::assertSame('http://www.queryphp.com/foo/bar', $response->getTargetUrl());
        static::assertNull($response->getSession());

        $content = <<<'eot'
            <!DOCTYPE html>
            <html>
                <head>
                    <meta charset="UTF-8" />
                    <meta http-equiv="refresh" content="0;url='http://www.queryphp.com/foo/bar'" />

                    <title>Redirecting to http://www.queryphp.com/foo/bar</title>
                </head>
                <body>
                    Redirecting to <a href="http://www.queryphp.com/foo/bar">http://www.queryphp.com/foo/bar</a>.
                </body>
            </html>
            eot;

        static::assertSame($content, $response->getContent());
    }

    public function testRaw(): void
    {
        $request = $this->makeRequest();
        $url = new Url($request, [
            'domain' => 'queryphp.com',
        ]);
        $redirect = new Redirect($url);

        $this->assertInstanceof(IUrl::class, $redirect->getUrl());
        $this->assertInstanceof(Url::class, $redirect->getUrl());

        $this->assertInstanceof(RedirectResponse::class, $response = $redirect->raw('/foo/bar'));
        static::assertSame('/foo/bar', $response->getTargetUrl());
        static::assertNull($response->getSession());

        $content = <<<'eot'
            <!DOCTYPE html>
            <html>
                <head>
                    <meta charset="UTF-8" />
                    <meta http-equiv="refresh" content="0;url='/foo/bar'" />

                    <title>Redirecting to /foo/bar</title>
                </head>
                <body>
                    Redirecting to <a href="/foo/bar">/foo/bar</a>.
                </body>
            </html>
            eot;

        static::assertSame($content, $response->getContent());
    }

    public function testSetSession(): void
    {
        $request = $this->makeRequest();
        $url = new Url($request, [
            'domain' => 'queryphp.com',
        ]);
        $redirect = new Redirect($url);

        $session = $this->createMock(ISession::class);

        $redirect->setSession($session);

        $this->assertInstanceof(IUrl::class, $redirect->getUrl());
        $this->assertInstanceof(Url::class, $redirect->getUrl());

        $this->assertInstanceof(RedirectResponse::class, $response = $redirect->raw('/foo/bar'));
        static::assertSame('/foo/bar', $response->getTargetUrl());
        static::assertSame($session, $response->getSession());
    }

    protected function makeRequest(bool $isSecure = false): Request
    {
        $request = $this->createMock(Request::class);

        $request->method('getEnter')->willReturn('');
        static::assertSame('', $request->getEnter());

        $request->method('isSecure')->willReturn($isSecure);
        static::assertSame($isSecure, $request->isSecure($isSecure));

        return $request;
    }
}
