<?php
// [$QueryPHP] A PHP Framework Since 2010.10.03. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\cache\abstracts;

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

/**
 * 缓存抽象类
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2017.02.15
 * @version 1.0
 */
abstract class cache {
    
    /**
     * 缓存配置
     *
     * @var array
     */
    protected $arrOption = [ ];
    
    /**
     * 缓存服务句柄
     *
     * @var handle
     */
    protected $hHandle = null;
    
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
    
    /**
     * 返回缓存句柄
     *
     * @return mixed
     */
    public function handle() {
        return $this->hHandle;
    }
    
    /**
     * 获取缓存名字
     *
     * @param string $sCacheName            
     * @param array $arrOption            
     * @return string
     */
    protected function getCacheName($sCacheName, $arrOption) {
        return $arrOption ['prefix'] . $sCacheName;
    }
    
    /**
     * 初始化缓存配置
     *
     * @param array $arrOption            
     * @return void
     */
    protected function initialization($arrOption = []) {
        foreach ( array_keys ( $this->arrClasssFacesOption ) as $strOption ) {
            $arrTemp = explode ( '.', $strOption );
            $arrTemp = array_pop ( $arrTemp );
            $this->arrOption [$arrTemp] = $this->classsFacesOption ( $strOption );
        }
        $this->arrOption ['prefix'] = $this->arrOption ['cache\prefix'];
        $this->arrOption ['expire'] = $this->arrOption ['cache\expire'];
        unset ( $this->arrOption ['cache\prefix'], $this->arrOption ['cache\expire'] );
        
        if ($arrOption) {
            $this->arrOption = array_merge ( $this->arrOption, $arrOption );
        }
    }
    
    /**
     * 读取缓存时间配置
     *
     * @param string $sId            
     * @param int $intDefaultTime            
     * @return number
     */
    protected function cacheTime($sId, $intDefaultTime = 0) {
        if (! $this->arrOption ['cache\time_preset'])
            return $intDefaultTime;
        
        if (isset ( $this->arrOption ['cache\time_preset'] [$sId] )) {
            return $this->arrOption ['cache\time_preset'] [$sId];
        }
        
        foreach ( $this->arrOption ['cache\time_preset'] as $sKey => $nValue ) {
            $sKeyCache = '/^' . str_replace ( '*', '(\S+)', $sKey ) . '$/';
            if (preg_match ( $sKeyCache, $sId, $arrRes )) {
                return $this->arrOption ['cache\time_preset'] [$sKey];
            }
        }
        
        return $intDefaultTime;
    }
    
    /**
     * 强制不启用缓存
     *
     * @return boolean
     */
    protected function checkForce() {
        if (! empty ( $_REQUEST [$this->classsFacesOption ( 'cache\nocache_force' )] ))
            return true;
        return false;
    }
}
