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

use Leevel\Http\ApiResponse;
use Leevel\Http\FileResponse;
use Leevel\Http\IRequest;
use Leevel\Http\IResponse;
use Leevel\Http\JsonResponse;
use Leevel\Http\RedirectResponse;
use Leevel\Http\Response;
use Leevel\Router\Redirect;
use Leevel\Router\Response as RouterResponse;
use Leevel\Router\Url;
use Leevel\Router\View;
use Leevel\View\Phpui;
use SplFileInfo;
use SplFileObject;
use Tests\TestCase;

/**
 * responseFactory test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.09.11
 *
 * @version 1.0
 */
class ResponseTest extends TestCase
{
    public function testBaseUse()
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->make('hello');

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);

        $this->assertSame('hello', $response->getContent());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame([], $response->headers->all());
    }

    public function testMake()
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->make(['foo', 'bar'], 404, ['foo' => 'bar']);

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);

        $this->assertSame('["foo","bar"]', $response->getContent());
        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame(['foo' => 'bar', 'content-type' => 'application/json'], $response->headers->all());
    }

    public function testView()
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->view('view1', ['foo' => 'bar']);

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);

        $this->assertSame('hello view1 for bar.', $response->getContent());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame([], $response->headers->all());
    }

    public function testViewWithCustomExt()
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->view('view1', ['foo' => 'bar new'], '.foo');

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);

        $this->assertSame('hello view1.foo for bar new.', $response->getContent());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame([], $response->headers->all());
    }

    public function testViewWithHeaderAndStatus()
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->view('view1', ['foo' => 'bar new'], null, 404, ['hello' => 'world']);

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);

        $this->assertSame('hello view1 for bar new.', $response->getContent());
        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame(['hello' => 'world'], $response->headers->all());
    }

    public function testViewSuccess()
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->viewSuccess('it is success.');

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);

        $this->assertSame('success! message is it is success.,url is ,time is 1.', $response->getContent());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame([], $response->headers->all());
    }

    public function testViewSuccess2()
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->viewSuccess('it is success2.', 'http://queryphp.com', 3);

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);

        $this->assertSame('success! message is it is success2.,url is http://queryphp.com,time is 3.', $response->getContent());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame([], $response->headers->all());
    }

    public function testViewSuccess3()
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $factory->setViewSuccessTemplate('success_custom');

        $response = $factory->viewSuccess('it is success3.', 'http://queryphp.com', 3);

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);

        $this->assertSame('success custom! message is it is success3.,url is http://queryphp.com,time is 3.', $response->getContent());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame([], $response->headers->all());
    }

    public function testViewFail()
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->viewFail('it is fail.');

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);

        $this->assertSame('fail! message is it is fail.,url is ,time is 3.', $response->getContent());
        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame([], $response->headers->all());
    }

    public function testViewFail2()
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->viewFail('it is fail2.', 'http://queryphp.com', 3);

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);

        $this->assertSame('fail! message is it is fail2.,url is http://queryphp.com,time is 3.', $response->getContent());
        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame([], $response->headers->all());
    }

    public function testViewFail3()
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $factory->setViewFailTemplate('fail_custom');

        $response = $factory->viewFail('it is fail3.', 'http://queryphp.com', 3);

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);

        $this->assertSame('fail custom! message is it is fail3.,url is http://queryphp.com,time is 3.', $response->getContent());
        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame([], $response->headers->all());
    }

    public function testJson()
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->json();

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(JsonResponse::class, $response);

        $this->assertSame('{}', $response->getContent());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(['content-type' => 'application/json'], $response->headers->all());
    }

    public function testJson2()
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->json('hello world', 404, ['foo' => 'bar']);

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(JsonResponse::class, $response);

        $this->assertSame('"hello world"', $response->getContent());
        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame(['foo' => 'bar', 'content-type' => 'application/json'], $response->headers->all());
    }

    public function testJson3()
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->json(['foo' => 'bar', 'hello' => 'world'], 404, ['foo' => 'bar']);

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(JsonResponse::class, $response);

        $this->assertSame('{"foo":"bar","hello":"world"}', $response->getContent());
        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame(['foo' => 'bar', 'content-type' => 'application/json'], $response->headers->all());
    }

    public function testJson4()
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->json('{"foo":"bar","hello":"world"}', 404, ['foo' => 'bar'], true);

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(JsonResponse::class, $response);

        $this->assertSame('{"foo":"bar","hello":"world"}', $response->getContent());
        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame(['foo' => 'bar', 'content-type' => 'application/json'], $response->headers->all());
    }

    public function testJsonp()
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->jsonp('foo');

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(JsonResponse::class, $response);

        $this->assertSame(';foo({});', $response->getContent());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(['content-type' => 'text/javascript'], $response->headers->all());
    }

    public function testJsonp2()
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->jsonp('foo', ['foo' => 'bar']);

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(JsonResponse::class, $response);

        $this->assertSame(';foo({"foo":"bar"});', $response->getContent());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(['content-type' => 'text/javascript'], $response->headers->all());
    }

    public function testJsonp3()
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->jsonp('bar', ['foo' => 'bar'], 404, ['hello' => 'world']);

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(JsonResponse::class, $response);

        $this->assertSame(';bar({"foo":"bar"});', $response->getContent());
        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame(['hello' => 'world', 'content-type' => 'text/javascript'], $response->headers->all());
    }

    public function testDownload()
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->download(__DIR__.'/assert/download.txt');

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(FileResponse::class, $response);
        $this->assertFalse($response->getContent());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('attachment; filename="download.txt"', $response->headers->all()['content-disposition']);
    }

    public function testDownload2()
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->download(new SplFileInfo(__DIR__.'/assert/download.txt'));

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(FileResponse::class, $response);
        $this->assertFalse($response->getContent());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('attachment; filename="download.txt"', $response->headers->all()['content-disposition']);
    }

    public function testDownload3()
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->download(new SplFileObject(__DIR__.'/assert/download.txt'));

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(FileResponse::class, $response);
        $this->assertFalse($response->getContent());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('attachment; filename="download.txt"', $response->headers->all()['content-disposition']);
    }

    public function testDownload4()
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->download(__DIR__.'/assert/download.txt', 'foo.txt', 200, ['foo' => 'bar']);

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(FileResponse::class, $response);
        $this->assertFalse($response->getContent());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('attachment; filename="foo.txt"', $response->headers->all()['content-disposition']);
        $this->assertSame('bar', $response->headers->all()['foo']);
    }

    public function testFile()
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->file(__DIR__.'/assert/download.txt');

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(FileResponse::class, $response);
        $this->assertFalse($response->getContent());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('inline; filename="download.txt"', $response->headers->all()['content-disposition']);
    }

    public function testFile2()
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->file(new SplFileInfo(__DIR__.'/assert/download.txt'));

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(FileResponse::class, $response);
        $this->assertFalse($response->getContent());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('inline; filename="download.txt"', $response->headers->all()['content-disposition']);
    }

    public function testFile3()
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->file(new SplFileObject(__DIR__.'/assert/download.txt'));

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(FileResponse::class, $response);
        $this->assertFalse($response->getContent());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('inline; filename="download.txt"', $response->headers->all()['content-disposition']);
    }

    public function testFile4()
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->file(__DIR__.'/assert/download.txt', 200, ['foo' => 'bar']);

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(FileResponse::class, $response);
        $this->assertFalse($response->getContent());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('inline; filename="download.txt"', $response->headers->all()['content-disposition']);
        $this->assertSame('bar', $response->headers->all()['foo']);
    }

    public function testRedirect()
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
                    <meta http-equiv="refresh" content="0;url=http://www.queryphp.com/hello/world" />
                    <title>Redirecting to http://www.queryphp.com/hello/world</title>
                </head>
                <body>
                    Redirecting to <a href="http://www.queryphp.com/hello/world">http://www.queryphp.com/hello/world</a>.
                </body>
            </html>
            eot;

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(RedirectResponse::class, $response);
        $this->assertSame($content, $response->getContent());
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame(['location' => 'http://www.queryphp.com/hello/world'], $response->headers->all());
    }

    public function testRedirect2()
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
                    <meta http-equiv="refresh" content="0;url=http://www.queryphp.com/hello/world?foo=bar" />
                    <title>Redirecting to http://www.queryphp.com/hello/world?foo=bar</title>
                </head>
                <body>
                    Redirecting to <a href="http://www.queryphp.com/hello/world?foo=bar">http://www.queryphp.com/hello/world?foo=bar</a>.
                </body>
            </html>
            eot;

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(RedirectResponse::class, $response);
        $this->assertSame($content, $response->getContent());
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame(['location' => 'http://www.queryphp.com/hello/world?foo=bar'], $response->headers->all());
    }

    public function testRedirectRaw()
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
                    <meta http-equiv="refresh" content="0;url=http://queryphp.com/raw" />
                    <title>Redirecting to http://queryphp.com/raw</title>
                </head>
                <body>
                    Redirecting to <a href="http://queryphp.com/raw">http://queryphp.com/raw</a>.
                </body>
            </html>
            eot;

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(RedirectResponse::class, $response);
        $this->assertSame($content, $response->getContent());
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame(['location' => 'http://queryphp.com/raw'], $response->headers->all());
    }

    public function testRedirectRaw2()
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
                    <meta http-equiv="refresh" content="0;url=http://queryphp.com/raw?foo=bar" />
                    <title>Redirecting to http://queryphp.com/raw?foo=bar</title>
                </head>
                <body>
                    Redirecting to <a href="http://queryphp.com/raw?foo=bar">http://queryphp.com/raw?foo=bar</a>.
                </body>
            </html>
            eot;

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(RedirectResponse::class, $response);
        $this->assertSame($content, $response->getContent());
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame(['location' => 'http://queryphp.com/raw?foo=bar'], $response->headers->all());
    }

    public function testApiOk()
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->apiOk();

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(ApiResponse::class, $response);
        $this->assertSame('""', $response->getContent());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(['content-type' => 'application/json'], $response->headers->all());
        $this->assertSame('OK', $this->getTestProperty($response, 'statusText'));
    }

    public function testApiOk2()
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->apiOk(['foo' => 'bar'], 'hello world');

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(ApiResponse::class, $response);
        $this->assertSame('{"foo":"bar"}', $response->getContent());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(['content-type' => 'application/json'], $response->headers->all());
        $this->assertSame('hello world', $this->getTestProperty($response, 'statusText'));
    }

    public function testApiCreated()
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->apiCreated();

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(ApiResponse::class, $response);
        $this->assertSame('""', $response->getContent());
        $this->assertSame(201, $response->getStatusCode());
        $this->assertSame(['content-type' => 'application/json'], $response->headers->all());
        $this->assertSame('Created', $this->getTestProperty($response, 'statusText'));
    }

    public function testApiCreated2()
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->apiCreated('http://queryphp.com', 'hello world');

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(ApiResponse::class, $response);
        $this->assertSame('"hello world"', $response->getContent());
        $this->assertSame(201, $response->getStatusCode());
        $this->assertSame(['content-type' => 'application/json', 'location' => 'http://queryphp.com'], $response->headers->all());
        $this->assertSame('Created', $this->getTestProperty($response, 'statusText'));
    }

    public function testApiAccepted()
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->apiAccepted();

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(ApiResponse::class, $response);
        $this->assertSame('""', $response->getContent());
        $this->assertSame(202, $response->getStatusCode());
        $this->assertSame(['content-type' => 'application/json'], $response->headers->all());
        $this->assertSame('Accepted', $this->getTestProperty($response, 'statusText'));
    }

    public function testApiAccepted2()
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->apiAccepted('http://queryphp.com', 'hello world');

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(ApiResponse::class, $response);
        $this->assertSame('"hello world"', $response->getContent());
        $this->assertSame(202, $response->getStatusCode());
        $this->assertSame(['content-type' => 'application/json', 'location' => 'http://queryphp.com'], $response->headers->all());
        $this->assertSame('Accepted', $this->getTestProperty($response, 'statusText'));
    }

    public function testApiNoContent()
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->apiNoContent();

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(ApiResponse::class, $response);
        $this->assertSame('{}', $response->getContent());
        $this->assertSame(204, $response->getStatusCode());
        $this->assertSame(['content-type' => 'application/json'], $response->headers->all());
        $this->assertSame('No Content', $this->getTestProperty($response, 'statusText'));
    }

    public function testApiError()
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->apiError('foo', 404);

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(ApiResponse::class, $response);
        $this->assertSame('{"message":"foo"}', $response->getContent());
        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame(['content-type' => 'application/json'], $response->headers->all());
        $this->assertSame('Not Found', $this->getTestProperty($response, 'statusText'));
    }

    public function testApiBadRequest()
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->apiBadRequest();

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(ApiResponse::class, $response);
        $this->assertSame('{"message":"Bad Request"}', $response->getContent());
        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame(['content-type' => 'application/json'], $response->headers->all());
        $this->assertSame('Bad Request', $this->getTestProperty($response, 'statusText'));
    }

    public function testApiBadRequest2()
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->apiBadRequest('foo', 'bar');

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(ApiResponse::class, $response);
        $this->assertSame('{"message":"foo"}', $response->getContent());
        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame(['content-type' => 'application/json'], $response->headers->all());
        $this->assertSame('bar', $this->getTestProperty($response, 'statusText'));
    }

    public function testApiUnauthorized()
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->apiUnauthorized();

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(ApiResponse::class, $response);
        $this->assertSame('{"message":"Unauthorized"}', $response->getContent());
        $this->assertSame(401, $response->getStatusCode());
        $this->assertSame(['content-type' => 'application/json'], $response->headers->all());
        $this->assertSame('Unauthorized', $this->getTestProperty($response, 'statusText'));
    }

    public function testApiUnauthorized2()
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->apiUnauthorized('foo', 'bar');

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(ApiResponse::class, $response);
        $this->assertSame('{"message":"foo"}', $response->getContent());
        $this->assertSame(401, $response->getStatusCode());
        $this->assertSame(['content-type' => 'application/json'], $response->headers->all());
        $this->assertSame('bar', $this->getTestProperty($response, 'statusText'));
    }

    public function testApiForbidden()
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->apiForbidden();

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(ApiResponse::class, $response);
        $this->assertSame('{"message":"Forbidden"}', $response->getContent());
        $this->assertSame(403, $response->getStatusCode());
        $this->assertSame(['content-type' => 'application/json'], $response->headers->all());
        $this->assertSame('Forbidden', $this->getTestProperty($response, 'statusText'));
    }

    public function testApiForbidden2()
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->apiForbidden('foo', 'bar');

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(ApiResponse::class, $response);
        $this->assertSame('{"message":"foo"}', $response->getContent());
        $this->assertSame(403, $response->getStatusCode());
        $this->assertSame(['content-type' => 'application/json'], $response->headers->all());
        $this->assertSame('bar', $this->getTestProperty($response, 'statusText'));
    }

    public function testApiNotFound()
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->apiNotFound();

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(ApiResponse::class, $response);
        $this->assertSame('{"message":"Not Found"}', $response->getContent());
        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame(['content-type' => 'application/json'], $response->headers->all());
        $this->assertSame('Not Found', $this->getTestProperty($response, 'statusText'));
    }

    public function testApiNotFound2()
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->apiNotFound('foo', 'bar');

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(ApiResponse::class, $response);
        $this->assertSame('{"message":"foo"}', $response->getContent());
        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame(['content-type' => 'application/json'], $response->headers->all());
        $this->assertSame('bar', $this->getTestProperty($response, 'statusText'));
    }

    public function testApiMethodNotAllowed()
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->apiMethodNotAllowed();

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(ApiResponse::class, $response);
        $this->assertSame('{"message":"Method Not Allowed"}', $response->getContent());
        $this->assertSame(405, $response->getStatusCode());
        $this->assertSame(['content-type' => 'application/json'], $response->headers->all());
        $this->assertSame('Method Not Allowed', $this->getTestProperty($response, 'statusText'));
    }

    public function testApiMethodNotAllowed2()
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->apiMethodNotAllowed('foo', 'bar');

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(ApiResponse::class, $response);
        $this->assertSame('{"message":"foo"}', $response->getContent());
        $this->assertSame(405, $response->getStatusCode());
        $this->assertSame(['content-type' => 'application/json'], $response->headers->all());
        $this->assertSame('bar', $this->getTestProperty($response, 'statusText'));
    }

    public function testApiUnprocessableEntity()
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->apiUnprocessableEntity();

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(ApiResponse::class, $response);
        $this->assertSame('{"message":"Unprocessable Entity","errors":[]}', $response->getContent());
        $this->assertSame(422, $response->getStatusCode());
        $this->assertSame(['content-type' => 'application/json'], $response->headers->all());
        $this->assertSame('Unprocessable Entity', $this->getTestProperty($response, 'statusText'));
    }

    public function testApiUnprocessableEntity2()
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->apiUnprocessableEntity(['hello' => 'world'], 'foo', 'bar');

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(ApiResponse::class, $response);
        $this->assertSame('{"message":"foo","errors":{"hello":"world"}}', $response->getContent());
        $this->assertSame(422, $response->getStatusCode());
        $this->assertSame(['content-type' => 'application/json'], $response->headers->all());
        $this->assertSame('bar', $this->getTestProperty($response, 'statusText'));
    }

    public function testApiTooManyRequests()
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->apiTooManyRequests();

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(ApiResponse::class, $response);
        $this->assertSame('{"message":"Too Many Requests"}', $response->getContent());
        $this->assertSame(429, $response->getStatusCode());
        $this->assertSame(['content-type' => 'application/json'], $response->headers->all());
        $this->assertSame('Too Many Requests', $this->getTestProperty($response, 'statusText'));
    }

    public function testApiTooManyRequests2()
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->apiTooManyRequests('foo', 'bar');

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(ApiResponse::class, $response);
        $this->assertSame('{"message":"foo"}', $response->getContent());
        $this->assertSame(429, $response->getStatusCode());
        $this->assertSame(['content-type' => 'application/json'], $response->headers->all());
        $this->assertSame('bar', $this->getTestProperty($response, 'statusText'));
    }

    public function testApiInternalServerError()
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->apiInternalServerError();

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(ApiResponse::class, $response);
        $this->assertSame('{"message":"Internal Server Error"}', $response->getContent());
        $this->assertSame(500, $response->getStatusCode());
        $this->assertSame(['content-type' => 'application/json'], $response->headers->all());
        $this->assertSame('Internal Server Error', $this->getTestProperty($response, 'statusText'));
    }

    public function testApiInternalServerError2()
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->apiInternalServerError('foo', 'bar');

        $this->assertInstanceof(IResponse::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(ApiResponse::class, $response);
        $this->assertSame('{"message":"foo"}', $response->getContent());
        $this->assertSame(500, $response->getStatusCode());
        $this->assertSame(['content-type' => 'application/json'], $response->headers->all());
        $this->assertSame('bar', $this->getTestProperty($response, 'statusText'));
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
