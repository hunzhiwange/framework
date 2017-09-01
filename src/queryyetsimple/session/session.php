<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\session;

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
use SessionHandlerInterface;
use queryyetsimple\support\interfaces\container;
use queryyetsimple\session\interfaces\session as interfaces_session;

/**
 * session 入口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.02.15
 * @version 1.0
 */
class session implements interfaces_session {
    
    /**
     * IOC Container
     *
     * @var \queryyetsimple\support\interfaces\container
     */
    protected $objContainer;
    
    /**
     * session 连接对象
     *
     * @var \queryyetsimple\session\store[]
     */
    protected static $arrConnect;
    
    /**
     * 构造函数
     *
     * @param \queryyetsimple\support\interfaces\container $objConnect            
     * @return void
     */
    public function __construct(container $objContainer) {
        $this->objContainer = $objContainer;
    }
    
    /**
     * 连接 session 并返回连接对象
     *
     * @param array|string $mixOption            
     * @return \queryyetsimple\session\store
     */
    public function connect($mixOption = []) {
        if (is_string ( $mixOption ) && ! is_array ( ($mixOption = $this->objContainer ['option'] ['session\\connect.' . $mixOption]) )) {
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
     * 创建 session store
     *
     * @param \SessionHandlerInterface $oHandler            
     * @return \queryyetsimple\session\store
     */
    public function store(SessionHandlerInterface $oHandler = null) {
        $arrOption = $this->objContainer ['option'] ['session\\'];
        unset ( $arrOption ['default'], $arrOption ['connect'] );
        return new store ( $arrOption, $oHandler );
    }
    
    /**
     * 返回默认驱动
     *
     * @return string
     */
    public function getDefaultDriver() {
        return $this->objContainer ['option'] ['session\default'];
    }
    
    /**
     * 设置默认驱动
     *
     * @param string $strName            
     * @return void
     */
    public function setDefaultDriver($strName) {
        $this->objContainer ['option'] ['session\default'] = $strName;
    }
    
    /**
     * 创建连接
     *
     * @param string $strConnect            
     * @param array $arrOption            
     * @return \SessionHandlerInterface|null
     */
    protected function makeConnect($strConnect = null, $arrOption = []) {
        if (! $strConnect)
            return null;
        if (is_null ( $this->objContainer ['option'] ['session\connect.' . $strConnect] ))
            throw new Exception ( __ ( 'session 驱动 %s 不存在', $strConnect ) );
        return $this->{'makeConnect' . ucfirst ( $strConnect )} ( $arrOption );
    }
    
    /**
     * 创建 memcache 缓存
     *
     * @param array $arrOption            
     * @return \queryyetsimple\session\memcache
     */
    protected function makeConnectMemcache($arrOption = []) {
        return new memcache ( array_merge ( $this->getOption ( 'memcache', $arrOption ) ) );
    }
    
    /**
     * 创建 redis 缓存
     *
     * @param array $arrOption            
     * @return \queryyetsimple\session\redis
     */
    protected function makeConnectRedis($arrOption = []) {
        return new redis ( array_merge ( $this->getOption ( 'redis', $arrOption ) ) );
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
     * 读取默认 session 配置
     *
     * @param string $strConnect            
     * @param array $arrExtendOption            
     * @return array
     */
    protected function getOption($strConnect, array $arrExtendOption = []) {
        $arrOption = $this->objContainer ['option'] ['session\\'];
        unset ( $arrOption ['default'], $arrOption ['connect'] );
        
        return array_merge ( array_filter ( $this->objContainer ['option'] ['session\connect.' . $strConnect], function ($mixValue) {
            return ! is_null ( $mixValue );
        } ), $arrOption, $arrExtendOption );
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
