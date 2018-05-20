<?php declare(strict_types=1);
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
use Leevel\Router\Url;
use Leevel\Http\Request;

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
    public function testMakeUrl()
    {
        $request = Request::createFromGlobals();
        $url = new Url($request);

        // 开始不带斜线，自动添加
        $this->assertEquals($url->make('/'), '/'); 
        $this->assertEquals($url->make(''), '/'); 
        $this->assertEquals($url->make('test/hello'), '/test/hello'); 
        $this->assertEquals($url->make('test/hello?arg1=1&arg2=3'), '/test/hello?arg1=1&arg2=3');
        $this->assertEquals($url->make('test/sub1/sub2/hello?arg1=1&arg2=3'), '/test/sub1/sub2/hello?arg1=1&arg2=3');
        $this->assertEquals($url->make(':myapp/hello/world' ,['id'=>5,'name'=>'yes']), '/:myapp/hello/world?id=5&name=yes'); 
        $this->assertEquals($url->make(':myapp/test'), '/:myapp/test'); 
        $this->assertEquals($url->make('hello/world', [], '', true), '/hello/world.html'); 
        $this->assertEquals($url->make('hello/world', [], '', '.jsp'), '/hello/world.jsp');

        // 开始可带斜线
        $this->assertEquals($url->make('/hello-world'), '/hello-world'); 
        $this->assertEquals($url->make('/new-{id}-{name}', ['id' => 5, 'name' => 'tom', 'arg1' => '5']), '/new-5-tom?arg1=5');
        $this->assertEquals($url->make('/new-{id}-{name}?hello=world', ['id' => 5, 'name' => 'tom', 'arg1' => '5']), '/new-5-tom?hello=world&arg1=5');
        $this->assertEquals($url->make('/new-{id}-{name}?hello={foo}', ['id' => 5, 'name' => 'tom', 'foo' => 'bar', 'arg1' => '5']), '/new-5-tom?hello=bar&arg1=5');

        $urlDomain = new Url($request, [
            'domain_top' => 'queryphp.com'
        ]);

        $this->assertEquals($urlDomain->make('hello/world'), 'http://www.queryphp.com/hello/world');
        $this->assertEquals($urlDomain->make('hello/world', [], 'vip'), 'http://vip.queryphp.com/hello/world');
        $this->assertEquals($urlDomain->make('hello/world', [], 'defu.vip'), 'http://defu.vip.queryphp.com/hello/world');
        $this->assertEquals($urlDomain->make('hello/world', [], '*'), 'http://queryphp.com/hello/world');
    }

    public function testSetOption()
    {
        $request = Request::createFromGlobals();
        $url = new Url($request);

        $this->assertEquals($url->make('hello/world'), '/hello/world');

        $url->setOption('domain_top', 'queryphp.cn');
        $this->assertEquals($url->make('hello/world'), 'http://www.queryphp.cn/hello/world');
    }
}
