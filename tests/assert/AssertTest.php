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
 * (c) 2010-2017 http://queryphp.com All rights reserved.
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace tests\assert;

use tests\testcase;
use queryyetsimple\assert\assert;

/**
 * assert 组件测试
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.05.09
 * @version 1.0
 */
class AssertTest extends testcase
{
    
    /**
     * 开启断言
     *
     * @return void
     */
    protected function setUp()
    {
        assert::open(true);
    }
    
    /**
     * test
     *
     * @return void
     */
    public function testFirst()
    {
        $this->assertEquals(true, assert::string('hello'));
        $this->assertEquals(true, assert::boolean(true));
        $this->assertEquals(true, assert::null(null));
    }
}
