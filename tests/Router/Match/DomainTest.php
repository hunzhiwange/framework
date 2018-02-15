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
namespace tests\Router\Match;

use tests\testcase;
use Queryyetsimple\Router\Router;
use Queryyetsimple\Router\Match\Domain;

/**
 * match.domain 组件测试
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2018.02.13
 * @version 1.0
 */
class DomainTest extends testcase
{

    /**
     * 初始化
     *
     * @return void
     */
    protected function setUp()
    {
        $this->router = app('router');
        $this->router->option('router_domain_on', true);
        $this->router->option('router_domain_top', 'queryphp.cn');
        $this->router->domain('{domain}', 'home://index/index');
        $this->router->development(true);

        $this->request = app('request');

        $this->domain = new Domain();
    }

    /**
     * pathinfo url 生成
     *
     * @return void
     */
    public function testPathinfoUrl()
    {
        // ddd($this->domain->matche($this->router, $this->request)); 
    }

    /**
     * 自定义 url
     *
     * @return void
     */
    public function testCustomUrl()
    {
    }

    /**
     * normal url
     *
     * @return void
     */
    public function testNormalUrl()
    {
    }
}
