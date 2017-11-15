<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\view;

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
 * iparser 接口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.04.23
 * @version 1.0
 */
interface iparser
{

    /**
     * 注册视图编译器
     *
     * @return $this
     */
    public function registerCompilers();

    /**
     * 注册视图分析器
     *
     * @return $this
     */
    public function registerParsers();

    /**
     * 执行编译
     *
     * @param string $sFile
     * @param string $sCachePath
     * @param boolean $bReturn
     * @return string
     */
    public function doCombile($sFile, $sCachePath, $bReturn = false);

    /**
     * code 编译编码，后还原
     *
     * @param string $sContent
     * @return string
     */
    public static function revertEncode($sContent);

    /**
     * tagself 编译编码，后还原
     *
     * @param string $sContent
     * @return string
     */
    public static function globalEncode($sContent);
}
