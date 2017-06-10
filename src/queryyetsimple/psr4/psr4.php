<?php
// [$QueryPHP] A PHP Framework Since 2010.10.03. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\psr4;

use RuntimeException;
use queryyetsimple\classs\faces2;
use Composer\Autoload\ClassLoader;
use queryyetsimple\support\interfaces\container;

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
 * psr4 自动载入规范
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2016.11.17
 * @version 1.0
 */
class psr4 {
    
    /**
     * 设置 composer
     *
     * @var \Composer\Autoload\ClassLoader
     */
    private static $objComposer;
    
    /**
     * 设置 composer
     *
     * @param \Composer\Autoload\ClassLoader $objComposer            
     * @return void
     */
    public static function composer(ClassLoader $objComposer) {
        static::$objComposer = $objComposer;
    }
    
    /**
     * 获取 composer
     *
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getComposer() {
        return static::$objComposer;
    }
    
    /**
     * 导入一个目录中命名空间结构
     *
     * @param string $sNamespace
     *            命名空间名字
     * @param string $sPackage
     *            命名空间路径
     * @return void
     */
    public static function import($sNamespace, $sPackage) {
        if (! is_dir ( $sPackage )) {
            return;
        }
        $sPackagePath = realpath ( $sPackage );
        static::getComposer ()->setPsr4 ( $sNamespace . '\\', $sPackagePath );
    }
    
    /**
     * 获取命名空间路径
     *
     * @param string $sNamespace            
     * @return string|null
     */
    public static function getNamespace($sNamespace) {
        $arrNamespace = explode ( '\\', $sNamespace );
        $arrPrefix = static::getComposer ()->getPrefixesPsr4 ();
        if (! isset ( $arrPrefix [$arrNamespace [0] . '\\'] ))
            return null;
        $arrNamespace [0] = $arrPrefix [$arrNamespace [0] . '\\'] [0];
        return implode ( '\\', $arrNamespace );
    }
    
    /**
     * 根据命名空间取得文件路径
     *
     * @param string $strFile            
     * @return string
     */
    public static function getFilePath($strFile) {
        if (($strNamespace = static::getNamespace ( $strFile ))) {
            return $strNamespace . '.php';
        } else {
            return $strFile . '.php';
        }
    }
    
    /**
     * 框架自动载入
     *
     * @param string $strClass            
     * @return void
     */
    public static function autoload($strClass) {
        // 实现沙盒
        if (strpos ( $strClass, 'queryyetsimple\\' ) !== false) {
            $strSandbox = dirname ( __DIR__ ) . '/bootstrap/sandbox/' . ltrim ( $strClass, 'queryyetsimple\\' ) . '.php';
            if (is_file ( $strSandbox )) {
                require $strSandbox;
            } else {
                throw new RuntimeException ( sprintf ( 'Sandbox class %s cannot find.', $strSandbox ) );
            }
        }
    }
    
    /**
     * 容器注入门面
     *
     * @param \queryyetsimple\support\interfaces\container $objProject            
     * @return void
     */
    public static function faces(container $objProject) {
        faces2::setProjectContainer ( $objProject );
    }
}
