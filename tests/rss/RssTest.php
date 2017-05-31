<?php
// [$QueryPHP] A PHP Framework Since 2010.10.03. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace tests\rss;

<<<queryphp
##########################################################
#   ____                          ______  _   _ ______   #
#  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
# |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
#  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
#       \__   | \___ |_|    \__  || |    | | | || |      #
#     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
#                          |___ /  Since 2010.10.03      #
##########################################################
queryphp;

use queryyetsimple\rss\rss;
use tests\testcase;

/**
 * rss 组件测试
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2017.05.31
 * @version 1.0
 */
class Rss_test extends testcase {
    
    /**
     * rss
     *
     * @var \queryyetsimple\rss\rss
     */
    private $objRss;
    
    /**
     * xml 配置项
     *
     * @var array
     */
    private $arrOption = [ 
            'title' => '官方网站',
            'link' => 'http://www.queryphp.com' 
    ];
    
    /**
     * xml 数据项
     *
     * @var array
     */
    private $arrItems = [ 
            [ 
                    'title' => '苹果',
                    'link' => 'http://www.baidu.com',
                    'comments' => '苹果是一种水果，也是现在最流行的手机提供商.',
                    'creator' => 'admin' 
            ],
            [ 
                    'title' => '梨子',
                    'link' => 'http://www.baidu.com',
                    'comments' => '梨子水分非常多，夏天相当不错.',
                    'creator' => 'admin' 
            ],
            [ 
                    'title' => '香蕉',
                    'link' => 'http://www.baidu.com',
                    'comments' => '香蕉也是一种水果，吃起来软软的很可口.',
                    'creator' => 'admin' 
            ] 
    ];
    
    /**
     * 初始化
     *
     * @return void
     */
    protected function setUp() {
        $this->objRss = new rss ( $this->arrOption );
    }
    
    /**
     * 生成 xml
     *
     * @return void
     */
    public function test() {
        $this->objRss->addItem ( $this->arrItems );
        $strXml = $this->objRss->run ( false );
        $this->assertStringStartsWith ( '<?xml version="1.0" encoding="UTF-8"?>', $strXml );
        $this->assertStringEndsWith ( '</rss>', $strXml );
    }
}
