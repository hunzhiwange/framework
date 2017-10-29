<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\support;

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
use Composer\Autoload\ClassLoader;

/**
 * psr4 自动载入规范
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2016.11.17
 * @version 1.0
 */
class psr4 implements ipsr4 {
    
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
     * namespace
     *
     * @var array
     */
    protected $arrNamespace=[];
    
    /**
     * 设置 composer
     *
     * @param \Composer\Autoload\ClassLoader $objComposer            
     * @param string $strSandboxPath    
     * @param array $arrNamespace         
     * @return void
     */
    public function __construct(ClassLoader $objComposer, $strSandboxPath = '',$arrNamespace=[]) {
        $this->objComposer = $objComposer;
        $this->strSandboxPath = $strSandboxPath;
        $this->arrNamespace = $arrNamespace;
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

        if($this->arrNamespace){
            foreach($this->arrNamespace as $strNamespace){
                if(strpos ( $strClass, $strNamespace.'\\' ) !== false && is_file(($strSandbox = $this->strSandboxPath . '/' . str_replace ( '\\', '/', substr ( $strClass, strlen($strNamespace)+1 ) ) . '.php'))){
                    require $strSandbox;
                }
            }
        }
        
        if (is_file ( ($strSandbox = $this->strSandboxPath . '/' . str_replace ( '\\', '/', $strClass ) . '.php') ))
            require $strSandbox;
    }
}
