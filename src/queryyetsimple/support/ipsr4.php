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
 * (c) 2010-2017 http://queryphp.com All rights reserved.
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace queryyetsimple\support;

/**
 * ipsr4 接口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.04.23
 * @version 1.0
 */
interface ipsr4
{
    
    /**
     * 获取 composer
     *
     * @return \Composer\Autoload\ClassLoader
     */
    public function composer();
    
    /**
     * 导入一个目录中命名空间结构
     *
     * @param string $sNamespace 命名空间名字
     * @param string $sPackage 命名空间路径
     * @return void
     */
    public function import($sNamespace, $sPackage);
    
    /**
     * 获取命名空间路径
     *
     * @param string $sNamespace
     * @return string|null
     */
    public function namespaces($sNamespace);
    
    /**
     * 根据命名空间取得文件路径
     *
     * @param string $strFile
     * @return string
     */
    public function file($strFile);
    
    /**
     * 框架自动载入
     *
     * @param string $strClass
     * @return void
     */
    public function autoload($strClass);
}
