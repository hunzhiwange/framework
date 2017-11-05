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

use RuntimeException;
use queryyetsimple\support\helper;
use queryyetsimple\filesystem\fso;

/**
 * 语言包工具类
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2016.11.25
 * @version 1.0
 */
class tool {
    
    /**
     * 保存数据到 JS 的缓存文件
     *
     * @param string|array $mixFiles
     *            文件地址
     * @param string $sCacheFile
     *            缓存目录
     * @param string $sI18nSet
     *            语言上下文环境
     * @author 小牛
     * @since 2016.11.27
     * @return array
     */
    public static function saveToJs($mixFiles, $sCacheFile, $sI18nSet) {
        // 读取语言包数据
        if (is_string ( $mixFiles )) {
            $mixFiles = ( array ) $mixFiles;
        }
        $arrTexts = static::parseMoData ( $mixFiles );
        
        $sDir = dirname ( $sCacheFile );
        if (! is_dir ( $sDir )) {
            fso::createDirectory ( $sDir );
        }
        // 防止空数据无法写入
        $arrTexts ['Query Yet Simple'] = 'Query Yet Simple';
        if (! file_put_contents ( $sCacheFile, '/* ' . date ( 'Y-m-d H:i:s' ) . ' */;$(function(){$.fn.queryphp(\'i18nPackage\',\'' . $sI18nSet . '\',' . json_encode ( $arrTexts, 256 ) . ');});' )) {
            throw new RuntimeException ( sprintf ( 'Dir %s do not have permission.', $sCacheDir ) );
        }
        
        return $arrTexts;
    }
    
    /**
     * 保存数据到 PHP 的缓存文件
     *
     * @param string|array $mixFiles
     *            文件地址
     * @param string $CacheFile
     *            缓存目录
     * @author 小牛
     * @since 2016.11.27
     * @return array
     */
    public static function saveToPhp($mixFiles, $sCacheFile) {
        // 读取语言包数据
        if (is_string ( $mixFiles )) {
            $mixFiles = ( array ) $mixFiles;
        }
        $arrTexts = static::parseMoData ( $mixFiles );
        
        $sDir = dirname ( $sCacheFile );
        if (! is_dir ( $sDir )) {
            fso::createDirectory ( $sDir );
        }
        // 防止空数据无法写入
        $arrTexts ['Query Yet Simple'] = 'Query Yet Simple';
        if (! file_put_contents ( $sCacheFile, '<?php return ' . var_export ( $arrTexts, true ) . '; ?>' )) {
            throw new RuntimeException ( sprintf ( 'Dir %s do not have permission.', $sDir ) );
        }
        file_put_contents ( $sCacheFile, '<?php /* ' . date ( 'Y-m-d H:i:s' ) . ' */ ?>' . PHP_EOL . php_strip_whitespace ( $sCacheFile ) );
        
        return $arrTexts;
    }
    
    /**
     * 分析目录中的 PHP 语言包包含的文件
     *
     * @param string|array $mixI18nDir
     *            文件地址
     * @author 小牛
     * @since 2016.11.27
     * @return array
     */
    public static function findMoFile($mixI18nDir) {
        if (is_string ( $mixI18nDir )) {
            $mixI18nDir = ( array ) $mixI18nDir;
        }
        
        // 返回结果
        $arrFiles = [ ];
        foreach ( $mixI18nDir as $sDir ) {
            if (! is_dir ( $sDir )) {
                continue;
            }
            
            $arrFiles = array_merge ( $arrFiles, fso::lists ( $sDir, 'file', true, [ ], [ 
                    'po' 
            ] ) );
        }
        
        return $arrFiles;
    }
    
    /**
     * 分析 mo 文件语言包数据
     *
     * @param string|array $mixI18nFile
     *            文件地址
     * @author 小牛
     * @since 2016.11.25
     * @return array
     */
    protected static function parseMoData($mixI18nFile) {
        if (is_string ( $mixI18nFile )) {
            $mixI18nFile = ( array ) $mixI18nFile;
        }
        
        return (new mo ())->readToArray ( $mixI18nFile );
    }
}
