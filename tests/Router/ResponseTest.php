<?php

declare(strict_types=1);

namespace Tests\Router;

use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Http\JsonResponse;
use Leevel\Http\RedirectResponse;
use Leevel\Http\Request;
use Leevel\Kernel\App;
use Leevel\Kernel\Utils\Api;
use Leevel\Option\Option;
use Leevel\Router\Redirect;
use Leevel\Router\Response as RouterResponse;
use Leevel\Router\Url;
use Leevel\View\IView;
use Leevel\View\Manager;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => '路由响应',
    'path' => 'router/response',
    'zh-CN:description' => <<<'EOT'
QueryPHP 路由响应封装了常用的响应，比如模板、JSON、文件下载、URL 重定向等。

使用容器 response 服务

``` php
\App::make('response')->json($data = null, int $status = 200, array $headers = [], bool $json = false): \Leevel\Http\JsonResponse;
```
EOT,
])]
final class ResponseTest extends TestCase
{
    #[Api([
        'zh-CN:title' => '基本使用',
    ])]
    public function testBaseUse(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->make('hello');

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);

        static::assertSame('hello', $response->getContent());
        static::assertSame(200, $response->getStatusCode());
        $headers = $response->headers->all();
        unset($headers['date']);
        static::assertSame(['cache-control' => ['no-cache, private']], $headers);
    }

    #[Api([
        'zh-CN:title' => 'make 返回一个响应',
    ])]
    public function testMake(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->make('foo.bar', 404, ['foo' => 'bar']);

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);

        static::assertSame('foo.bar', $response->getContent());
        static::assertSame(404, $response->getStatusCode());
        static::assertSame(['foo' => ['bar']], $this->getFilterHeaders($response->headers->all()));
    }

    #[Api([
        'zh-CN:title' => 'view 返回视图响应',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**tests/Router/assert/view1.php**

``` php
{[file_get_contents('vendor/hunzhiwange/framework/tests/Router/assert/view1.php')]}
```
EOT,
    ])]
    public function testView(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->view('view1', ['foo' => 'bar']);

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);

        static::assertSame('hello view1 for bar.', $response->getContent());
        static::assertSame(200, $response->getStatusCode());
        static::assertSame([], $this->getFilterHeaders($response->headers->all()));
    }

    #[Api([
        'zh-CN:title' => 'view 返回视图响应支持自定义后缀',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**tests/Router/assert/view1.foo**

``` php
{[file_get_contents('vendor/hunzhiwange/framework/tests/Router/assert/view1.foo')]}
```
EOT,
    ])]
    public function testViewWithCustomExt(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->view('view1', ['foo' => 'bar new'], '.foo');

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);

        static::assertSame('hello view1.foo for bar new.', $response->getContent());
        static::assertSame(200, $response->getStatusCode());
        $headers = $response->headers->all();
        static::assertSame([], $this->getFilterHeaders($headers));
    }

    public function testViewWithHeaderAndStatus(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->view('view1', ['foo' => 'bar new'], null, 404, ['hello' => 'world']);

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);

        static::assertSame('hello view1 for bar new.', $response->getContent());
        static::assertSame(404, $response->getStatusCode());
        static::assertSame(['hello' => ['world']], $this->getFilterHeaders($response->headers->all()));
    }

    #[Api([
        'zh-CN:title' => 'view 返回视图成功消息',
        'zh-CN:description' => <<<'EOT'
默认错误模板为 `success`。

**fixture 定义**

**tests/Router/assert/success.php**

``` php
{[file_get_contents('vendor/hunzhiwange/framework/tests/Router/assert/success.php')]}
```
EOT,
    ])]
    public function testViewSuccess(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->viewSuccess('it is success.');

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);

        static::assertSame('success! message is it is success.,url is ,time is 1.', $response->getContent());
        static::assertSame(200, $response->getStatusCode());
        static::assertSame([], $this->getFilterHeaders($response->headers->all()));
    }

    public function testViewSuccess2(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->viewSuccess('it is success2.', 'http://queryphp.com', 3);

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);

        static::assertSame('success! message is it is success2.,url is http://queryphp.com,time is 3.', $response->getContent());
        static::assertSame(200, $response->getStatusCode());
        static::assertSame([], $this->getFilterHeaders($response->headers->all()));
    }

    #[Api([
        'zh-CN:title' => 'setViewSuccessTemplate 返回视图成功消息支持设置视图正确模板',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**tests/Router/assert/success_custom.php**

