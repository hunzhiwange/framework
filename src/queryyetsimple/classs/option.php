<?php
// [$QueryPHP] A PHP Framework Since 2010.10.03. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\classs;

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
 * 类配置复用
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2017.04.13
 * @version 1.0
 */
trait option {
    
    /**
     * 修改单个配置
     *
     * @param string $strName            
     * @param mixed $mixValue            
     * @return void
     */
    public function option($strName, $mixValue) {
        $this->arrOption [$strName] = $mixValue;
    }
    
    /**
     * 修改多个配置
     *
     * @param string $strName            
     * @param mixed $mixValue            
     * @return array
     */
    public function options($arrOption = []) {
        if (! $arrOption)
            return;
        foreach ( ( array ) $arrOption as $strName => $mixValue )
            $this->option ( $strName, $mixValue );
    }
    
    /**
     * 获取单个配置
     *
     * @param string $strName            
     * @param mixed $mixDefault            
     * @return mixed
     */
    public function getOption($strName, $mixDefault = null) {
        return isset ( $this->arrOption [$strName] ) ? $this->arrOption [$strName] : $mixDefault;
    }
    
    /**
     * 获取所有配置
     *
     * @param array $arrOption            
     * @return mixed
     */
    public function getOptions($arrOption = []) {
        return $arrOption ? array_merge ( $this->arrOption, $arrOption ) : $this->arrOption;
    }
}
