<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\http;

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

use queryyetsimple\classs\infinity;

/**
 * http 请求
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2016.11.19
 * @version 1.0
 */
class request {
    
    use infinity;
    
    /**
     * 基础 url
     *
     * @var string
     */
    private static $sBaseUrl;
    
    /**
     * 请求 url
     *
     * @var string
     */
    private static $sRequestUrl;
    
    /**
     * 构造函数
     *
     * @return void
     */
    public function __construct() {
    }
    
    /**
     * 返回 REQUEST 参数
     *
     * @return array
     */
    public function getRequest() {
        return $_REQUEST;
    }
    
    /**
     * 获取 in 数据
     *
     * @param string $sKey            
     * @param string $sVar            
     * @return mixed
     */
    public function in($sKey, $sVar = 'request') {
        switch ($sVar) {
            case 'get' :
                $sVar = &$_GET;
                break;
            case 'post' :
                $sVar = &$_POST;
                break;
            case 'cookie' :
                $sVar = &$_COOKIE;
                break;
            case 'session' :
                $sVar = &$_SESSION;
                break;
            case 'request' :
                $sVar = &$_REQUEST;
                break;
            case 'files' :
                $sVar = &$_FILES;
                break;
        }
        
        return isset ( $sVar [$sKey] ) ? $sVar [$sKey] : NULL;
    }
    
    /**
     * PHP 运行模式命令行
     * link http://www.phpddt.com/php/php-sapi.html
     *
     * @return boolean
     */
    public function isCli() {
        return PHP_SAPI == 'cli' ? true : false;
    }
    
    /**
     * PHP 运行模式 cgi
     * link http://www.phpddt.com/php/php-sapi.html
     *
     * @return boolean
     */
    public function isCgi() {
        return substr ( PHP_SAPI, 0, 3 ) == 'cgi' ? true : false;
    }
    
    /**
     * 是否为 Ajax 请求行为
     *
     * @return boolean
     */
    public function isAjax() {
        return isset ( $_SERVER ['HTTP_X_REQUESTED_WITH'] ) && 'xmlhttprequest' == strtolower ( $_SERVER ['HTTP_X_REQUESTED_WITH'] );
    }
    
    /**
     * 是否为 Get 请求行为
     *
     * @return boolean
     */
    public function isGet() {
        return strtolower ( $_SERVER ['REQUEST_METHOD'] ) == 'get';
    }
    
    /**
     * 是否为 Post 请求行为
     *
     * @return boolean
     */
    public function isPost() {
        return strtolower ( $_SERVER ['REQUEST_METHOD'] ) == 'post';
    }
    
    /**
     * 是否启用 https
     *
     * @return boolean
     */
    public function isSsl() {
        if (isset ( $_SERVER ['HTTPS'] ) && ('1' == $_SERVER ['HTTPS'] || 'on' == strtolower ( $_SERVER ['HTTPS'] ))) {
            return true;
        } elseif (isset ( $_SERVER ['SERVER_PORT'] ) && ('443' == $_SERVER ['SERVER_PORT'])) {
            return true;
        }
        return false;
    }
    
    /**
     * 获取 host
     *
     * @return boolean
     */
    public function getHost() {
        return isset ( $_SERVER ['HTTP_X_FORWARDED_HOST'] ) ? $_SERVER ['HTTP_X_FORWARDED_HOST'] : (isset ( $_SERVER ['HTTP_HOST'] ) ? $_SERVER ['HTTP_HOST'] : '');
    }
    
    /**
     * 返回当前 URL 地址
     *
     * @return string
     */
    public function getUrl() {
        return ($this->isSsl () ? 'https://' : 'http://') . $_SERVER ['HTTP_HOST'] . $_SERVER ['REQUEST_URI'];
    }
    
    /**
     * 获取 IP 地址
     *
     * @return string
     */
    public function getIp() {
        static $sRealip = NULL;
        
        if ($sRealip !== NULL) {
            return $sRealip;
        }
        
        if (isset ( $_SERVER )) {
            if (isset ( $_SERVER ['HTTP_X_FORWARDED_FOR'] )) {
                $arrValue = explode ( ',', $_SERVER ['HTTP_X_FORWARDED_FOR'] );
                foreach ( $arrValue as $sIp ) { // 取 X-Forwarded-For 中第一个非 unknown 的有效 IP 字符串
                    $sIp = trim ( $sIp );
                    if ($sIp != 'unknown') {
                        $sRealip = $sIp;
                        break;
                    }
                }
            } elseif (isset ( $_SERVER ['HTTP_CLIENT_IP'] )) {
                $sRealip = $_SERVER ['HTTP_CLIENT_IP'];
            } else {
                if (isset ( $_SERVER ['REMOTE_ADDR'] )) {
                    $sRealip = $_SERVER ['REMOTE_ADDR'];
                } else {
                    $sRealip = '0.0.0.0';
                }
            }
        } else {
            if (getenv ( 'HTTP_X_FORWARDED_FOR' )) {
                $sRealip = getenv ( 'HTTP_X_FORWARDED_FOR' );
            } elseif (getenv ( 'HTTP_CLIENT_IP' )) {
                $sRealip = getenv ( 'HTTP_CLIENT_IP' );
            } else {
                $sRealip = getenv ( 'REMOTE_ADDR' );
            }
        }
        
        preg_match ( "/[\d\.]{7,15}/", $sRealip, $arrOnlineip );
        $sRealip = ! empty ( $arrOnlineip [0] ) ? $arrOnlineip [0] : '0.0.0.0';
        
        return $sRealip;
    }
    
