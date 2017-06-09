<?php
// [$QueryPHP] A PHP Framework Since 2010.10.03. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\cookie;

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

use queryyetsimple\assert\assert;
use queryyetsimple\classs\faces as classs_faces;

/**
 * cookie 封装
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2016.11.19
 * @version 1.0
 */
class cookie {
    
    use classs_faces;
    
    /**
     * 配置
     *
     * @var array
     */
    protected $arrClasssFacesOption = [ 
            'cookie\prefix' => 'q_',
            'cookie\expire' => 86400,
            'cookie\domain' => '',
            'cookie\path' => '/',
            'cookie\httponly' => false 
    ];
    
    /**
     * 缓存配置
     *
     * @var array
     */
    protected $arrOption = [ ];
    
    /**
     * 构造函数
     *
     * @param array $arrOption            
     * @return void
     */
    public function __construct(array $arrOption = []) {
        foreach ( array_keys ( $this->arrClasssFacesOption ) as $strOption ) {
            $arrTemp = explode ( '\\', $strOption );
            $arrTemp = array_pop ( $arrTemp );
            $this->arrOption [$arrTemp] = $this->classsFacesOption ( $strOption );
        }
        if ($arrOption) {
            $this->arrOption = array_merge ( $this->arrOption, $arrOption );
        }
    }
    
    /**
     * 设置 COOKIE
     *
     * @param string $sName            
     * @param string $mixValue            
     * @param array $arrOption            
     * @return void
     */
    public function set($sName, $mixValue = '', array $arrOption = []) {
        $arrOption = $this->option ( $arrOption, null, false );
        
        // 验证 cookie 值是不是一个标量
        assert::notNull ( $mixValue );
        assert::scalar ( $mixValue );
        
        $sName = $arrOption ['prefix'] . $sName;
        
        if ($mixValue === null || $arrOption ['expire'] < 0) {
            if (isset ( $_COOKIE [$sName] ))
                unset ( $_COOKIE [$sName] );
        } else {
            $_COOKIE [$sName] = $mixValue;
        }
        
        $arrOption ['expire'] = $arrOption ['expire'] > 0 ? time () + $arrOption ['expire'] : ($arrOption ['expire'] < 0 ? time () - 31536000 : 0);
        setcookie ( $sName, $mixValue, $arrOption ['expire'], $arrOption ['path'], $arrOption ['domain'], ! empty ( $_SERVER ['HTTPS'] ) && strtoupper ( $_SERVER ['HTTPS'] ) == 'ON', $arrOption ['httponly'] );
    }
    
    /**
     * 获取 cookie
     *
     * @param string $sName            
     * @param mixed $mixDefault            
     * @param array $arrOption            
     * @return mixed
     */
    public function get($sName, $mixDefault = null, array $arrOption = []) {
        $arrOption = $this->option ( $arrOption, null, false );
        $sName = $arrOption ['prefix'] . $sName;
        return isset ( $_COOKIE [$sName] ) ? $_COOKIE [$sName] : $mixDefault;
    }
    
    /**
     * 删除 cookie
     *
     * @param string $sName            
     * @param array $arrOption            
     * @return void
     */
    public function delete($sName, array $arrOption = []) {
        $this->set ( $sName, null, $arrOption );
    }
    
    /**
     * 清空 cookie
     *
     * @param boolean $bOnlyPrefix            
     * @param array $arrOption            
     * @return void
     */
    public function clear($bOnlyPrefix = true, array $arrOption = []) {
        $arrOption = $this->option ( $arrOption, null, false );
        $strPrefix = $arrOption ['prefix'];
        foreach ( $_COOKIE as $sKey => $mixVal ) {
            if ($bOnlyPrefix === true && $strPrefix) {
                if (strpos ( $sKey, $strPrefix ) === 0) {
                    $this->delete ( $sKey, $arrOption );
                }
            } else {
                $arrOption ['prefix'] = '';
                $this->delete ( $sKey, $arrOption );
            }
        }
    }
    
    /**
     * 修改配置
     *
     * @param mixed $mixName            
     * @param mixed $mixValue            
     * @param boolean $booMerge            
     * @return array
     */
    public function option($mixName = '', $mixValue = null, $booMerge = true) {
        $arrOption = $this->arrOption;
        if (! empty ( $mixName )) {
            if (is_array ( $mixName )) {
                $arrOption = array_merge ( $arrOption, $mixName );
            } else {
                if (is_null ( $mixValue )) {
                    if (isset ( $arrOption [$mixName] )) {
                        unset ( $arrOption [$mixName] );
                    }
                } else {
                    $arrOption [$mixName] = $mixValue;
                }
            }
            
            if ($booMerge === true) {
                $this->arrOption = $arrOption;
            }
        }
        
        return $arrOption;
    }
}
