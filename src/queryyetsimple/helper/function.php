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
use queryyetsimple\exception\exceptions;

/**
 * 辅助方法
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2017.05.22
 * @version 1.0
 */
if (! function_exists ( '__' )) {
    /**
     * 语言包
     *
     * @param string|null $sValue            
     * @return mixed
     */
    function __($sValue = null /*argvs*/ ){
        if (func_num_args () > 1) { // 代入参数
            $sValue = call_user_func_array ( 'sprintf', func_get_args () );
        }
        return $sValue;
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

if (! function_exists ( 'project' )) {
    /**
     * 返回项目容器
     *
     * @param string|null $sInstance            
     * @return \queryyetsimple\mvc\queryyetsimple\mvc\project
     */
    function project($sInstance = null /*argvs*/) {
        if ($sInstance === null) {
            return queryyetsimple\mvc\project::bootstrap ();
        } else {
            $arrArgs = func_get_args ();
            $strFacades = array_shift ( $arrArgs );
            if (($objFacades = queryyetsimple\mvc\project::bootstrap ()->make ( $strFacades, $arrArgs ))) {
                return $objFacades;
            }
            queryyetsimple\exception\exceptions::badMethodCallException ( __ ( '容器中未发现注入的 %s', $sInstance ) );
        }
    }
}