    /**
     * pathinfo 兼容性分析
     *
     * @return string
     */
    public function pathinfo() {
        if (! empty ( $_SERVER ['PATH_INFO'] )) {
            return $_SERVER ['PATH_INFO'];
        }
        
        // 分析基础 url
        $sBaseUrl = $this->baseUrl ();
        
        // 分析请求参数
        if (null === ($sRequestUrl = $this->requestUrl ())) {
            return '';
        }
        
        if (($nPos = strpos ( $sRequestUrl, '?' )) > 0) {
            $sRequestUrl = substr ( $sRequestUrl, 0, $nPos );
        }
        
        if ((null !== $sBaseUrl) && (false === ($sPathinfo = substr ( $sRequestUrl, strlen ( $sBaseUrl ) )))) {
            $sPathinfo = '';
        } elseif (null === $sBaseUrl) {
            $sPathinfo = $sRequestUrl;
        }
        
        return $sPathinfo;
    }
    
    /**
     * 分析基础 url
     *
     * @return string
     */
    public function baseUrl() {
        // 存在返回
        if (static::$sBaseUrl) {
            return static::$sBaseUrl;
        }
        
        // 兼容分析
        $sFileName = basename ( $_SERVER ['SCRIPT_FILENAME'] );
        if (basename ( $_SERVER ['SCRIPT_NAME'] ) === $sFileName) {
            $sUrl = $_SERVER ['SCRIPT_NAME'];
        } elseif (basename ( $_SERVER ['PHP_SELF'] ) === $sFileName) {
            $sUrl = $_SERVER ['PHP_SELF'];
        } elseif (isset ( $_SERVER ['ORIG_SCRIPT_NAME'] ) && basename ( $_SERVER ['ORIG_SCRIPT_NAME'] ) === $sFileName) {
            $sUrl = $_SERVER ['ORIG_SCRIPT_NAME'];
        } else {
            $sPath = $_SERVER ['PHP_SELF'];
            $arrSegs = explode ( '/', trim ( $_SERVER ['SCRIPT_FILENAME'], '/' ) );
            $arrSegs = array_reverse ( $arrSegs );
            $nIndex = 0;
            $nLast = count ( $arrSegs );
            $sUrl = '';
            do {
                $sSeg = $arrSegs [$nIndex];
                $sUrl = '/' . $sSeg . $sUrl;
                ++ $nIndex;
            } while ( ($nLast > $nIndex) && (false !== ($nPos = strpos ( $sPath, $sUrl ))) && (0 != $nPos) );
        }
        
        // 比对请求
        $sRequestUrl = $this->requestUrl ();
        if (0 === strpos ( $sRequestUrl, $sUrl )) {
            return static::$sBaseUrl = $sUrl;
        }
        
        if (0 === strpos ( $sRequestUrl, dirname ( $sUrl ) )) {
            return static::$sBaseUrl = rtrim ( dirname ( $sUrl ), '/' ) . '/';
        }
        
        if (! strpos ( $sRequestUrl, basename ( $sUrl ) )) {
            return '';
        }
        
        if ((strlen ( $sRequestUrl ) >= strlen ( $sUrl )) && ((false !== ($nPos = strpos ( $sRequestUrl, $sUrl ))) && ($nPos !== 0))) {
            $sUrl = substr ( $sRequestUrl, 0, $nPos + strlen ( $sUrl ) );
        }
        
        return static::$sBaseUrl = rtrim ( $sUrl, '/' ) . '/';
    }
    
    /**
     * 请求参数
     *
     * @return string
     */
    public function requestUrl() {
        if (static::$sRequestUrl) {
            return static::$sRequestUrl;
        }
        
        // For IIS
        $_SERVER ['REQUEST_URI'] = isset ( $_SERVER ['REQUEST_URI'] ) ? $_SERVER ['REQUEST_URI'] : $_SERVER ["HTTP_X_REWRITE_URL"];
        
        if (isset ( $_SERVER ['HTTP_X_REWRITE_URL'] )) {
            $sUrl = $_SERVER ['HTTP_X_REWRITE_URL'];
        } elseif (isset ( $_SERVER ['REQUEST_URI'] )) {
            $sUrl = $_SERVER ['REQUEST_URI'];
        } elseif (isset ( $_SERVER ['ORIG_PATH_INFO'] )) {
            $sUrl = $_SERVER ['ORIG_PATH_INFO'];
            if (! empty ( $_SERVER ['QUERY_STRING'] )) {
                $sUrl .= '?' . $_SERVER ['QUERY_STRING'];
            }
        } else {
            $sUrl = '';
        }
        
        return static::$sRequestUrl = $sUrl;
    }
}
