<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\mail;

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
use InvalidArgumentException;
use queryyetsimple\support\icontainer;

/**
 * mail 入口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.08.26
 * @version 1.0
 */
class mail implements imail {
    
    /**
     * IOC Container
     *
     * @var \queryyetsimple\support\icontainer
     */
    protected $objContainer;
    
    /**
     * mail 连接对象
     *
     * @var \queryyetsimple\mail\store[]
     */
    protected static $arrConnect;
    
    /**
     * 构造函数
     *
     * @param \queryyetsimple\support\icontainer $objContainer            
     * @return void
     */
    public function __construct(icontainer $objContainer) {
        $this->objContainer = $objContainer;
    }
    
    /**
     * 连接 mail 并返回连接对象
     *
     * @param array|string $mixOption            
     * @return \queryyetsimple\mail\store
     */
    public function connect($mixOption = []) {
        if (is_string ( $mixOption ) && ! is_array ( ($mixOption = $this->objContainer ['option'] ['mail\\connect.' . $mixOption]) )) {
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
     * 创建 mail store
     *
     * @param \queryyetsimple\mail\iconnect $oConnect            
     * @return \queryyetsimple\mail\store
     */
    public function store($oConnect) {
        $arrOption = $this->objContainer ['option'] ['mail\\'];
        unset ( $arrOption ['default'], $arrOption ['connect'] );
        return new store ( $arrOption, $oConnect, $this->objContainer ['view'], $this->objContainer ['event'] );
    }
    
    /**
     * 返回默认驱动
     *
     * @return string
     */
    public function getDefaultDriver() {
        return $this->objContainer ['option'] ['mail\default'];
    }
    
    /**
     * 设置默认驱动
     *
     * @param string $strName            
     * @return void
     */
    public function setDefaultDriver($strName) {
        $this->objContainer ['option'] ['mail\default'] = $strName;
    }
    
    /**
     * 创建连接
     *
     * @param string $strConnect            
     * @param array $arrOption            
     * @return \queryyetsimple\mail\iconnect
     */
    protected function makeConnect($strConnect, $arrOption = []) {
        if (is_null ( $this->objContainer ['option'] ['mail\connect.' . $strConnect] ))
            throw new Exception ( sprintf ( '%s driver %s not exits.', 'Mail', $strConnect ) );
        return $this->{'makeConnect' . ucfirst ( $strConnect )} ( $arrOption );
    }
    
    /**
     * 创建 smtp 连接
     *
     * @param array $arrOption            
     * @return \queryyetsimple\mail\smtp
     */
    protected function makeConnectSmtp($arrOption = []) {
        return new smtp ( array_merge ( $this->getOption ( 'smtp', $arrOption ) ) );
    }
    
    /**
     * 创建 sendmail 连接
     *
     * @param array $arrOption            
     * @return \queryyetsimple\mail\sendmail
     */
    protected function makeConnectSendmail($arrOption = []) {
        return new sendmail ( array_merge ( $this->getOption ( 'sendmail', $arrOption ) ) );
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
     * 读取默认 mail 配置
     *
     * @param string $strConnect            
     * @param array $arrExtendOption            
     * @return array
     */
    protected function getOption($strConnect, array $arrExtendOption = []) {
        $arrOption = $this->objContainer ['option'] ['mail\\'];
        unset ( $arrOption ['default'], $arrOption ['from'], $arrOption ['to'], $arrOption ['connect'] );
        return array_merge ( $this->objContainer ['option'] ['mail\connect.' . $strConnect], $arrOption, $arrExtendOption );
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
