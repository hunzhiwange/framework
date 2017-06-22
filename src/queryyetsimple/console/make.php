<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\console;

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
use queryyetsimple\console\command;
use queryyetsimple\filesystem\filesystem;

/**
 * 生成器基类
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.05.02
 * @version 1.0
 */
abstract class make extends command {
    
    /**
     * 创建类型
     *
     * @var string
     */
    protected $strMakeType;
    
    /**
     * 文件保存路径
     *
     * @var string
     */
    protected $strSaveFilePath;
    
    /**
     * 模板路径
     *
     * @var string
     */
    protected $strTemplatePath;
    
    /**
     * 模板源码
     *
     * @var string
     */
    protected $strTemplateSource;
    
    /**
     * 保存的模板结果
     *
     * @var string
     */
    protected $strTemplateResult;
    
    /**
     * 自定义替换
     *
     * @var array
     */
    protected $arrCustomReplaceKeyValue = [ ];
    
    /**
     * 响应命令
     *
     * @return void
     */
    public function handle() {
        // 替换模板变量
        $this->replaceTemplateSource ();
        
        // 保存文件
        $this->saveTemplateResult ();
        
        // 保存成功输出消息
        $this->info ( sprintf ( '%s <%s> created successfully.', $this->getMakeType (), $this->argument ( 'name' ) ) );
        $this->comment ( $this->formatFile ( $this->getSaveFilePath () ) );
    }
    
    /**
     * 替换模板变量
     *
     * @return string
     */
    protected function replaceTemplateSource() {
        // 解析模板源码
        $this->parseTemplateSource ();
        
        // 获取替换变量
        $arrSourceAndReplace = $this->parseSourceAndReplace ();
        
        // 执行替换
        $strTemplateSource = str_replace ( $arrSourceAndReplace [0], $arrSourceAndReplace [1], $this->getTemplateSource () ); // 第一替换基本变量
        $this->strTemplateResult = str_replace ( $arrSourceAndReplace [0], $arrSourceAndReplace [1], $strTemplateSource ); // 第一替换基本变量中的变量
    }
    
    /**
     * 保存模板
     *
     * @return void
     */
    protected function saveTemplateResult() {
        $strSaveFilePath = $this->getSaveFilePath ();
        if (! is_dir ( dirname ( $strSaveFilePath ) )) {
            filesystem::createDirectory ( dirname ( $strSaveFilePath ) );
        }
        if (is_file ( $strSaveFilePath )) {
            throw new RuntimeException ( 'File is already exits.' . PHP_EOL . $this->formatFile ( $this->getSaveFilePath () ) );
        }
        if (! file_put_contents ( $strSaveFilePath, $this->getTemplateResult () )) {
            throw new RuntimeException ( 'Can not write file.' . PHP_EOL . $this->formatFile ( $this->getSaveFilePath () ) );
        }
    }
    
    /**
     * 获取模板编译结果
     *
     * @return string
     */
    protected function getTemplateResult() {
        return $this->strTemplateResult;
    }
    
    /**
     * 分析模板源码
     *
     * @return void
     */
    protected function parseTemplateSource() {
        $strTemplateSource = $this->getTemplatePath () . '/' . pathinfo ( str_replace ( ':', '.', $this->getName () ), PATHINFO_EXTENSION );
        if (! is_file ( $strTemplateSource )) {
            throw new RuntimeException ( 'Template not found.' . PHP_EOL . $this->formatFile ( $strTemplateSource ) );
        }
        $this->strTemplateSource = file_get_contents ( $strTemplateSource );
    }
    
    /**
     * 获取模板源码
     *
     * @return string
     */
    protected function getTemplateSource() {
        return $this->strTemplateSource;
    }
    
