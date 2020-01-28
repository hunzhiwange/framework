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

namespace Tests\Router;

use Leevel\Http\JsonResponse;
use Leevel\Http\RedirectResponse;
use Leevel\Http\Request;
use Leevel\Router\Redirect;
use Leevel\Router\Response as RouterResponse;
use Leevel\Router\Url;
use Leevel\Router\View;
use Leevel\View\Phpui;
use SplFileInfo;
use SplFileObject;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ResponseTest extends TestCase
{
    public function testBaseUse(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->make('hello');

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);

        $this->assertSame('hello', $response->getContent());
        $this->assertSame(200, $response->getStatusCode());
        $headers = $response->headers->all();
        unset($headers['date']);
        $this->assertSame(['cache-control' => ['no-cache, private']], $headers);
    }

    public function testMake(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->make('foo.bar', 404, ['foo' => 'bar']);

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);

        $this->assertSame('foo.bar', $response->getContent());
        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame(['foo' => ['bar']], $this->getFilterHeaders($response->headers->all()));
    }

    public function testView(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->view('view1', ['foo' => 'bar']);

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);

        $this->assertSame('hello view1 for bar.', $response->getContent());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame([], $this->getFilterHeaders($response->headers->all()));
    }

    public function testViewWithCustomExt(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->view('view1', ['foo' => 'bar new'], '.foo');

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);

        $this->assertSame('hello view1.foo for bar new.', $response->getContent());
        $this->assertSame(200, $response->getStatusCode());
        $headers = $response->headers->all();
        $this->assertSame([], $this->getFilterHeaders($headers));
    }

    public function testViewWithHeaderAndStatus(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->view('view1', ['foo' => 'bar new'], null, 404, ['hello' => 'world']);

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);

        $this->assertSame('hello view1 for bar new.', $response->getContent());
        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame(['hello' => ['world']], $this->getFilterHeaders($response->headers->all()));
    }

    public function testViewSuccess(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->viewSuccess('it is success.');

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);

        $this->assertSame('success! message is it is success.,url is ,time is 1.', $response->getContent());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame([], $this->getFilterHeaders($response->headers->all()));
    }

    public function testViewSuccess2(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->viewSuccess('it is success2.', 'http://queryphp.com', 3);

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);

        $this->assertSame('success! message is it is success2.,url is http://queryphp.com,time is 3.', $response->getContent());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame([], $this->getFilterHeaders($response->headers->all()));
    }

    public function testViewSuccess3(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $factory->setViewSuccessTemplate('success_custom');

        $response = $factory->viewSuccess('it is success3.', 'http://queryphp.com', 3);

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);

        $this->assertSame('success custom! message is it is success3.,url is http://queryphp.com,time is 3.', $response->getContent());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame([], $this->getFilterHeaders($response->headers->all()));
    }

    public function testViewFail(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->viewFail('it is fail.');

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);

        $this->assertSame('fail! message is it is fail.,url is ,time is 3.', $response->getContent());
        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame([], $this->getFilterHeaders($response->headers->all()));
    }

    public function testViewFail2(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->viewFail('it is fail2.', 'http://queryphp.com', 3);

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);

        $this->assertSame('fail! message is it is fail2.,url is http://queryphp.com,time is 3.', $response->getContent());
        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame([], $this->getFilterHeaders($response->headers->all()));
    }

    public function testViewFail3(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $factory->setViewFailTemplate('fail_custom');

        $response = $factory->viewFail('it is fail3.', 'http://queryphp.com', 3);

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);

        $this->assertSame('fail custom! message is it is fail3.,url is http://queryphp.com,time is 3.', $response->getContent());
        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame([], $this->getFilterHeaders($response->headers->all()));
    }

    public function testJson(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->json();

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(JsonResponse::class, $response);

        $this->assertSame('{}', $response->getContent());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(['content-type' => ['application/json']], $this->getFilterHeaders($response->headers->all()));
    }

    public function testJson2(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->json('hello world', 404, ['foo' => 'bar']);

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(JsonResponse::class, $response);

        $this->assertSame('"hello world"', $response->getContent());
        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame(['foo' => ['bar'], 'content-type' => ['application/json']], $this->getFilterHeaders($response->headers->all()));
    }

    public function testJson3(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->json(['foo' => 'bar', 'hello' => 'world'], 404, ['foo' => 'bar']);

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(JsonResponse::class, $response);

        $this->assertSame('{"foo":"bar","hello":"world"}', $response->getContent());
        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame(['foo' => ['bar'], 'content-type' => ['application/json']], $this->getFilterHeaders($response->headers->all()));
    }

    public function testJson4(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->json('{"foo":"bar","hello":"world"}', 404, ['foo' => 'bar'], true);

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(JsonResponse::class, $response);

        $this->assertSame('{"foo":"bar","hello":"world"}', $response->getContent());
        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame(['foo' => ['bar'], 'content-type' => ['application/json']], $this->getFilterHeaders($response->headers->all()));
    }

    public function testJsonp(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->jsonp('foo');

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(JsonResponse::class, $response);

        $this->assertSame('/**/foo({});', $response->getContent());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(['content-type' => ['text/javascript']], $this->getFilterHeaders($response->headers->all()));
    }

    public function testJsonp2(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->jsonp('foo', ['foo' => 'bar']);

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(JsonResponse::class, $response);

        $this->assertSame('/**/foo({"foo":"bar"});', $response->getContent());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(['content-type' => ['text/javascript']], $this->getFilterHeaders($response->headers->all()));
    }

    public function testJsonp3(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->jsonp('bar', ['foo' => 'bar'], 404, ['hello' => 'world']);

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(JsonResponse::class, $response);

        $this->assertSame('/**/bar({"foo":"bar"});', $response->getContent());
        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame(['hello' => ['world'], 'content-type' => ['text/javascript']], $this->getFilterHeaders($response->headers->all()));
    }

    public function testDownload(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->download(__DIR__.'/assert/download.txt');

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(BinaryFileResponse::class, $response);
        $this->assertFalse($response->getContent());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('attachment; filename=download.txt', $response->headers->all()['content-disposition'][0]);
    }

    public function testDownload2(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->download(new SplFileInfo(__DIR__.'/assert/download.txt'));

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(BinaryFileResponse::class, $response);
        $this->assertFalse($response->getContent());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('attachment; filename=download.txt', $response->headers->all()['content-disposition'][0]);
    }

    public function testDownload3(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->download(new SplFileObject(__DIR__.'/assert/download.txt'));

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(BinaryFileResponse::class, $response);
        $this->assertFalse($response->getContent());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('attachment; filename=download.txt', $response->headers->all()['content-disposition'][0]);
    }

    public function testDownload4(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->download(__DIR__.'/assert/download.txt', 'foo.txt', 200, ['foo' => 'bar']);

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(BinaryFileResponse::class, $response);
        $this->assertFalse($response->getContent());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('attachment; filename=foo.txt', $response->headers->all()['content-disposition'][0]);
        $this->assertSame('bar', $response->headers->all()['foo'][0]);
    }

    public function testFile(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->file(__DIR__.'/assert/download.txt');

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(BinaryFileResponse::class, $response);
        $this->assertFalse($response->getContent());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('inline; filename=download.txt', $response->headers->all()['content-disposition'][0]);
    }

    public function testFile2(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->file(new SplFileInfo(__DIR__.'/assert/download.txt'));

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(BinaryFileResponse::class, $response);
        $this->assertFalse($response->getContent());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('inline; filename=download.txt', $response->headers->all()['content-disposition'][0]);
    }

    public function testFile3(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->file(new SplFileObject(__DIR__.'/assert/download.txt'));

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(BinaryFileResponse::class, $response);
        $this->assertFalse($response->getContent());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('inline; filename=download.txt', $response->headers->all()['content-disposition'][0]);
    }

    public function testFile4(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->file(__DIR__.'/assert/download.txt', 200, ['foo' => 'bar']);

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(BinaryFileResponse::class, $response);
        $this->assertFalse($response->getContent());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('inline; filename=download.txt', $response->headers->all()['content-disposition'][0]);
        $this->assertSame('bar', $response->headers->all()['foo'][0]);
    }

    public function testRedirect(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->redirect('hello/world');

        $content = <<<'eot'
            <!DOCTYPE html>
            <html>
                <head>
                    <meta charset="UTF-8" />
                    <meta http-equiv="refresh" content="0;url='http://www.queryphp.com/hello/world'" />

                    <title>Redirecting to http://www.queryphp.com/hello/world</title>
                </head>
                <body>
                    Redirecting to <a href="http://www.queryphp.com/hello/world">http://www.queryphp.com/hello/world</a>.
                </body>
            </html>
            eot;

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(RedirectResponse::class, $response);
        $this->assertSame($content, $response->getContent());
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame(['location' => ['http://www.queryphp.com/hello/world']], $this->getFilterHeaders($response->headers->all()));
    }

    public function testRedirect2(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->redirect('hello/world', ['foo' => 'bar']);

        $content = <<<'eot'
            <!DOCTYPE html>
            <html>
                <head>
                    <meta charset="UTF-8" />
                    <meta http-equiv="refresh" content="0;url='http://www.queryphp.com/hello/world?foo=bar'" />

                    <title>Redirecting to http://www.queryphp.com/hello/world?foo=bar</title>
                </head>
                <body>
                    Redirecting to <a href="http://www.queryphp.com/hello/world?foo=bar">http://www.queryphp.com/hello/world?foo=bar</a>.
                </body>
            </html>
            eot;

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(RedirectResponse::class, $response);
        $this->assertSame($content, $response->getContent());
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame(['location' => ['http://www.queryphp.com/hello/world?foo=bar']], $this->getFilterHeaders($response->headers->all()));
    }

    public function testRedirectRaw(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->redirectRaw('http://queryphp.com/raw');

        $content = <<<'eot'
            <!DOCTYPE html>
            <html>
                <head>
                    <meta charset="UTF-8" />
                    <meta http-equiv="refresh" content="0;url='http://queryphp.com/raw'" />

                    <title>Redirecting to http://queryphp.com/raw</title>
                </head>
                <body>
                    Redirecting to <a href="http://queryphp.com/raw">http://queryphp.com/raw</a>.
                </body>
            </html>
            eot;

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(RedirectResponse::class, $response);
        $this->assertSame($content, $response->getContent());
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame(['location' => ['http://queryphp.com/raw']], $this->getFilterHeaders($response->headers->all()));
    }

    public function testRedirectRaw2(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->redirectRaw('http://queryphp.com/raw?foo=bar');

        $content = <<<'eot'
            <!DOCTYPE html>
            <html>
                <head>
                    <meta charset="UTF-8" />
                    <meta http-equiv="refresh" content="0;url='http://queryphp.com/raw?foo=bar'" />

                    <title>Redirecting to http://queryphp.com/raw?foo=bar</title>
                </head>
                <body>
                    Redirecting to <a href="http://queryphp.com/raw?foo=bar">http://queryphp.com/raw?foo=bar</a>.
                </body>
            </html>
            eot;

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(RedirectResponse::class, $response);
        $this->assertSame($content, $response->getContent());
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame(['location' => ['http://queryphp.com/raw?foo=bar']], $this->getFilterHeaders($response->headers->all()));
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

    protected function makeView(): View
    {
        return new View(
            new Phpui([
                'theme_path' => __DIR__.'/assert',
            ])
        );
    }

    protected function makeRedirect(bool $isSecure = false): Redirect
    {
        $request = $this->makeRequest($isSecure);

        $url = new Url($request, [
            'domain' => 'queryphp.com',
        ]);

        $redirect = new Redirect($url);

        return $redirect;
    }

    protected function makeRequest(bool $isSecure = false): Request
    {
        $request = $this->createMock(Request::class);

        $request->method('getEnter')->willReturn('');
        $this->assertSame('', $request->getEnter());

        $request->method('isSecure')->willReturn($isSecure);
        $this->assertSame($isSecure, $request->isSecure($isSecure));

        return $request;
    }
}
