<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\mvc\interfaces;

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
 * view 接口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.04.23
 * @version 1.0
 */
interface view {
    
    /**
     * 变量赋值
     *
     * @param mixed $mixName            
     * @param mixed $mixValue            
     * @return $this
     */
    public function assign($mixName, $mixValue = null);
    
    /**
     * 获取变量赋值
     *
     * @param string|null $sName            
     * @return mixed
     */
    public function getAssign($sName = null);
    
    /**
     * 加载视图文件
     *
     * @param string $sFile            
     * @param array $arrOption
     *            charset 编码
     *            content_type 内容类型
     *            return 是否返回
     * @return void|string
     */
    public function display($sFile = '', $arrOption = []);
}
