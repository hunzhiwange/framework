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
 * controller 接口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.04.23
 * @version 1.0
 */
interface controller {
    
    /**
     * 返回父控制器
     *
     * @param \queryyetsimple\mvc\interfaces\view $objView            
     * @return $this
     */
    public function setView($objView);
    
    /**
     * 返回父控制器
     *
     * @param \queryyetsimple\router\router $objRouter            
     * @return $this
     */
    public function setRouter($objRouter);
    
    /**
     * 执行子方法器
     *
     * @param string $sActionName
     *            方法名
     * @return void
     */
    public function action($sActionName);
    
    /**
     * 赋值
     *
     * @param 变量或变量数组集合 $Name            
     * @param mixed $mixValue            
     * @return $this
     */
    public function assign($Name, $mixValue = null);
    
    /**
     * 取回赋值
     *
     * @param 变量名字 $sName            
     * @return mixed
     */
    public function getAssign($sName);
    
    /**
     * 加载视图文件
     *
     * @param string $sThemeFile            
     * @param array $in
     *            charset 编码
     *            content_type 类型
     *            return 是否返回 html 返回而不直接输出
     * @return mixed
     */
    public function display($sThemeFile = '', $arrOption = []);
}
