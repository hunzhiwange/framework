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

namespace Tests\Router;

use Leevel\Http\IRequest;
use Leevel\Http\RedirectResponse;
use Leevel\Router\IUrl;
use Leevel\Router\Redirect;
use Leevel\Router\Url;
use Leevel\Session\ISession;
use Tests\TestCase;

/**
 * redirect test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.09.10
 *
 * @version 1.0
 */
class RedirectTest extends TestCase
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
        $this->assertSame('http://www.queryphp.com/foo/bar', $response->getTargetUrl());
        $this->assertNull($response->getSession());

        $content = <<<'eot'
            <!DOCTYPE html>
            <html>
                <head>
                    <meta charset="UTF-8" />
                    <meta http-equiv="refresh" content="0;url=http://www.queryphp.com/foo/bar" />
                    <title>Redirecting to http://www.queryphp.com/foo/bar</title>
                </head>
                <body>
                    Redirecting to <a href="http://www.queryphp.com/foo/bar">http://www.queryphp.com/foo/bar</a>.
                </body>
            </html>
            eot;

        $this->assertSame($content, $response->getContent());
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
        $this->assertSame('/foo/bar', $response->getTargetUrl());
        $this->assertNull($response->getSession());

        $content = <<<'eot'
            <!DOCTYPE html>
            <html>
                <head>
                    <meta charset="UTF-8" />
                    <meta http-equiv="refresh" content="0;url=/foo/bar" />
                    <title>Redirecting to /foo/bar</title>
                </head>
                <body>
                    Redirecting to <a href="/foo/bar">/foo/bar</a>.
                </body>
            </html>
            eot;

        $this->assertSame($content, $response->getContent());
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
        $this->assertSame('/foo/bar', $response->getTargetUrl());
        $this->assertSame($session, $response->getSession());
    }

    protected function makeRequest(bool $isSecure = false): IRequest
    {
        $request = $this->createMock(IRequest::class);

        $request->method('getEnter')->willReturn('');
        $this->assertSame('', $request->getEnter());

        $request->method('isSecure')->willReturn($isSecure);
        $this->assertSame($isSecure, $request->isSecure($isSecure));

        return $request;
    }
}
