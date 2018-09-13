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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Router;

use Leevel\Http\IRequest;
use Leevel\Router\Url;
use Tests\TestCase;

/**
 * url 组件测试.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.02.06
 *
 * @version 1.0
 */
class UrlTest extends TestCase
{
    public function testMakeUrl()
    {
        $request = $this->makeRequest();
        $url = new Url($request);

        $this->assertInstanceof(IRequest::class, $url->getRequest());

        // 开始不带斜线，自动添加
        $this->assertSame($url->make('/'), '/');
        $this->assertSame($url->make(''), '/');
        $this->assertSame($url->make('test/hello'), '/test/hello');
        $this->assertSame($url->make('test/hello?arg1=1&arg2=3'), '/test/hello?arg1=1&arg2=3');
        $this->assertSame($url->make('test/sub1/sub2/hello?arg1=1&arg2=3'), '/test/sub1/sub2/hello?arg1=1&arg2=3');
        $this->assertSame($url->make(':myapp/hello/world', ['id' => 5, 'name' => 'yes']), '/:myapp/hello/world?id=5&name=yes');
        $this->assertSame($url->make(':myapp/test'), '/:myapp/test');
        $this->assertSame($url->make('hello/world', [], '', true), '/hello/world.html');
        $this->assertSame($url->make('hello/world', [], '', '.jsp'), '/hello/world.jsp');

        // 开始可带斜线
        $this->assertSame($url->make('/hello-world'), '/hello-world');
        $this->assertSame($url->make('/new-{id}-{name}', ['id' => 5, 'name' => 'tom', 'arg1' => '5']), '/new-5-tom?arg1=5');
        $this->assertSame($url->make('/new-{id}-{name}?hello=world', ['id' => 5, 'name' => 'tom', 'arg1' => '5']), '/new-5-tom?hello=world&arg1=5');
        $this->assertSame($url->make('/new-{id}-{name}?hello={foo}', ['id' => 5, 'name' => 'tom', 'foo' => 'bar', 'arg1' => '5']), '/new-5-tom?hello=bar&arg1=5');
    }

    public function testWithDomainTop()
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

    public function testSetOption()
    {
        $request = $this->makeRequest();
        $url = new Url($request);

        $this->assertSame($url->make('hello/world'), '/hello/world');

        $url->setOption('domain', 'queryphp.cn');
        $this->assertSame($url->make('hello/world'), 'http://www.queryphp.cn/hello/world');
    }

    public function testSecure()
    {
        $request = $this->makeRequest(true);
        $url = new Url($request);

        $this->assertInstanceof(IRequest::class, $url->getRequest());

        $this->assertSame($url->make('hello/world'), '/hello/world');
    }

    public function testWithVarButNotMatche()
    {
        $request = $this->makeRequest();
        $url = new Url($request);

        $this->assertInstanceof(IRequest::class, $url->getRequest());

        $this->assertSame('/hello/{foo}', $url->make('hello/{foo}', []));
    }

    public function testSecureWithDomainTop()
    {
        $request = $this->makeRequest(true);
        $url = new Url($request, [
            'domain' => 'queryphp.cn',
        ]);

        $this->assertInstanceof(IRequest::class, $url->getRequest());

        $this->assertSame($url->make('hello/world'), 'https://www.queryphp.cn/hello/world');
    }

    protected function makeRequest(bool $isSecure = false): IRequest
    {
        $request = $this->createMock(IRequest::class);

        $request->method('getEnter')->willReturn('');
        $this->assertSame('', $request->getEnter());

        $request->method('isSecure')->willReturn($isSecure);
        $this->assertSame($isSecure, $request->isSecure());

        return $request;
    }
}
