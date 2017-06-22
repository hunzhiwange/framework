<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\psr4;

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

use RuntimeException;
use queryyetsimple\classs\faces;
use Composer\Autoload\ClassLoader;
use queryyetsimple\support\interfaces\container;
use queryyetsimple\psr4\interfaces\psr4 as interfaces_psr4;

/**
 * psr4 自动载入规范
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2016.11.17
 * @version 1.0
 */
class psr4 implements interfaces_psr4 {
    
    /**
     * 设置 composer
     *
     * @var \Composer\Autoload\ClassLoader
     */
    protected $objComposer;
    
    /**
     * 沙盒路径
     *
     * @var string
     */
    protected $strSandboxPath;
    
    /**
     * 设置 composer
     *
     * @param \queryyetsimple\support\interfaces\container $objContainer            
     * @param \Composer\Autoload\ClassLoader $objComposer            
     * @param string $strSandboxPath            
     * @return void
     */
    public function __construct(container $objContainer, ClassLoader $objComposer, $strSandboxPath = '') {
        $this->objComposer = $objComposer;
        $this->strSandboxPath = $strSandboxPath;
        faces::setProjectContainer ( $objContainer );
    }
    
    /**
     * 获取 composer
     *
     * @return \Composer\Autoload\ClassLoader
     */
    public function composer() {
        return $this->objComposer;
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
    public function import($sNamespace, $sPackage) {
        if (! is_dir ( $sPackage )) {
            return;
        }
        $sPackagePath = realpath ( $sPackage );
        $this->composer ()->setPsr4 ( $sNamespace . '\\', $sPackagePath );
    }
    
    /**
     * 获取命名空间路径
     *
     * @param string $sNamespace            
     * @return string|null
     */
    public function namespaces($sNamespace) {
        $arrNamespace = explode ( '\\', $sNamespace );
        $arrPrefix = $this->composer ()->getPrefixesPsr4 ();
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
    public function file($strFile) {
        if (($strNamespace = $this->namespaces ( $strFile ))) {
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
    public function autoload($strClass) {
        if (! $this->strSandboxPath)
            return;
        
        if (strpos ( $strClass, 'queryyetsimple\\' ) !== false || strpos ( $strClass, 'qys\\' ) !== false) {
            if (is_file ( ($strSandbox = $this->strSandboxPath . '/' . str_replace ( '\\', '/', substr ( $strClass, strpos ( $strClass, 'queryyetsimple\\' ) !== false ? 15 : 4 ) ) . '.php') ))
                require $strSandbox;
        }
        
        if (is_file ( ($strSandbox = $this->strSandboxPath . '/' . str_replace ( '\\', '/', $strClass ) . '.php') ))
            require $strSandbox;
    }
}