``` php
{[file_get_contents('vendor/hunzhiwange/framework/tests/Router/assert/success_custom.php')]}
```
EOT,
    ])]
    public function testViewSuccess3(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $factory->setViewSuccessTemplate('success_custom');

        $response = $factory->viewSuccess('it is success3.', 'http://queryphp.com', 3);

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);

        static::assertSame('success custom! message is it is success3.,url is http://queryphp.com,time is 3.', $response->getContent());
        static::assertSame(200, $response->getStatusCode());
        static::assertSame([], $this->getFilterHeaders($response->headers->all()));
    }

    #[Api([
        'zh-CN:title' => 'view 返回视图失败消息',
        'zh-CN:description' => <<<'EOT'
默认错误模板为 `fail`。

**fixture 定义**

**tests/Router/assert/fail.php**

``` php
{[file_get_contents('vendor/hunzhiwange/framework/tests/Router/assert/fail.php')]}
```
EOT,
    ])]
    public function testViewFail(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->viewFail('it is fail.');

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);

        static::assertSame('fail! message is it is fail.,url is ,time is 3.', $response->getContent());
        static::assertSame(404, $response->getStatusCode());
        static::assertSame([], $this->getFilterHeaders($response->headers->all()));
    }

    public function testViewFail2(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->viewFail('it is fail2.', 'http://queryphp.com', 3);

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);

        static::assertSame('fail! message is it is fail2.,url is http://queryphp.com,time is 3.', $response->getContent());
        static::assertSame(404, $response->getStatusCode());
        static::assertSame([], $this->getFilterHeaders($response->headers->all()));
    }

    #[Api([
        'zh-CN:title' => 'setViewFailTemplate 返回视图失败消息支持设置视图错误模板',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**tests/Router/assert/fail_custom.php**

