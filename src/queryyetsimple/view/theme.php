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

use InvalidArgumentException;
use queryyetsimple\view\interfaces\parser;
use queryyetsimple\cookie\interfaces\cookie;
use queryyetsimple\classs\option as classs_option;
use queryyetsimple\view\interfaces\theme as interfaces_theme;

/**
 * 模板处理类
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2016.11.18
 * @version 1.0
 */
class theme implements interfaces_theme {
    
    use classs_option;
    
    /**
     * 视图分析器
     *
     * @var \queryyetsimple\view\parser
     */
    protected $objParse;
    
    /**
     * cookie 处理
     *
     * @var \queryyetsimple\cookie\interfaces\cookie
     */
    protected $objCookie;
    
    /**
     * 主题参数名
     *
     * @var string
     */
    const ARGS = '~@theme';
    
    /**
     * 变量值
     *
     * @var array
     */
    protected $arrVar = [ ];
    
    /**
     * 配置
     *
     * @todo 验证缓存模板路径是否存在
     * @var array
     */
    protected $arrOption = [ 
            'app_debug' => true,
            'app_name' => 'home',
            'controller_name' => 'index',
            'action_name' => 'index',
            'controlleraction_depr' => '_',
            'theme_name' => '',
            'theme_path' => '',
            'theme_path_default' => '',
            'suffix' => '.html',
            'theme_cache_path' => '',
            'cache_children' => false,
            'switch' => true,
            'default' => 'default',
            'cookie_app' => false 
    ];
    
    /**
     * 构造函数
     *
     * @param \queryyetsimple\view\parser $objParse            
     * @param \queryyetsimple\cookie\interfaces\cookie $objCookie            
     * @param array $arrOption            
     * @return void
     */
    public function __construct(parser $objParse, cookie $objCookie, array $arrOption = []) {
        $this->objParse = $objParse;
        $this->objCookie = $objCookie;
        $this->options ( $arrOption );
    }
    
    /**
     * 加载视图文件
     *
     * @param string $sFile
     *            视图文件地址
     * @param boolean $bDisplay
     *            是否显示
     * @param string $strExt
     *            后缀
     * @param string $sTargetCache
     *            主模板缓存路径
     * @param string $sMd5
     *            源文件地址 md5 标记
     * @return string
     */
    public function display($sFile, $bDisplay = true, $strExt = '', $sTargetCache = '', $sMd5 = '') {
        // 加载视图文件
        if (! is_file ( $sFile )) {
            $sFile = $this->parseFile ( $sFile, $strExt );
        }
        
        // 分析默认视图文件
        if (! is_file ( $sFile )) {
            $sFile = $this->parseDefaultFile ( $sFile );
        }
        
        if (! is_file ( $sFile )) {
            throw new InvalidArgumentException ( __ ( '模板文件 %s 不存在', $sFile ) );
        }
        
        // 变量赋值
        if (is_array ( $this->arrVar ) and ! empty ( $this->arrVar )) {
            extract ( $this->arrVar, EXTR_PREFIX_SAME, 'q_' );
        }
        
        $sCachePath = $this->getCachePath ( $sFile ); // 编译文件路径
        if ($this->isCacheExpired ( $sFile, $sCachePath )) { // 重新编译
            $this->objParse->doCombile ( $sFile, $sCachePath );
        }
        
        // 逐步将子模板缓存写入父模板至到最后
        if ($sTargetCache) {
            if (is_file ( $sFile ) && is_file ( $sTargetCache )) {
                // 源码
                $sTargetContent = file_get_contents ( $sTargetCache );
                $sChildCache = file_get_contents ( $sCachePath );
                
                // 替换
                $sTargetContent = preg_replace ( "/<!--<\#\#\#\#incl\*" . $sMd5 . "\*ude\#\#\#\#>-->(.*?)<!--<\/\#\#\#\#incl\*" . $sMd5 . "\*ude\#\#\#\#\/>-->/s", substr ( $sChildCache, strpos ( $sChildCache, PHP_EOL ) ), $sTargetContent );
                file_put_contents ( $sTargetCache, $sTargetContent );
                
                unset ( $sChildCache, $sTargetContent );
            } else {
                throw new InvalidArgumentException ( sprintf ( 'Source %s and target cache %s is not a valid path', $sFile, $sTargetCache ) );
            }
        }
        
        // 返回类型
        if ($bDisplay === false) {
            ob_start ();
            include $sCachePath;
            $sReturn = ob_get_contents ();
            ob_end_clean ();
            $this->fixIe ( $sReturn );
            return $sReturn;
        } else {
            include $sCachePath;
        }
    }
    
