<?php
// [$QueryPHP] A PHP Framework For Simple As Free As Wind. <Query Yet Simple>
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
use queryyetsimple\bootstrap\project;
use queryyetsimple\cache\interfaces\connect;
use queryyetsimple\cache\interfaces\cache as interfaces_cache;

/**
 * 缓存入口
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2017.02.15
 * @version 1.0
 */
class cache implements interfaces_cache {
    
    /**
     * 项目管理
     *
     * @var \queryyetsimple\bootstrap\project
     */
    protected $objProject;
    
    /**
     * 缓存连接对象
     *
     * @var array(\queryyetsimple\cache\repository)
     */
    protected static $arrConnect;
    
    /**
     * 构造函数
     *
     * @param \queryyetsimple\bootstrap\project $objConnect            
     * @return void
     */
    public function __construct(project $objProject) {
        $this->objProject = $objProject;
    }
    
    /**
     * 连接缓存并返回连接对象
     *
     * @param array|string $mixOption            
     * @return \queryyetsimple\cache\repository
     */
    public function connect($mixOption = []) {
        if (is_string ( $mixOption ) && ! is_array ( ($mixOption = $this->objProject ['option'] ['cache\\connect.' . $mixOption]) )) {
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
     * @param \queryyetsimple\cache\interfaces\connect $objCache            
     * @return \queryyetsimple\cache\repository
     */
    public function repository(connect $objCache) {
        return new repository ( $objCache );
    }
    
    /**
     * 返回默认驱动
     *
     * @return string
     */
    public function getDefaultDriver() {
        return $this->objProject ['option'] ['cache\default'];
    }
    
    /**
     * 设置默认驱动
     *
     * @param string $strName            
     * @return void
     */
    public function setDefaultDriver($strName) {
        $this->objProject ['option'] ['cache\default'] = $strName;
    }
    
    /**
     * 创建连接
     *
     * @param string $strConnect            
     * @param array $arrOption            
     * @return \queryyetsimple\cache\repository
     */
    protected function makeConnect($strConnect, $arrOption = []) {
        if (is_null ( $this->objProject ['option'] ['cache\connect.' . $strConnect] ))
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
        return $this->repository ( new file ( array_merge ( $this->getOption ( 'file' ), $arrOption ) ) );
    }
    
    /**
     * 创建 memcache 缓存
     *
     * @param array $arrOption            
     * @return \queryyetsimple\cache\repository
     */
    protected function makeConnectMemcache($arrOption = []) {
        return $this->repository ( new memcache ( array_merge ( $this->getOption ( 'memcache' ), $arrOption ) ) );
    }
    
    /**
     * 创建 redis 缓存
     *
     * @param array $arrOption            
     * @return \queryyetsimple\cache\repository
     */
    protected function makeConnectRedis($arrOption = []) {
        return $this->repository ( new redis ( array_merge ( $this->getOption ( 'redis' ), $arrOption ) ) );
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
     * @return array
     */
    protected function getOption($strConnect) {
        $arrOption = $this->objProject ['option'] ['cache\\'];
        unset ( $arrOption ['default'], $arrOption ['connect'] );
        
        return array_merge ( array_filter ( $this->objProject ['option'] ['cache\connect.' . $strConnect], function ($mixValue) {
            return ! is_null ( $mixValue );
        } ), $arrOption );
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