``` php
{[file_get_contents('vendor/hunzhiwange/framework/tests/Router/assert/fail_custom.php')]}
```
EOT,
    ])]
    public function testViewFail3(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $factory->setViewFailTemplate('fail_custom');

        $response = $factory->viewFail('it is fail3.', 'http://queryphp.com', 3);

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);

        static::assertSame('fail custom! message is it is fail3.,url is http://queryphp.com,time is 3.', $response->getContent());
        static::assertSame(404, $response->getStatusCode());
        static::assertSame([], $this->getFilterHeaders($response->headers->all()));
    }

    #[Api([
        'zh-CN:title' => 'json 返回 JSON 响应',
    ])]
    public function testJson(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->json();

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(JsonResponse::class, $response);

        static::assertSame('{}', $response->getContent());
        static::assertSame(200, $response->getStatusCode());
        static::assertSame(['content-type' => ['application/json']], $this->getFilterHeaders($response->headers->all()));
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

        static::assertSame('"hello world"', $response->getContent());
        static::assertSame(404, $response->getStatusCode());
        static::assertSame(['foo' => ['bar'], 'content-type' => ['application/json']], $this->getFilterHeaders($response->headers->all()));
    }

    #[Api([
        'zh-CN:title' => 'json 返回 JSON 响应支持数组',
    ])]
    public function testJson3(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->json(['foo' => 'bar', 'hello' => 'world'], 404, ['foo' => 'bar']);

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(JsonResponse::class, $response);

        static::assertSame('{"foo":"bar","hello":"world"}', $response->getContent());
        static::assertSame(404, $response->getStatusCode());
        static::assertSame(['foo' => ['bar'], 'content-type' => ['application/json']], $this->getFilterHeaders($response->headers->all()));
    }

    #[Api([
        'zh-CN:title' => 'json 返回 JSON 响应支持原生 JSON 字符串',
    ])]
    public function testJson4(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->json('{"foo":"bar","hello":"world"}', 404, ['foo' => 'bar'], true);

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(JsonResponse::class, $response);

        static::assertSame('{"foo":"bar","hello":"world"}', $response->getContent());
        static::assertSame(404, $response->getStatusCode());
        static::assertSame(['foo' => ['bar'], 'content-type' => ['application/json']], $this->getFilterHeaders($response->headers->all()));
    }

    #[Api([
        'zh-CN:title' => 'jsonp 返回 JSONP 响应',
    ])]
    public function testJsonp(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->jsonp('foo');

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(JsonResponse::class, $response);

        static::assertSame('/**/foo({});', $response->getContent());
        static::assertSame(200, $response->getStatusCode());
        static::assertSame(['content-type' => ['text/javascript']], $this->getFilterHeaders($response->headers->all()));
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

        static::assertSame('/**/foo({"foo":"bar"});', $response->getContent());
        static::assertSame(200, $response->getStatusCode());
        static::assertSame(['content-type' => ['text/javascript']], $this->getFilterHeaders($response->headers->all()));
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

        static::assertSame('/**/bar({"foo":"bar"});', $response->getContent());
        static::assertSame(404, $response->getStatusCode());
        static::assertSame(['hello' => ['world'], 'content-type' => ['text/javascript']], $this->getFilterHeaders($response->headers->all()));
    }

    #[Api([
        'zh-CN:title' => 'download 返回下载响应',
    ])]
    public function testDownload(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->download(__DIR__.'/assert/download.txt');

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(BinaryFileResponse::class, $response);
        static::assertFalse($response->getContent());
        static::assertSame(200, $response->getStatusCode());
        static::assertSame('attachment; filename=download.txt', $response->headers->all()['content-disposition'][0]);
    }

    #[Api([
        'zh-CN:title' => 'download 返回下载响应支持 \SplFileInfo',
    ])]
    public function testDownload2(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->download(new \SplFileInfo(__DIR__.'/assert/download.txt'));

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(BinaryFileResponse::class, $response);
        static::assertFalse($response->getContent());
        static::assertSame(200, $response->getStatusCode());
        static::assertSame('attachment; filename=download.txt', $response->headers->all()['content-disposition'][0]);
    }

    #[Api([
        'zh-CN:title' => 'download 返回下载响应支持 \SplFileObject',
    ])]
    public function testDownload3(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->download(new \SplFileObject(__DIR__.'/assert/download.txt'));

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(BinaryFileResponse::class, $response);
        static::assertFalse($response->getContent());
        static::assertSame(200, $response->getStatusCode());
        static::assertSame('attachment; filename=download.txt', $response->headers->all()['content-disposition'][0]);
    }

    #[Api([
        'zh-CN:title' => 'download 返回下载响应支持自定义文件名',
    ])]
    public function testDownload4(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->download(__DIR__.'/assert/download.txt', 'foo.txt', 200, ['foo' => 'bar']);

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(BinaryFileResponse::class, $response);
        static::assertFalse($response->getContent());
        static::assertSame(200, $response->getStatusCode());
        static::assertSame('attachment; filename=foo.txt', $response->headers->all()['content-disposition'][0]);
        static::assertSame('bar', $response->headers->all()['foo'][0]);
    }

    #[Api([
        'zh-CN:title' => 'file 返回文件响应',
    ])]
    public function testFile(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->file(__DIR__.'/assert/download.txt');

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(BinaryFileResponse::class, $response);
        static::assertFalse($response->getContent());
        static::assertSame(200, $response->getStatusCode());
        static::assertSame('inline; filename=download.txt', $response->headers->all()['content-disposition'][0]);
    }

    #[Api([
        'zh-CN:title' => 'file 返回文件响应支持 \SplFileInfo',
    ])]
    public function testFile2(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->file(new \SplFileInfo(__DIR__.'/assert/download.txt'));

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(BinaryFileResponse::class, $response);
        static::assertFalse($response->getContent());
        static::assertSame(200, $response->getStatusCode());
        static::assertSame('inline; filename=download.txt', $response->headers->all()['content-disposition'][0]);
    }

    #[Api([
        'zh-CN:title' => 'file 返回文件响应支持 \SplFileObject',
    ])]
    public function testFile3(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();

        $factory = new RouterResponse($view, $redirect);

        $response = $factory->file(new \SplFileObject(__DIR__.'/assert/download.txt'));

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(BinaryFileResponse::class, $response);
        static::assertFalse($response->getContent());
        static::assertSame(200, $response->getStatusCode());
        static::assertSame('inline; filename=download.txt', $response->headers->all()['content-disposition'][0]);
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
        static::assertFalse($response->getContent());
        static::assertSame(200, $response->getStatusCode());
        static::assertSame('inline; filename=download.txt', $response->headers->all()['content-disposition'][0]);
        static::assertSame('bar', $response->headers->all()['foo'][0]);
    }

    #[Api([
        'zh-CN:title' => 'redirect 返回一个 URL 生成跳转响应',
    ])]
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
        static::assertSame($content, $response->getContent());
        static::assertSame(302, $response->getStatusCode());
        static::assertSame(['location' => ['http://www.queryphp.com/hello/world']], $this->getFilterHeaders($response->headers->all()));
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
        static::assertSame($content, $response->getContent());
        static::assertSame(302, $response->getStatusCode());
        static::assertSame(['location' => ['http://www.queryphp.com/hello/world?foo=bar']], $this->getFilterHeaders($response->headers->all()));
    }

    #[Api([
        'zh-CN:title' => 'redirectRaw 返回一个跳转响应',
    ])]
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
        static::assertSame($content, $response->getContent());
        static::assertSame(302, $response->getStatusCode());
        static::assertSame(['location' => ['http://queryphp.com/raw']], $this->getFilterHeaders($response->headers->all()));
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
        static::assertSame($content, $response->getContent());
        static::assertSame(302, $response->getStatusCode());
        static::assertSame(['location' => ['http://queryphp.com/raw?foo=bar']], $this->getFilterHeaders($response->headers->all()));
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

    protected function makeView(): IView
    {
        return $this->createViewManager('phpui')->connect('phpui');
    }

    protected function makeRedirect(bool $isSecure = false): Redirect
    {
        $request = $this->makeRequest($isSecure);

        $url = new Url($request, [
            'domain' => 'queryphp.com',
        ]);

        return new Redirect($url);
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

    protected function createViewManager(string $connect = 'html'): Manager
    {
        $app = new ExtendAppForResponse($container = new Container(), '');
        $container->instance('app', $app);

        $manager = new Manager($container);

        $this->assertInstanceof(IContainer::class, $manager->container());
        $this->assertInstanceof(Container::class, $manager->container());

        static::assertSame(__DIR__.'/assert', $app->themesPath());
        static::assertSame(__DIR__.'/cache_theme', $app->storagePath('theme'));

        $option = new Option([
            'view' => [
                'default' => $connect,
                'action_fail' => 'public/fail',
                'action_success' => 'public/success',
                'connect' => [
                    'html' => [
                        'driver' => 'html',
                        'suffix' => '.html',
                    ],
                    'phpui' => [
                        'driver' => 'phpui',
                        'suffix' => '.php',
                    ],
                ],
            ],
        ]);
        $container->singleton('option', $option);

        $request = new ExtendRequestForResponse();
        $container->singleton('request', $request);

        return $manager;
    }
}

class ExtendAppForResponse extends App
{
    public function development(): bool
    {
        return true;
    }

    public function themesPath(string $path = ''): string
    {
        return __DIR__.'/assert';
    }

    public function storagePath(string $path = ''): string
    {
        return __DIR__.'/cache_'.$path;
    }
}

class ExtendRequestForResponse
{
}