    /**
     * 设置模板变量
     *
     * @param mixed $mixName            
     * @param mixed $mixValue            
     * @return void
     */
    public function setVar($mixName, $mixValue = null) {
        if (is_string ( $mixName )) {
            $this->arrVar [$mixName] = $mixValue;
        } elseif (is_array ( $mixName )) {
            $this->arrVar = array_merge ( $this->arrVar, $mixName );
        }
    }
    
    /**
     * 获取变量值
     *
     * @param string|null $sName            
     * @return mixed
     */
    public function getVar($sName = null) {
        if (is_null ( $sName ))
            return $this->arrVar;
        return isset ( $this->arrVar [$sName] ) ? $this->_arrVar [$sName] : null;
    }
    
    /**
     * 获取编译路径
     *
     * @param string $sFile            
     * @return string
     */
    public function getCachePath($sFile) {
        // 统一斜线
        $sFile = str_replace ( '//', '/', str_replace ( '\\', '/', $sFile ) );
        
        // 统一缓存文件
        $sFile = basename ( $sFile, '.' . pathinfo ( $sFile, PATHINFO_EXTENSION ) ) . '.' . md5 ( $sFile ) . '.php';
        
        // 返回真实路径
        return $this->getOption ( 'theme_cache_path' ) . '/' . $sFile;
    }
    
    /**
     * 自动分析视图上下文环境
     *
     * @param string $strThemePath            
     * @return void
     */
    public function parseContext($strThemePath) {
        if ($this->arrOption ['theme_name']) {
            return $this;
        }
        
        if (! $this->getOption ( 'switch' )) {
            $sThemeSet = $this->getOption ( 'default' );
        } else {
            if ($this->getOption ( 'cookie_app' ) === true) {
                $sCookieName = $this->getOption ( 'app_name' ) . '_view';
            } else {
                $sCookieName = 'view';
            }
            
            if (isset ( $_GET [static::ARGS] )) {
                $sThemeSet = $_GET [static::ARGS];
                $this->objCookie->set ( $sCookieName, $sThemeSet );
            } else {
                if ($this->objCookie->get ( $sCookieName )) {
                    $sThemeSet = $this->objCookie->get ( $sCookieName );
                } else {
                    $sThemeSet = $this->getOption ( 'default' );
                }
            }
        }
        $this->arrOption ['theme_name'] = $sThemeSet;
        $this->arrOption ['theme_path'] = $strThemePath . '/' . $sThemeSet;
        return $this;
    }
    
