<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\cache;

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
use queryyetsimple\support\interfaces\container;

/**
 * 缓存入口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.02.15
 * @version 1.0
 */
class cache implements icache {
    
    /**
     * IOC Container
     *
     * @var \queryyetsimple\support\interfaces\container
     */
    protected $objContainer;
    
    /**
     * 缓存连接对象
     *
     * @var \queryyetsimple\cache\repository[]
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
     * 连接缓存并返回连接对象
     *
     * @param array|string $mixOption            
     * @return \queryyetsimple\cache\repository
     */
    public function connect($mixOption = []) {
        if (is_string ( $mixOption ) && ! is_array ( ($mixOption = $this->objContainer ['option'] ['cache\\connect.' . $mixOption]) )) {
            $mixOption = [ ];
        }
        
        $strDriver = ! empty ( $mixOption ['driver'] ) ? $mixOption ['driver'] : $this->getDefaultDriver ();
        $strUnique = $this->getUnique ( $mixOption );
        
        if (isset ( static::$arrConnect [$strUnique] )) {
            return static::$arrConnect [$strUnique];
        }
        
        return static::$arrConnect [$strUnique] = $this->makeConnect ( $strDriver, $mixOption );
    }
    
    /**
     * 创建一个缓存仓库
     *
     * @param \queryyetsimple\cache\iconnect $objCache            
     * @return \queryyetsimple\cache\repository
     */
    public function repository(iconnect $objCache) {
        return new repository ( $objCache );
    }
    
    /**
     * 返回默认驱动
     *
     * @return string
     */
    public function getDefaultDriver() {
        return $this->objContainer ['option'] ['cache\default'];
    }
    
    /**
     * 设置默认驱动
     *
     * @param string $strName            
     * @return void
     */
    public function setDefaultDriver($strName) {
        $this->objContainer ['option'] ['cache\default'] = $strName;
    }
    
    /**
     * 创建连接
     *
     * @param string $strConnect            
     * @param array $arrOption            
     * @return \queryyetsimple\cache\repository
     */
    protected function makeConnect($strConnect, $arrOption = []) {
        if (is_null ( $this->objContainer ['option'] ['cache\connect.' . $strConnect] ))
            throw new Exception ( __ ( '缓存驱动 %s 不存在', $strConnect ) );
        return $this->{'makeConnect' . ucfirst ( $strConnect )} ( $arrOption );
    }
    
    /**
     * 创建文件缓存
     *
     * @param array $arrOption            
     * @return \queryyetsimple\cache\repository
     */
    protected function makeConnectFile($arrOption = []) {
        return $this->repository ( new file ( array_merge ( $this->getOption ( 'file', $arrOption ) ) ) );
    }
    
    /**
     * 创建 memcache 缓存
     *
     * @param array $arrOption            
     * @return \queryyetsimple\cache\repository
     */
    protected function makeConnectMemcache($arrOption = []) {
        return $this->repository ( new memcache ( array_merge ( $this->getOption ( 'memcache', $arrOption ) ) ) );
    }
    
    /**
     * 创建 redis 缓存
     *
     * @param array $arrOption            
     * @return \queryyetsimple\cache\repository
     */
    protected function makeConnectRedis($arrOption = []) {
        return $this->repository ( new redis ( array_merge ( $this->getOption ( 'redis', $arrOption ) ) ) );
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
     * 读取默认缓存配置
     *
     * @param string $strConnect            
     * @param array $arrExtendOption            
     * @return array
     */
    protected function getOption($strConnect, array $arrExtendOption = []) {
        $arrOption = $this->objContainer ['option'] ['cache\\'];
        unset ( $arrOption ['default'], $arrOption ['connect'] );
        
        return array_merge ( array_filter ( $this->objContainer ['option'] ['cache\connect.' . $strConnect], function ($mixValue) {
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
