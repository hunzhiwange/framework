<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\cache;

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
 * cache 接口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.04.23
 * @version 1.0
 */
interface icache {
    
    /**
     * 连接缓存并返回连接对象
     *
     * @param array $arrOption            
     * @return \queryyetsimple\acache
     */
    public function connect($arrOption = []);
    
    /**
     * 创建一个缓存仓库
     *
     * @param \queryyetsimple\cache\iconnect $objCache            
     * @return \queryyetsimple\cache\repository
     */
    public function repository(iconnect $objCache);
    
    /**
     * 返回默认连接
     *
     * @return string
     */
    public function getDefaultDriver();
    
    /**
     * 设置默认连接
     *
     * @param string $strName            
     * @return void
     */
    public function setDefaultDriver($strName);
}
