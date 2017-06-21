<?php
// [$QueryPHP] A PHP Framework For Simple As Free As Wind. <Query Yet Simple>
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
use queryyetsimple\classs\option as classs_option;
use queryyetsimple\cookie\interfaces\cookie as interfaces_cookie;

/**
 * cookie 封装
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2016.11.19
 * @version 1.0
 */
class cookie implements interfaces_cookie {
    
    use classs_option;
    
    /**
     * 配置
     *
     * @var array
     */
    protected $arrOption = [ 
            'prefix' => 'q_',
            'expire' => 86400,
            'domain' => '',
            'path' => '/',
            'httponly' => false 
    ];
    
    /**
     * 构造函数
     *
     * @param array $arrOption            
     * @return void
     */
    public function __construct(array $arrOption = []) {
        $this->options ( $arrOption );
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
        $arrOption = $this->getOptions ( $arrOption );
        
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
        $arrOption = $this->getOptions ( $arrOption );
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
        $arrOption = $this->getOptions ( $arrOption );
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
}
