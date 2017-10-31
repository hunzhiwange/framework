<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\auth;

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

use queryyetsimple\support\option;

/**
 * connect 驱动抽象类
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.09.07
 * @version 1.0
 */
abstract class aconnect {
    
    use option;
    
    /**
     * 是否已经设置过登录字段
     *
     * @var boolean
     */
    protected $booSetField = false;
    
    /**
     * 登录字段设置
     *
     * @var array
     */
    protected $arrField = [ 
            'id' => 'id',
            'name' => 'name',
            'nikename' => 'nikename',
            'random' => 'random',
            'email' => 'email',
            'mobile' => 'mobile',
            'password' => 'password',
            'register_ip' => 'register_ip',
            'login_time' => 'login_time',
            'login_ip' => 'login_ip',
            'login_count' => 'login_count',
            'status' => 'status' 
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
     * 设置字段
     *
     * @param array $arrField            
     * @param boolean $booForce            
     * @return void
     */
    public function setField(array $arrField, $booForce = false) {
        if ($booForce === false && $this->booSetField = true) {
            return;
        }
        
        $this->booSetField = true;
        foreach ( $arrField as $strKey => $strField ) {
            if (isset ( $this->arrField [$strKey] )) {
                $this->arrField [$strKey] = $strField;
            }
        }
    }
    
    /**
     * 获取字段
     *
     * @param array $strField            
     * @return mixed
     */
    public function getField($strField) {
        return isset ( $this->arrField [$strField] ) ? $this->arrField [$strField] : null;
    }
    
    /**
     * 批量获取字段
     *
     * @param array $arrField            
     * @param boolean $booFilterNull            
     * @return array
     */
    public function getFields(array $arrField, $booFilterNull = true) {
        $arrData = [ ];
        foreach ( $arrField as $strField ) {
            if (is_null ( $mixValue = $this->getField ( $strField ) ) && $booFilterNull === true) {
                continue;
            }
            $arrData [] = $mixValue;
        }
        return $arrData;
    }
}
