<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\cookie\interfaces;

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
 * cookie 接口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.04.23
 * @version 1.0
 */
interface cookie {
    
    /**
     * 设置 COOKIE
     *
     * @param string $sName            
     * @param string $mixValue            
     * @param array $arrOption            
     * @return void
     */
    public function set($sName, $mixValue = '', array $arrOption = []);
    
    /**
     * 获取 cookie
     *
     * @param string $sName            
     * @param mixed $mixDefault            
     * @param array $arrOption            
     * @return mixed
     */
    public function get($sName, $mixDefault = null, array $arrOption = []);
    
    /**
     * 删除 cookie
     *
     * @param string $sName            
     * @param array $arrOption            
     * @return void
     */
    public function delete($sName, array $arrOption = []);
    
    /**
     * 清空 cookie
     *
     * @param boolean $bOnlyPrefix            
     * @param array $arrOption            
     * @return void
     */
    public function clear($bOnlyPrefix = true, array $arrOption = []);
}
