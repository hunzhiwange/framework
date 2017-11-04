<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\log;

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

use Exception;
use queryyetsimple\support\icontainer;

/**
 * log 入口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.02.15
 * @version 1.0
 */
class log implements ilog {
    
    /**
     * 项目管理
     *
     * @var \queryyetsimple\support\icontainer
     */
    protected $objContainer;
    
    /**
     * log 连接对象
     *
     * @var \queryyetsimple\log\store[]
     */
    protected static $arrConnect;
    
    /**
     * 构造函数
     *
     * @param \queryyetsimple\support\icontainer $objConnect            
     * @return void
     */
    public function __construct(icontainer $objContainer) {
        $this->objContainer = $objContainer;
    }
    
    /**
     * 返回 IOC 容器
     *
     * @return \queryyetsimple\support\icontainer
     */
    public function container() {
        return $this->objContainer;
    }
    
    /**
     * 连接 log 并返回连接对象
     *
     * @param array|string $mixOption            
     * @return \queryyetsimple\log\store
     */
    public function connect($mixOption = []) {
        if (is_string ( $mixOption ) && ! is_array ( ($mixOption = $this->objContainer ['option'] ['log\\connect.' . $mixOption]) )) {
            $mixOption = [ ];
        }
        
        $strDriver = ! empty ( $mixOption ['driver'] ) ? $mixOption ['driver'] : $this->getDefaultDriver ();
        $strUnique = $this->getUnique ( $mixOption );
        
        if (isset ( static::$arrConnect [$strUnique] )) {
            return static::$arrConnect [$strUnique];
        }
        return static::$arrConnect [$strUnique] = $this->store ( $this->makeConnect ( $strDriver, $mixOption ) );
    }
    
    /**
     * 创建 log store
     *
     * @param \queryyetsimple\log\iconnect $oConnect            
     * @return \queryyetsimple\log\store
     */
    public function store(iconnect $oConnect) {
        $arrOption = $this->objContainer ['option'] ['log\\'];
        unset ( $arrOption ['default'], $arrOption ['connect'] );
        return new store ( $oConnect, $arrOption );
    }
    
    /**
     * 返回默认驱动
     *
     * @return string
     */
    public function getDefaultDriver() {
        return $this->objContainer ['option'] ['log\default'];
    }
    
    /**
     * 设置默认驱动
     *
     * @param string $strName            
     * @return void
     */
    public function setDefaultDriver($strName) {
        $this->objContainer ['option'] ['log\default'] = $strName;
    }
    
    /**
     * 创建连接
     *
     * @param string $strConnect            
     * @param array $arrOption            
     * @return \queryyetsimple\log\iconnect
     */
    protected function makeConnect($strConnect, $arrOption = []) {
        if (is_null ( $this->objContainer ['option'] ['log\connect.' . $strConnect] ))
            throw new Exception ( sprintf ( '%s driver %s does not exist.', 'Log', $strConnect ) );
        return $this->{'makeConnect' . ucfirst ( $strConnect )} ( $arrOption );
    }
    
    /**
     * 创建 file 日志驱动
     *
     * @param array $arrOption            
     * @return \queryyetsimple\log\file
     */
    protected function makeConnectFile($arrOption = []) {
        return new file ( array_merge ( $this->getOption ( 'file', $arrOption ) ) );
    }
    
    /**
     * 创建 monolog 日志驱动
     *
     * @param array $arrOption            
     * @return \queryyetsimple\log\monolog
     */
    protected function makeConnectMonolog($arrOption = []) {
        return new monolog ( array_merge ( $this->getOption ( 'monolog', $arrOption ) ) );
    }
    
    /**
     * 取得唯一值
     *
     * @param array $arrOption            
     * @return string
     */
    protected function getUnique($arrOption) {
        return md5 ( serialize ( $arrOption ) );
    }
    
    /**
     * 读取默认日志配置
     *
     * @param string $strConnect            
     * @param array $arrExtendOption            
     * @return array
     */
    protected function getOption($strConnect, array $arrExtendOption = []) {
        $arrOption = $this->objContainer ['option'] ['log\\'];
        unset ( $arrOption ['default'], $arrOption ['connect'] );
        
        return array_merge ( $this->objContainer ['option'] ['log\connect.' . $strConnect], $arrOption, $arrExtendOption );
    }
    
    /**
     * 拦截匿名注册控制器方法
     *
     * @param 方法名 $sMethod            
     * @param 参数 $arrArgs            
     * @return mixed
     */
    public function __call($sMethod, $arrArgs) {
        return call_user_func_array ( [ 
                $this->connect (),
                $sMethod 
        ], $arrArgs );
    }
}
