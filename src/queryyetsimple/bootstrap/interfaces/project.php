<?php
// [$QueryPHP] A PHP Framework Since 2010.10.03. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\bootstrap\interfaces;

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

use Composer\Autoload\ClassLoader;
use queryyetsimple\support\interfaces\container;

/**
 * project 接口
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2017.04.23
 * @version 1.0
 */
interface project extends container {
    
    /**
     * 执行项目
     *
     * @return void
     */
    public function run();
    
    /**
     * 返回项目
     *
     * @param \Composer\Autoload\ClassLoader $objComposer            
     * @param array $arrOption            
     * @return $this
     */
    public static function bootstrap(ClassLoader $objComposer = null, $arrOption = []);
    
    /**
     * 程序版本
     *
     * @return number
     */
    public function version();
    
    /**
     * 注册应用提供者
     *
     * @param array $arrProvider            
     * @param array $arrProviderCache            
     * @return $this
     */
    public function registerAppProvider($arrProvider, $arrProviderCache);
    
    /**
     * 基础路径
     *
     * @return string
     */
    public function path();
    
    /**
     * 应用路径
     *
     * @return string
     */
    public function pathApplication();
    
    /**
     * 公共路径
     *
     * @return string
     */
    public function pathCommon();
    
    /**
     * 运行路径
     *
     * @return string
     */
    public function pathRuntime();
    
    /**
     * 资源路径
     *
     * @return string
     */
    public function pathPublic();
    
    /**
     * public url
     *
     * @return string
     */
    public function urlPublic();
    
    /**
     * root url
     *
     * @return string
     */
    public function urlRoot();
    
    /**
     * enter url
     *
     * @return string
     */
    public function urlEnter();
}
