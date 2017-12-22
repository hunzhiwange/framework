<?php
/*
 * This file is part of the ************************ package.
 * ##########################################################
 * #   ____                          ______  _   _ ______   #
 * #  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
 * # |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
 * #  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
 * #       \__   | \___ |_|    \__  || |    | | | || |      #
 * #     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
 * #                          |___ /  Since 2010.10.03      #
 * ##########################################################
 * 
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace queryyetsimple\bootstrap;

use queryyetsimple\{
    support\provider,
    support\icontainer
};

/**
 * iproject 接口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.04.23
 * @version 1.0
 */
interface iproject extends icontainer
{
    
    /**
     * QueryPHP 版本
     *
     * @var string
     */
    const VERSION = '4.0.0';
    
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
    public static function singletons($objComposer = null, $arrOption = []);
    
    /**
     * 程序版本
     *
     * @return number
     */
    public function version();
    
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
     * 应用路径
     *
     * @return string
     */
    public function pathApplicationCurrent();
    
    /**
     * 取得应用缓存目录
     *
     * @param string $strType
     * @return string
     */
    public function pathApplicationCache($strType);
    
    /**
     * 取得应用目录
     *
     * @param string $strType
     * @return string
     */
    public function pathApplicationDir($strType);
    
    /**
     * 是否开启 debug
     *
     * @return boolean
     */
    public function debug();
    
    /**
     * 是否为开发环境
     *
     * @return string
     */
    public function development();
    
    /**
     * 运行环境
     *
     * @return boolean
     */
    public function environment();
    
    /**
     * 是否为 API
     *
     * @return boolean
     */
    public function api();
    
    /**
     * 是否为 Console
     *
     * @return boolean
     */
    public function console();
    
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
    
    /**
     * 创建服务提供者
     *
     * @param string $strProvider
     * @return \queryyetsimple\support\provider
     */
    public function makeProvider($strProvider);
    
    /**
     * 执行 bootstrap
     *
     * @param \queryyetsimple\support\provider $objProvider
     * @return void
     */
    public function callProviderBootstrap(provider $objProvider);
}
