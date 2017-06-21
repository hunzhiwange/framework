<?php
// [$QueryPHP] A PHP Framework For Simple As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
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

use queryyetsimple\bootstrap\project;

if (! function_exists ( 'project' )) {
    /**
     * 返回项目容器或者注入
     *
     * @param string|null $sInstance            
     * @return \queryyetsimple\bootstrap\project
     */
    function project($sInstance = null /*argvs*/) {
        if ($sInstance === null) {
            return project::singletons ();
        } else {
            $arrArgs = func_get_args ();
            array_shift ( $arrArgs );
            if (($objInstance = project::singletons ()->make ( $sInstance, $arrArgs ))) {
                return $objInstance;
            }
            throw new BadMethodCallException ( __ ( '容器中未发现注入的 %s', $sInstance ) );
        }
    }
}

if (! function_exists ( 'dump' )) {
    /**
     * 调试一个变量
     *
     * @param mixed $mixValue            
     * @return mixed
     */
    function dump($mixValue /*argvs*/ ){
        return call_user_func_array ( [ 
                'queryyetsimple\debug\dump',
                'dump' 
        ], func_get_args () );
    }
}

if (! function_exists ( 'env' )) {
    /**
     * 取得项目的环境变量.支持 boolean, empty 和 null.
     *
     * @param string $strName            
     * @param mixed $mixDefault            
     * @return mixed
     */
    function env($strName, $mixDefault = null) {
        switch (true) {
            case array_key_exists ( $strName, $_ENV ) :
                $strName = $_ENV [$strName];
                break;
            case array_key_exists ( $strName, $_SERVER ) :
                $strName = $_SERVER [$strName];
                break;
            default :
                $strName = getenv ( $strName );
                if ($strName === false)
                    $strName = value ( $mixDefault );
        }
        
        switch (strtolower ( $strName )) {
            case 'true' :
            case '(true)' :
                return true;
            
            case 'false' :
            case '(false)' :
                return false;
            
            case 'empty' :
            case '(empty)' :
                return '';
            
            case 'null' :
            case '(null)' :
                return;
        }
        
        if (strlen ( $strName ) > 1 && $strName [0] == '"' && $strName [strlen ( $strName ) - 1] == '"') {
            return substr ( $strName, 1, - 1 );
        }
        
        return $strName;
    }
}

if (! function_exists ( 'encrypt' )) {
    /**
     * 加密字符串
     *
     * @param string $strValue            
     * @return string
     */
    function encrypt($strValue) {
        return project ( 'encryption' )->encrypt ( $strValue );
    }
}

if (! function_exists ( 'decrypt' )) {
    /**
     * 解密字符串
     *
     * @param string $strValue            
     * @return string
     */
    function decrypt($strValue) {
        return project ( 'encryption' )->decrypt ( $strValue );
    }
}

if (! function_exists ( 'session' )) {
    /**
     * 设置或者获取 session 值
     *
     * @param array|string $mixKey            
     * @param mixed $mixDefault            
     * @return mixed
     */
    function session($mixKey = null, $mixDefault = null) {
        if (is_null ( $mixKey )) {
            return project ( 'session' );
        }
        
        if (is_array ( $mixKey )) {
            return project ( 'session' )->put ( $mixKey );
        }
        
        return project ( 'session' )->get ( $mixKey, $mixDefault );
    }
}

if (! function_exists ( 'url' )) {
    /**
     * 生成路由地址
     *
     * @param string $sUrl            
     * @param array $arrParams            
     * @param array $in
     *            suffix boolean 是否包含后缀
     *            normal boolean 是否为普通 url
     *            subdomain string 子域名
     * @return string
     */
    function url($sUrl, $arrParams = [], $in = []) {
        if (is_null ( $sUrl )) {
            return project ( 'router' );
        }
        
        return project ( 'router' )->url ( $sUrl, $arrParams, $in );
    }
}

if (! function_exists ( '__' )) {
    /**
     * 语言包
     *
     * @param string|null $sValue            
     * @return mixed
     */
    function __($sValue = null /*argvs*/ ){
        return call_user_func_array ( [ 
                project ( 'i18n' ),
                'getText' 
        ], func_get_args () );
    }
}

if (! function_exists ( 'value' )) {
    /**
     * 返回默认值
     *
     * @param mixed $mixValue            
     * @return mixed
     */
    function value($mixValue) {
        $arrArgs = func_get_args ();
        array_shift ( $arrArgs );
        return ! is_string ( $mixValue ) && is_callable ( $mixValue ) ? call_user_func_array ( $mixValue, $arrArgs ) : $mixValue;
    }
}

if (! function_exists ( 'exception' )) {
    /**
     * 抛出异常处理
     *
     * @param string $strMessage            
     * @param integer $intCode            
     * @param string $strException            
     * @return void
     */
    function exception($strMessage, $intCode = 0, $strException = null) {
        $strException = $strException ?  : 'Exception';
        throw new $strException ( $strMessage, $intCode );
    }
}

if (! function_exists ( 'log' )) {
    /**
     * 记录错误消息
     *
     * @param string $strMessage
     *            应该被记录的错误信息
     * @param string $strLevel
     *            日志类型
     * @param boolean $booWrite            
     * @return void
     */
    function log($strMessage, $strLevel = 'info', $booWrite = false) {
        if ($booWrite)
            project ( 'log' )->write ( $strMessage, $strLevel );
        else
            project ( 'log' )->record ( $strMessage, $strLevel );
    }
}

if (! function_exists ( 'option' )) {
    /**
     * 设置或者获取 option 值
     *
     * @param array|string $mixKey            
     * @param mixed $mixDefault            
     * @return mixed
     */
    function option($mixKey = null, $mixDefault = null) {
        if (is_null ( $mixKey )) {
            return project ( 'option' );
        }
        
        if (is_array ( $mixKey )) {
            return project ( 'option' )->set ( $mixKey );
        }
        
        return project ( 'option' )->get ( $mixKey, $mixDefault );
    }
}
