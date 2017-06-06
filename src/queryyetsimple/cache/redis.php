<?php
// [$QueryPHP] A PHP Framework Since 2010.10.03. <Query Yet Simple>
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

use RuntimeException;
use Redis as Rediss;
use queryyetsimple\cache\abstracts\cache as abstracts_cache;
use queryyetsimple\classs\faces as classs_faces;

/**
 * redis 扩展缓存
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2017.06.05
 * @version 1.0
 */
class redis extends abstracts_cache {
    
    use classs_faces;
    
    /**
     * 配置
     *
     * @var array
     */
    protected $arrClasssFacesOption = [ 
            'cache\nocache_force' => '~@nocache_force',
            'cache\time_preset' => [ ],
            'cache\global_prefix' => '~@',
            'cache\global_expire' => 86400,
            'cache\connect.redis.host' => '127.0.0.1',
            'cache\connect.redis.port' => 6379,
            'cache\connect.redis.password' => '',
            'cache\connect.redis.select' => 0,
            'cache\connect.redis.timeout' => 0,
            'cache\connect.redis.persistent' => false,
            'cache\connect.redis.serialize' => true,
            'cache\connect.redis.prefix' => null,
            'cache\connect.redis.expire' => null 
    ];
    
    /**
     * 构造函数
     *
     * @param array $arrOption            
     * @return void
     */
    public function __construct(array $arrOption = []) {
        if (! extension_loaded ( 'redis' )) {
            throw new RuntimeException ( 'redis extension must be loaded before use.' );
        }
        
        $this->initialization ( $arrOption );
        
        $this->hHandle = $this->getRedis ();
        $this->hHandle->{$this->arrOption ['persistent'] ? 'pconnect' : 'connect'} ( $this->arrOption ['host'], $this->arrOption ['port'], $this->arrOption ['timeout'] );
        
        if ($this->arrOption ['password']) {
            $this->hHandle->auth ( $this->arrOption ['password'] );
        }
        
        if ($this->arrOption ['select']) {
            $this->hHandle->select ( $this->arrOption ['select'] );
        }
    }
    
    /**
     * 获取缓存
     *
     * @param string $sCacheName            
     * @param mixed $mixDefault            
     * @param array $arrOption            
     * @return mixed
     */
    public function get($sCacheName, $mixDefault = false, array $arrOption = []) {
        if ($this->checkForce ())
            return $mixDefault;
        
        $arrOption = $this->option ( $arrOption, null, false );
        $mixData = $this->hHandle->get ( $this->getCacheName ( $sCacheName, $arrOption ) );
        if (is_null ( $mixData )) {
            return $mixDefault;
        }
        if ($arrOption ['serialize']) {
            $mixData = unserialize ( $mixData );
        }
        return $mixData;
    }
    
    /**
     * 设置缓存
     *
     * @param string $sCacheName            
     * @param mixed $mixData            
     * @param array $arrOption            
     * @return void
     */
    public function set($sCacheName, $mixData, array $arrOption = []) {
        $arrOption = $this->option ( $arrOption, null, false );
        if ($arrOption ['serialize']) {
            $mixData = serialize ( $mixData );
        }
        
        $arrOption ['expire'] = $this->cacheTime ( $sCacheName, $arrOption ['expire'] );
        
        if (( int ) $arrOption ['expire'])
            $this->hHandle->setex ( $this->getCacheName ( $sCacheName, $arrOption ), ( int ) $arrOption ['expire'], $mixData );
        else
            $this->hHandle->set ( $this->getCacheName ( $sCacheName, $arrOption ), $mixData );
    }
    
    /**
     * 清除缓存
     *
     * @param string $sCacheName            
     * @param array $arrOption            
     * @return void
     */
    public function delele($sCacheName, array $arrOption = []) {
        $this->hHandle->delete ( $this->getCacheName ( $sCacheName, $this->option ( $arrOption, null, false ) ) );
    }
    
    /**
     * 返回 redis 对象
     *
     * @return \Redis
     */
    protected function getRedis() {
        return new Rediss ();
    }
}
