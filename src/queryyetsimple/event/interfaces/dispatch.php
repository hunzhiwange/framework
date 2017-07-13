<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\event\interfaces;

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
 * dispatch 接口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.07.12
 * @version 1.0
 */
interface dispatch {
    
    /**
     * 执行一个事件
     *
     * @param string|object $mixEvent            
     * @return void
     */
    public function run($mixEvent /* args */ );
    
    /**
     * 注册监听器
     *
     * @param string|array $mixEvent            
     * @param mixed $mixListener            
     * @return void
     */
    public function listener($mixEvent, $mixListener);
    
    /**
     * 获取一个监听器
     *
     * @param string $strEvent            
     * @return array
     */
    public function getListener($strEvent);
    
    /**
     * 判断监听器是否存在
     *
     * @param string $strEvent            
     * @return bool
     */
    public function hasListener($strEvent);
    
    /**
     * 删除一个事件所有监听器
     *
     * @param string $strEvent            
     * @return void
     */
    public function deleteListener($strEvent);
}