    /**
     * 分析变量替换
     *
     * @return array
     */
    protected function parseSourceAndReplace() {
        $arrReplaceKeyValue = array_merge ( $this->getDefaultReplaceKeyValue (), option ( 'console\template' ) );
        $arrSourceKey = array_map ( function ($strItem) {
            return '{{' . $strItem . '}}';
        }, array_keys ( $arrReplaceKeyValue ) );
        $arrReplace = array_values ( $arrReplaceKeyValue );
        return [ 
                $arrSourceKey,
                $arrReplace 
        ];
    }
    
    /**
     * 取得系统的替换变量
     *
     * @return array
     */
    protected function getDefaultReplaceKeyValue() {
        return array_merge ( [ 
                'namespace' => $this->getNamespace (),
                'file_name' => $this->argument ( 'name' ),
                'date_y' => date ( 'Y' ) 
        ], $this->getCustomReplaceKeyValue () ); // 日期年
    }
    
    /**
     * 设置文件保存路径
     *
     * @param string $strSaveFilePath            
     * @return void
     */
    protected function setSaveFilePath($strSaveFilePath) {
        $this->strSaveFilePath = $strSaveFilePath;
    }
    
    /**
     * 读取文件保存路径
     *
     * @return string
     */
    protected function getSaveFilePath() {
        return $this->strSaveFilePath;
    }
    
    /**
     * 获取命名空间路径
     *
     * @return string
     */
    protected function getNamespacePath() {
        if (($strNamespacePath = $this->project ()->make ( 'psr4' )->namespaces ( $this->getNamespace () ) . '/') != '/') {
            $strNamespacePath = $this->project ()->path_application . '/' . $this->getNamespace () . '/';
        }
        return $strNamespacePath;
    }
    
    /**
     * 分析命名空间
     *
     * @return void
     */
    protected function parseNamespace() {
        $strNamespace = $this->option ( 'namespace' );
        if (empty ( $strNamespace )) {
            $strNamespace = option ( 'default_app' );
        }
        $this->setNamespace ( $strNamespace );
    }
    
    /**
     * 设置命名空间
     *
     * @param string $strNamespace            
     * @return void
     */
    protected function setNamespace($strNamespace) {
        $this->strNamespace = $strNamespace;
    }
    
    /**
     * 读取命名空间
     *
     * @return string
     */
    protected function getNamespace() {
        return $this->strNamespace;
    }
    
    /**
     * 设置创建类型
     *
     * @param string $strMakeType            
     * @return void
     */
    protected function setMakeType($strMakeType) {
        $this->strMakeType = $strMakeType;
    }
    
    /**
     * 读取创建类型
     *
     * @return string
     */
    protected function getMakeType() {
        return $this->strMakeType;
    }
    
    /**
     * 设置模板文件路径
     *
     * @param string $strTemplatePath            
     * @return void
     */
    protected function setTemplatePath($strTemplatePath) {
        $this->strTemplatePath = $strTemplatePath;
    }
    
    /**
     * 读取模板文件路径
     *
     * @return string
     */
    protected function getTemplatePath() {
        return $this->strTemplatePath;
    }
    
    /**
     * 设置自定义变量替换
     *
     * @param mixed $mixKey            
     * @param string $strValue            
     * @return void
     */
    protected function setCustomReplaceKeyValue($mixKey, $strValue) {
        if (is_array ( $mixKey )) {
            $this->arrCustomReplaceKeyValue = array_merge ( $this->arrCustomReplaceKeyValue, $mixKey );
        } else {
            $this->arrCustomReplaceKeyValue [$mixKey] = $strValue;
        }
    }
    
    /**
     * 读取自定义变量替换
     *
     * @param string $strMakeType            
     * @return array
     */
    protected function getCustomReplaceKeyValue() {
        return $this->arrCustomReplaceKeyValue;
    }
    
    /**
     * 格式化文件路径
     *
     * @param string $strFile            
     * @return array
     */
    protected function formatFile($strFile) {
        return filesystem::tidyPathLinux ( $strFile );
    }
}  