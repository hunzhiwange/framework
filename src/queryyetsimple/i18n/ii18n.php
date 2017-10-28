<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\i18n;

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
 * ii18n 接口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.08.07
 * @version 1.0
 */
interface ii18n {
    
    /**
     * 获取语言text
     *
     * @param string $sValue
     *            当前的语言
     * @return string
     */
    public function getText($sValue/*Argvs*/);
    
    /**
     * 获取语言text
     *
     * @param string $sValue
     *            当前的语言
     * @return string
     */
    public function __($sValue/*Argvs*/);
    
    /**
     * 添加语言包
     *
     * @param string $sI18nName
     *            语言名字
     * @param array $arrData
     *            语言包数据
     * @return void
     */
    public function addI18n($sI18nName, $arrData = []);
    
    /**
     * 自动分析语言上下文环境
     *
     * @return string
     */
    public function parseContext();
    
    /**
     * 设置当前语言包上下文环境
     *
     * @param string $sI18nName            
     * @return void
     */
    public function setContext($sI18nName);
    
    /**
     * 设置当前语言包默认上下文环境
     *
     * @param string $sI18nName            
     * @return void
     */
    public function setDefaultContext($sI18nName);
    
    /**
     * 设置 cookie 名字
     *
     * @param string $sCookieName
     *            cookie名字
     * @return void
     */
    public function setCookieName($sCookieName);
    
    /**
     * 获取当前语言包默认上下文环境
     *
     * @return string
     */
    public function getDefaultContext();
    
    /**
     * 获取当前语言包 cookie 名字
     *
     * @return string
     */
    public function getCookieName();
    
    /**
     * 获取当前语言包上下文环境
     *
     * @return string
     */
    public function getContext();
}
