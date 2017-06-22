<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\log\interfaces;

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

use queryyetsimple\log\interfaces\connect;

/**
 * log 接口
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2017.04.11
 * @version 1.0
 */
interface log {
    
    /**
     * 连接 log 并返回连接对象
     *
     * @param array|string $mixOption            
     * @return \queryyetsimple\log\store
     */
    public function connect($mixOption = []);
    
    /**
     * 创建 log store
     *
     * @param \queryyetsimple\log\interfaces\connect $oConnect            
     * @return \queryyetsimple\log\store
     */
    public function store(connect $oConnect);
    
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
