<?php
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

use Tests\TestCase;
use Queryyetsimple\Router\Url;
use Queryyetsimple\Http\Request;

/**
 * url 组件测试
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2018.02.06
 * @version 1.0
 */
class UrlTest extends TestCase
{

    /**
     * 初始化
     *
     * @return void
     */
    protected function setUp()
    {
        $request = Request::createFromGlobals();

        $request->

        setApp('home')->

        setController('index')->

        setAction('index');

        $this->url = new Url($request);

        $this->urlDomain = new Url($request, [
            'router_domain_top' => 'queryphp.com'
        ]);

        $this->urlNormal = new Url($request, [
            'model' => 'default'
        ]);

        $this->urlNormalDomain = new Url($request, [
            'model' => 'default',
            'router_domain_top' => 'queryphp.com'
        ]);
    }

    /**
     * pathinfo url 生成
     *
     * @return void
     */
    public function testPathinfoUrl()
    {
        $this->assertEquals($this->url->make('/'), '/'); 

        $this->assertEquals($this->url->make('test/hello'), '/test/hello.html'); 

        $this->assertEquals($this->url->make('test/hello?arg1=1&arg2=3'), '/test/hello.html?arg1=1&arg2=3');

        $this->assertEquals($this->url->make('test/hello?arg1=1&arg2=3'), '/test/hello.html?arg1=1&arg2=3');

        $this->assertEquals($this->url->make('test/sub1/sub2/hello?arg1=1&arg2=3'), '/test/sub1/sub2/hello.html?arg1=1&arg2=3');

        $this->assertEquals($this->url->make('myapp://hello/world' ,['id'=>5,'name'=>'yes']), '/myapp/hello/world.html?id=5&name=yes'); 

        $this->assertEquals($this->url->make('myapp://test'), '/myapp/test.html'); 

        $this->assertEquals($this->url->make('hello/world', [], ['normal' => true]), '?_app=home&_c=hello&_a=world'); 

        $this->assertEquals($this->url->make('hello/world', [], ['suffix' => false]), '/hello/world'); 

        $this->assertEquals($this->url->make('hello/world', [], ['suffix' => '.jsp']), '/hello/world.jsp');

        $this->assertEquals($this->urlDomain->make('hello/world'), 'http://www.queryphp.com/hello/world.html');

        $this->assertEquals($this->urlDomain->make('hello/world', [], ['subdomain' => 'vip']), 'http://vip.queryphp.com/hello/world.html');

        $this->assertEquals($this->urlDomain->make('hello/world', [], ['subdomain' => 'defu.vip']), 'http://defu.vip.queryphp.com/hello/world.html');

        $this->assertEquals($this->urlDomain->make('hello/world', [], ['subdomain' => '*']), 'http://queryphp.com/hello/world.html');
    }

    /**
     * 自定义 url
     *
     * @return void
     */
    public function testCustomUrl()
    {
        $this->assertEquals($this->url->make('/'), '/');

        $this->assertEquals($this->url->make('/hello-world'), '/hello-world.html'); 

        $this->assertEquals($this->url->make('/new-{id}-{name}', ['id' => 5, 'name' => 'tom', 'arg1' => '5']), '/new-5-tom.html?arg1=5');

        $this->assertEquals($this->url->make('/new-{id}-{name}?hello=world', ['id' => 5, 'name' => 'tom', 'arg1' => '5']), '/new-5-tom.html?hello=world&arg1=5');

        $this->assertEquals($this->url->make('/new-{id}-{name}?hello={foo}', ['id' => 5, 'name' => 'tom', 'foo' => 'bar', 'arg1' => '5']), '/new-5-tom.html?hello=bar&arg1=5');
    }

    /**
     * normal url
     *
     * @return void
     */
    public function testNormalUrl()
    {
        $this->assertEquals($this->urlNormal->make('/'), '');

        $this->assertEquals($this->urlNormal->make('test/hello'), '?_c=test&_a=hello');

        $this->assertEquals($this->urlNormal->make('test/hello?arg1=1&arg2=3'), '?_c=test&_a=hello&arg1=1&arg2=3');

        $this->assertEquals($this->urlNormal->make('test/hello?arg1=1&arg2=3', ['foo' => 'bar']), '?_c=test&_a=hello&arg1=1&arg2=3&foo=bar');

        $this->assertEquals($this->urlNormal->make('group://test/hello?arg1=1&arg2=3', ['foo' => 'bar']), '?_app=group&_c=test&_a=hello&arg1=1&arg2=3&foo=bar');

        $this->assertEquals($this->urlNormalDomain->make('group://test/hello?arg1=1&arg2=3', ['foo' => 'bar']), 'http://www.queryphp.com?_app=group&_c=test&_a=hello&arg1=1&arg2=3&foo=bar');
        
        $this->assertEquals($this->urlNormal->make('test/subdir/subdir2/hello?query1=1&query2=2'), '?_c=test&_a=hello&_prefix=subdir%5Csubdir2&query1=1&query2=2');

        $this->assertEquals($this->urlNormal->make('test/subdir/subdir2/hello?_params[]=1&_params[]=2&_params[foo]=bar'), '?_c=test&_a=hello&_prefix=subdir%5Csubdir2&_params%5B0%5D=1&_params%5B1%5D=2&_params%5Bfoo%5D=bar');
    }
}
