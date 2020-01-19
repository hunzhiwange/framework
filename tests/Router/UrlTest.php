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

use Leevel\Http\Request;
use Leevel\Router\Url;
use Tests\TestCase;

/**
 * @api(
 *     title="URL 生成",
 *     path="router/url",
 *     description="
 * QueryPHP 支持路由 URL 地址的统一生成，提供一套简洁的生成方法，无需记忆即可学会使用。
 *
 * 使用容器 url 服务
 *
 * ``` php
 * \App::make('url')->make(string $url, array $params = [], string $subdomain = 'www', $suffix = null): string;
 * ```
 * ",
 * )
 */
class UrlTest extends TestCase
{
    /**
     * @api(
     *     title="基本 URL 生成",
     *     description="",
     *     note="",
     * )
     */
    public function testMakeUrl(): void
    {
        $request = $this->makeRequest();
        $url = new Url($request);
        $this->assertInstanceof(Request::class, $url->getRequest());

        // 开始不带斜线，自动添加
        $this->assertSame($url->make('test/hello'), '/test/hello');
        $this->assertSame($url->make('/hello-world'), '/hello-world');
    }

    /**
     * @api(
     *     title="生成带参数的 URL",
     *     description="",
     *     note="",
     * )
     */
    public function testMakeUrlWithParams(): void
    {
        $request = $this->makeRequest();
        $url = new Url($request);

        $this->assertSame($url->make('test/hello?arg1=1&arg2=3'), '/test/hello?arg1=1&arg2=3');
        $this->assertSame($url->make('test/sub1/sub2/hello?arg1=1&arg2=3'), '/test/sub1/sub2/hello?arg1=1&arg2=3');
        $this->assertSame($url->make('test/sub1/sub2/hello', ['arg1' => 1, 'arg2' => 3]), '/test/sub1/sub2/hello?arg1=1&arg2=3');
    }

    /**
     * @api(
     *     title="生成带后缀的 URL",
     *     description="",
     *     note="",
     * )
     */
    public function testMakeUrlWithSuffix(): void
    {
        $request = $this->makeRequest();
        $url = new Url($request);

        $this->assertSame($url->make('hello/world', [], '', true), '/hello/world.html');
        $this->assertSame($url->make('hello/world', [], '', '.jsp'), '/hello/world.jsp');
    }

    /**
     * @api(
     *     title="生成 URL 支持变量替换",
     *     description="",
     *     note="",
     * )
     */
    public function testMakeUrlSupportVar(): void
    {
        $request = $this->makeRequest();
        $url = new Url($request);

        $this->assertSame($url->make('test/{id}?arg1=5', ['id' => 5]), '/test/5?arg1=5');
        $this->assertSame($url->make('/new-{id}-{name}', ['id' => 5, 'name' => 'tom', 'arg1' => '5']), '/new-5-tom?arg1=5');
        $this->assertSame($url->make('/new-{id}-{name}?hello=world', ['id' => 5, 'name' => 'tom', 'arg1' => '5']), '/new-5-tom?hello=world&arg1=5');
        $this->assertSame($url->make('/new-{id}-{name}?hello={foo}', ['id' => 5, 'name' => 'tom', 'foo' => 'bar', 'arg1' => '5']), '/new-5-tom?hello=bar&arg1=5');
    }

    /**
     * @api(
     *     title="生成指定应用的 URL",
     *     description="",
     *     note="",
     * )
     */
    public function testMakeUrlForApp(): void
    {
        $request = $this->makeRequest();
        $url = new Url($request);

        $this->assertSame($url->make(':myapp/hello/world', ['id' => 5, 'name' => 'yes']), '/:myapp/hello/world?id=5&name=yes');
        $this->assertSame($url->make(':myapp/test'), '/:myapp/test');
    }

    /**
     * @api(
     *     title="生成首页地址",
     *     description="",
     *     note="",
     * )
     */
    public function testMakeUrlForHome(): void
    {
        $request = $this->makeRequest();
        $url = new Url($request);

        $this->assertSame($url->make('/'), '/');
        $this->assertSame($url->make(''), '/');
    }

    /**
     * @api(
     *     title="生成带域名的 URL",
     *     description="",
     *     note="",
     * )
     */
    public function testWithDomainTop(): void
    {
        $request = $this->makeRequest();
        $url = new Url($request, [
            'domain' => 'queryphp.com',
        ]);

        $this->assertSame($url->make('hello/world'), 'http://www.queryphp.com/hello/world');
        $this->assertSame($url->make('hello/world', [], 'vip'), 'http://vip.queryphp.com/hello/world');
        $this->assertSame($url->make('hello/world', [], 'defu.vip'), 'http://defu.vip.queryphp.com/hello/world');
        $this->assertSame($url->make('hello/world', [], '*'), 'http://queryphp.com/hello/world');
    }

    public function testSetOption(): void
    {
        $request = $this->makeRequest();
        $url = new Url($request);

        $this->assertSame($url->make('hello/world'), '/hello/world');

        $url->setOption('domain', 'queryphp.cn');
        $this->assertSame($url->make('hello/world'), 'http://www.queryphp.cn/hello/world');
    }

    public function testSecure(): void
    {
        $request = $this->makeRequest(true);
        $url = new Url($request);

        $this->assertInstanceof(Request::class, $url->getRequest());
        $this->assertSame($url->make('hello/world'), '/hello/world');
    }

    public function testWithVarButNotMatche(): void
    {
        $request = $this->makeRequest();
        $url = new Url($request);

        $this->assertInstanceof(Request::class, $url->getRequest());
        $this->assertSame('/hello/{foo}', $url->make('hello/{foo}', []));
    }

    /**
     * @api(
     *     title="生成带 HTTPS 的 URL",
     *     description="",
     *     note="",
     * )
     */
    public function testSecureWithDomainTop(): void
    {
        $request = $this->makeRequest(true);
        $url = new Url($request, [
            'domain' => 'queryphp.cn',
        ]);

        $this->assertInstanceof(Request::class, $url->getRequest());
        $this->assertSame($url->make('hello/world'), 'https://www.queryphp.cn/hello/world');
    }

    public function testGetDomain(): void
    {
        $request = $this->makeRequest(true);
        $url = new Url($request);

        $this->assertSame($url->getDomain(), '');
    }

    public function testGetDomain2(): void
    {
        $request = $this->makeRequest(true);
        $url = new Url($request, [
            'domain' => 'queryphp.cn',
        ]);

        $this->assertSame($url->getDomain(), 'queryphp.cn');
    }

    protected function makeRequest(bool $isSecure = false): Request
    {
        $request = $this->createMock(Request::class);

        $request->method('getEnter')->willReturn('');
        $this->assertSame('', $request->getEnter());

        $request->method('isSecure')->willReturn($isSecure);
        $this->assertSame($isSecure, $request->isSecure());

        return $request;
    }
}
