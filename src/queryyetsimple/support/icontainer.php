<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\support;

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

/**
 * icontainer 接口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.04.13
 * @version 1.0
 */
interface icontainer {
    
    /**
     * 注册到容器
     *
     * @param mixed $mixFactoryName            
     * @param mixed $mixFactory            
     * @return void
     */
    public function register($mixFactoryName, $mixFactory = null);
    
    /**
     * 强制注册为实例，存放数据
     *
     * @param string $strFactoryName            
     * @param mixed $mixFactory            
     * @return void
     */
    public function instance($strFactoryName, $mixFactory);
    
    /**
     * 注册单一实例
     *
     * @param string $strFactoryName            
     * @param mixed $mixFactory            
     * @return void
     */
    public function singleton($mixFactoryName, $mixFactory = null);
    
    /**
     * 设置别名
     *
     * @param array|string $mixAlias            
     * @param string|null|array $mixValue            
     * @return void
     */
    public function alias($mixAlias, $mixValue = null);
    
    /**
     * 分组注册
     *
     * @param string $strGroupName            
     * @param mixed $mixGroupData            
     * @return void
     */
    public function group($strGroupName, $mixGroupData);
    
    /**
     * 分组制造
     *
     * @param string $strGroupName            
     * @param array $arrArgs            
     * @return array
     */
    public function groupMake($strGroupName, array $arrArgs = []);
    
    /**
     * 生产产品
     *
     * @param string $strFactoryName            
     * @param array $arrArgs            
     * @return object|false
     */
    public function make($strFactoryName, array $arrArgs = []);
    
    /**
     * 实例回调自动注入并返回结果 (数组参数)
     *
     * @param callable $calClass            
     * @param array $arrArgs            
     * @return mixed
     */
    public function call($calClass, array $arrArgs = []);
}
