<?php
// [$QueryPHP] A PHP Framework Since 2010.10.03. <Query Yet Simple>
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

use queryyetsimple\mvc\project;

if (! function_exists ( 'project' )) {
    /**
     * 返回项目容器或者注入
     *
     * @param string|null $sInstance            
     * @return \queryyetsimple\mvc\project
     */
    function project($sInstance = null /*argvs*/) {
        if ($sInstance === null) {
            return project::bootstrap ();
        } else {
            $arrArgs = func_get_args ();
            array_shift ( $arrArgs );
            if (($objInstance = project::bootstrap ()->make ( $sInstance, $arrArgs ))) {
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

if (! function_exists ( '__' )) {
    /**
     * 语言包
     *
     * @param string|null $sValue            
     * @return mixed
     */
    function __($sValue = null /*argvs*/ ){
        return call_user_func_array ( [ 
                'queryyetsimple\i18n\i18n',
                'getTexts' 
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
        return is_callable ( $mixValue ) ? call_user_func_array ( $mixValue, $arrArgs ) : $mixValue;
    }
}
