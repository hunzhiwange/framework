<?php

declare(strict_types=1);

namespace Tests\Router;

use Leevel\Http\Request;
use Leevel\Kernel\Utils\Api;
use Leevel\Router\Url;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => 'URL 生成',
    'path' => 'router/url',
    'zh-CN:description' => <<<'EOT'
QueryPHP 支持路由 URL 地址的统一生成，提供一套简洁的生成方法，无需记忆即可学会使用。

使用容器 url 服务

``` php
\App::make('url')->make(string $url, array $attributes = [], string $subdomain = 'www', $suffix = null): string;
```
EOT,
])]
final class UrlTest extends TestCase
{
    #[Api([
        'zh-CN:title' => '基本 URL 生成',
    ])]
    public function testMakeUrl(): void
    {
        $request = $this->makeRequest();
        $url = new Url($request);
        $this->assertInstanceof(Request::class, $url->getRequest());

        // 开始不带斜线，自动添加
        static::assertSame($url->make('test/hello'), '/test/hello');
        static::assertSame($url->make('/hello-world'), '/hello-world');
    }

    #[Api([
        'zh-CN:title' => '生成带参数的 URL',
    ])]
    public function testMakeUrlWithParams(): void
    {
        $request = $this->makeRequest();
        $url = new Url($request);

        static::assertSame($url->make('test/hello?arg1=1&arg2=3'), '/test/hello?arg1=1&arg2=3');
        static::assertSame($url->make('test/sub1/sub2/hello?arg1=1&arg2=3'), '/test/sub1/sub2/hello?arg1=1&arg2=3');
        static::assertSame($url->make('test/sub1/sub2/hello', ['arg1' => 1, 'arg2' => 3]), '/test/sub1/sub2/hello?arg1=1&arg2=3');
    }

    #[Api([
        'zh-CN:title' => '生成带后缀的 URL',
    ])]
    public function testMakeUrlWithSuffix(): void
    {
        $request = $this->makeRequest();
        $url = new Url($request);

        static::assertSame($url->make('hello/world', [], '', true), '/hello/world.html');
        static::assertSame($url->make('hello/world', [], '', '.jsp'), '/hello/world.jsp');
    }

    #[Api([
        'zh-CN:title' => '生成 URL 支持变量替换',
    ])]
    public function testMakeUrlSupportVar(): void
    {
        $request = $this->makeRequest();
        $url = new Url($request);

        static::assertSame($url->make('test/{id}?arg1=5', ['id' => 5]), '/test/5?arg1=5');
        static::assertSame($url->make('/new-{id}-{name}', ['id' => 5, 'name' => 'tom', 'arg1' => '5']), '/new-5-tom?arg1=5');
        static::assertSame($url->make('/new-{id}-{name}?hello=world', ['id' => 5, 'name' => 'tom', 'arg1' => '5']), '/new-5-tom?hello=world&arg1=5');
        static::assertSame($url->make('/new-{id}-{name}?hello={foo}', ['id' => 5, 'name' => 'tom', 'foo' => 'bar', 'arg1' => '5']), '/new-5-tom?hello=bar&arg1=5');
    }

    #[Api([
        'zh-CN:title' => '生成指定应用的 URL',
    ])]
    public function testMakeUrlForApp(): void
    {
        $request = $this->makeRequest();
        $url = new Url($request);

        static::assertSame($url->make(':myapp/hello/world', ['id' => 5, 'name' => 'yes']), '/:myapp/hello/world?id=5&name=yes');
        static::assertSame($url->make(':myapp/test'), '/:myapp/test');
    }

    #[Api([
        'zh-CN:title' => '生成首页地址',
    ])]
    public function testMakeUrlForHome(): void
    {
        $request = $this->makeRequest();
        $url = new Url($request);

        static::assertSame($url->make('/'), '/');
        static::assertSame($url->make(''), '/');
    }

    #[Api([
        'zh-CN:title' => '生成带域名的 URL',
    ])]
    public function testWithDomainTop(): void
    {
        $request = $this->makeRequest();
        $url = new Url($request, [
            'domain' => 'queryphp.com',
        ]);

        static::assertSame($url->make('hello/world'), 'http://www.queryphp.com/hello/world');
        static::assertSame($url->make('hello/world', [], 'vip'), 'http://vip.queryphp.com/hello/world');
        static::assertSame($url->make('hello/world', [], 'defu.vip'), 'http://defu.vip.queryphp.com/hello/world');
        static::assertSame($url->make('hello/world', [], '*'), 'http://queryphp.com/hello/world');
    }

    public function testSecure(): void
    {
        $request = $this->makeRequest(true);
        $url = new Url($request);

        $this->assertInstanceof(Request::class, $url->getRequest());
        static::assertSame($url->make('hello/world'), '/hello/world');
    }

    public function testWithVarButNotMatche(): void
    {
        $request = $this->makeRequest();
        $url = new Url($request);

        $this->assertInstanceof(Request::class, $url->getRequest());
        static::assertSame('/hello/{foo}', $url->make('hello/{foo}', []));
    }

    #[Api([
        'zh-CN:title' => '生成带 HTTPS 的 URL',
    ])]
    public function testSecureWithDomainTop(): void
    {
        $request = $this->makeRequest(true);
        $url = new Url($request, [
            'domain' => 'queryphp.cn',
        ]);

        $this->assertInstanceof(Request::class, $url->getRequest());
        static::assertSame($url->make('hello/world'), 'https://www.queryphp.cn/hello/world');
    }

    public function testGetDomain(): void
    {
        $request = $this->makeRequest(true);
        $url = new Url($request);

        static::assertSame($url->getDomain(), '');
    }

    public function testGetDomain2(): void
    {
        $request = $this->makeRequest(true);
        $url = new Url($request, [
            'domain' => 'queryphp.cn',
        ]);

        static::assertSame($url->getDomain(), 'queryphp.cn');
    }

    protected function makeRequest(bool $isSecure = false): Request
    {
        $request = $this->createMock(Request::class);

        $request->method('getEnter')->willReturn('');
        static::assertSame('', $request->getEnter());

        $request->method('isSecure')->willReturn($isSecure);
        static::assertSame($isSecure, $request->isSecure());

        return $request;
    }
}
