<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\view\interfaces;

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

use queryyetsimple\view\interfaces\parser;
use queryyetsimple\cookie\interfaces\cookie;

/**
 * theme 接口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.04.23
 * @version 1.0
 */
interface theme {
    
    /**
     * 视图分析器
     *
     * @param \queryyetsimple\view\parser $objParse            
     * @return $this
     */
    public function registerParser(parser $objParse);
    
    /**
     * 注册 cookie
     *
     * @param \queryyetsimple\cookie\interfaces\cookie $objCookie            
     * @return $this
     */
    public function registerCookie(cookie $objCookie);
    
    /**
     * 加载视图文件
     *
     * @param string $sFile
     *            视图文件地址
     * @param boolean $bDisplay
     *            是否显示
     * @param string $strExt
     *            后缀
     * @param string $sTargetCache
     *            主模板缓存路径
     * @param string $sMd5
     *            源文件地址 md5 标记
     * @return string
     */
    public function display($sFile, $bDisplay = true, $strExt = '', $sTargetCache = '', $sMd5 = '');
    
    /**
     * 设置模板变量
     *
     * @param mixed $mixName            
     * @param mixed $mixValue            
     * @return void
     */
    public function setVar($mixName, $mixValue = null);
    
    /**
     * 获取变量值
     *
     * @param string|null $sName            
     * @return mixed
     */
    public function getVar($sName = null);
    
    /**
     * 获取编译路径
     *
     * @param string $sFile            
     * @return string
     */
    public function getCachePath($sFile);
    
    /**
     * 自动分析视图上下文环境
     *
     * @param string $strThemePath            
     * @return void
     */
    public function parseContext($strThemePath);
}
