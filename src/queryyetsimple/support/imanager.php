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
 * imanager 接口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.09.07
 * @version 1.0
 */
interface manager {
    
    /**
     * 连接 connect 并返回连接对象
     *
     * @param array|string $mixOption            
     * @return object
     */
    public function connect($mixOption = []);
    
    /**
     * 重新连接
     *
     * @param array|string $mixOption            
     * @return object
     */
    public function reconnect($mixOption = []);
    
    /**
     * 删除连接
     *
     * @param array|string $mixOption            
     * @return void
     */
    public function disconnect($mixOption = []);
    
    /**
     * 取回所有连接
     *
     * @return object[]
     */
    public function getConnects();
    
    /**
     * 返回默认驱动
     *
     * @return string
     */
    public function getDefaultDriver();
    
    /**
     * 设置默认驱动
     *
     * @param string $strName            
     * @return void
     */
    public function setDefaultDriver($strName);
}
