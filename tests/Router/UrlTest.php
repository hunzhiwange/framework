<?php
/*
 * This file is part of the ************************ package.
 * ##########################################################
 * #   ____                          ______  _   _ ______   #
 * #  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
 * # |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
 * #  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
 * #       \__   | \___ |_|    \__  || |    | | | || |      #
 * #     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
 * #                          |___ /  Since 2010.10.03      #
 * ##########################################################
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace tests\Router;

use tests\testcase;
use Queryyetsimple\Router\Url;

/**
 * url 组件测试
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2018.02.06
 * @version 1.0
 */
class UrlTest extends testcase
{

    /**
     * 初始化
     *
     * @return void
     */
    protected function setUp()
    {
        $this->url = new Url();

        $this->urlDomain = new Url([
            'router_domain_top' => 'queryphp.com'
        ]);

        $this->urlNormal = new Url([
            'model' => 'default'
        ]);

        $this->urlNormalDomain = new Url([
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
        $this->assertEquals($this->url->make('test/hello'), '/test/hello.html'); 

        $this->assertEquals($this->url->make('test/hello?arg1=1&arg2=3'), '/test/hello/arg1/1/arg2/3.html'); 

        $this->assertEquals($this->url->make('myapp://hello/world' ,['id'=>5,'name'=>'yes']), '/myapp/hello/world/id/5/name/yes.html'); 

        $this->assertEquals($this->url->make('myapp://test'), '/myapp/test.html'); 

        $this->assertEquals($this->url->make('hello/world', [], ['normal' => true]), '?app=home&c=hello&a=world'); 

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
        $this->assertEquals($this->url->make('/hello-world'), '/hello-world.html'); 

        $this->assertEquals($this->url->make('/new-{id}-{name}', ['id' => 5, 'name' => 'tom', 'arg1' => '5']), '/new-5-tom/arg1/5.html'); 
    }

    /**
     * normal url
     *
     * @return void
     */
    public function testNormalUrl()
    {
        $this->assertEquals($this->urlNormal->make('test/hello'), '?c=test&a=hello');

        $this->assertEquals($this->urlNormal->make('test/hello?arg1=1&arg2=3'), '?c=test&a=hello&arg1=1&arg2=3');

        $this->assertEquals($this->urlNormal->make('test/hello?arg1=1&arg2=3', ['foo' => 'bar']), '?c=test&a=hello&arg1=1&arg2=3&foo=bar');

        $this->assertEquals($this->urlNormal->make('group://test/hello?arg1=1&arg2=3', ['foo' => 'bar']), '?app=group&c=test&a=hello&arg1=1&arg2=3&foo=bar');

        $this->assertEquals($this->urlNormalDomain->make('group://test/hello?arg1=1&arg2=3', ['foo' => 'bar']), 'http://www.queryphp.com?app=group&c=test&a=hello&arg1=1&arg2=3&foo=bar');
    }
}