    /**
     * 分析模板真实路径
     *
     * @param string $sTpl
     *            文件地址
     * @param string $sExt
     *            扩展名
     * @return string
     */
    protected function parseFile($sTpl, $sExt = '') {
        $calHelp = function ($sContent) {
            return str_replace ( [ 
                    ':',
                    '+' 
            ], [ 
                    '->',
                    '::' 
            ], $sContent );
        };
        
        $sTpl = trim ( str_replace ( '->', '.', $sTpl ) );
        
        // 完整路径 或者变量
        if (pathinfo ( $sTpl, PATHINFO_EXTENSION ) || strpos ( $sTpl, '$' ) === 0) {
            return $calHelp ( $sTpl );
        } elseif (strpos ( $sTpl, '(' ) !== false) { // 存在表达式
            return $calHelp ( $sTpl );
        } else {
            // 空取默认控制器和方法
            if ($sTpl == '') {
                $sTpl = $this->getOption ( 'controller_name' ) . $this->getOption ( 'controlleraction_depr' ) . $this->getOption ( 'action_name' );
            }
            
            if (strpos ( $sTpl, '@' )) { // 分析主题
                $arrArray = explode ( '@', $sTpl );
                $sTheme = array_shift ( $arrArray );
                $sTpl = array_shift ( $arrArray );
                unset ( $arrArray );
            }
            
            $sTpl = str_replace ( [ 
                    '+',
                    ':' 
            ], $this->getOption ( 'controlleraction_depr' ), $sTpl );
            return dirname ( $this->getOption ( 'theme_path' ) ) . '/' . (isset ( $sTheme ) ? $sTheme : $this->getOption ( 'theme_name' )) . '/' . $sTpl . ($sExt ?  : $this->getOption ( 'suffix' ));
        }
    }
    
    /**
     * 匹配默认地址（文件不存在）
     *
     * @param string $sTpl
     *            文件地址
     * @return string
     */
    protected function parseDefaultFile($sTpl) {
        if (is_file ( $sTpl )) {
            return $sTpl;
        }
        
        $sBakTpl = $sTpl;
        
        // 物理路径
        if (strpos ( $sTpl, ':' ) !== false || strpos ( $sTpl, '/' ) === 0 || strpos ( $sTpl, '\\' ) === 0) {
            $sTpl = str_replace ( str_replace ( '\\', '/', $this->getOption ( 'theme_path' ) . '/' ), '', str_replace ( '\\', '/', ($sTpl) ) );
        }
        
        // 当前主题
        if (is_file ( ($sTpl = $this->getOption ( 'theme_path' ) . '/' . $sTpl) )) {
            return $sTpl;
        }
        
        // 备用地址
        if ($this->getOption ( 'theme_path_default' ) && is_file ( ($sTpl = $this->getOption ( 'theme_path_default' ) . '/' . $sTpl) )) {
            return $sTpl;
        }
        
        // default 主题
        if ($this->getOption ( 'theme_name' ) != 'default' && is_file ( ($sTpl = dirname ( $this->getOption ( 'theme_path' ) ) . '/default/' . $sTpl) )) {
            return $sTpl;
        }
        
        return $sBakTpl;
    }
    
    /**
     * 判断缓存是否过期
     *
     * @param string $sFile            
     * @param string $sCachePath            
     * @return boolean
     */
    protected function isCacheExpired($sFile, $sCachePath) {
        // 开启调试
        if ($this->getOption ( 'app_debug' )) {
            return true;
        }
        
        // 缓存文件不存在过期
        if (! is_file ( $sCachePath )) {
            return true;
        }
        
        // 编译过期时间为 <= 0 表示永不过期
        if ($this->getOption ( 'cache_lifetime' ) <= 0) {
            return false;
        }
        
        // 缓存时间到期
        if (filemtime ( $sCachePath ) + intval ( $this->getOption ( 'cache_lifetime' ) ) < time ()) {
            return true;
        }
        
        // 文件有更新
        if (filemtime ( $sFile ) >= filemtime ( $sCachePath )) {
            return true;
        }
        
        return false;
    }
    
    /**
     * 修复 ie 显示问题
     * 过滤编译文件子模板定位注释标签，防止在网页头部出现注释，导致 IE 浏览器不居中
     *
     * @param string $sContent            
     * @return string
     */
    protected function fixIe($sContent) {
        if ($this->getOption ( 'cache_children' ) === true) {
            $sContent = preg_replace ( "/<!--<\#\#\#\#incl\*(.*?)\*ude\#\#\#\#>-->/", '', $sContent );
            $sContent = preg_replace ( "/<!--<\/\#\#\#\#incl\*(.*?)\*ude\#\#\#\#\/>-->/", '', $sContent );
        }
        return $sContent;
    }
}
