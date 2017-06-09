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
use Memcache as Memcaches;
use queryyetsimple\classs\faces as classs_faces;
use queryyetsimple\cache\abstracts\cache as abstracts_cache;
use queryyetsimple\cache\interfaces\cache as interfaces_cache;

/**
 * memcache 扩展缓存
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2017.02.15
 * @version 1.0
 */
class memcache extends abstracts_cache implements interfaces_cache {
    
    use classs_faces;
    
    /**
     * 配置
     *
     * @var array
     */
    protected $arrClasssFacesOption = [ 
            'cache\nocache_force' => '~@nocache_force',
            'cache\time_preset' => [ ],
            'cache\prefix' => '~@',
            'cache\expire' => 86400,
            'cache\connect.memcache.servers' => [ ],
            'cache\connect.memcache.host' => '127.0.0.1',
            'cache\connect.memcache.port' => 11211,
            'cache\connect.memcache.compressed' => false,
            'cache\connect.memcache.persistent' => false,
            'cache\connect.memcache.prefix' => null,
            'cache\connect.memcache.expire' => null 
    ];
    
    /**
     * 构造函数
     *
     * @param array $arrOption            
     * @return void
     */
    public function __construct(array $arrOption = []) {
        if (! extension_loaded ( 'memcache' )) {
            throw new RuntimeException ( 'Memcache extension must be loaded before use.' );
        }
        
        $this->initialization ( $arrOption );
        
        if (empty ( $this->arrOption ['servers'] )) {
            $this->arrOption ['servers'] [] = [ 
                    'host' => $this->classsFacesOption ( 'cache\connect.memcache.host' ),
                    'port' => $this->classsFacesOption ( 'cache\connect.memcache.port' ) 
            ];
        }
        
        // 连接缓存服务器
        $this->hHandle = $this->getMemcache ();
        
        foreach ( $this->arrOption ['servers'] as $arrServer ) {
            $bResult = $this->hHandle->addServer ( $arrServer ['host'], $arrServer ['port'], $this->arrOption ['persistent'] );
            if (! $bResult) {
                throw new RuntimeException ( sprintf ( 'Unable to connect the memcached server [%s:%s] failed.', $arrServer ['host'], $arrServer ['port'] ) );
            }
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
        
        $mixData = $this->hHandle->get ( $this->getCacheName ( $sCacheName, $this->option ( $arrOption, null, false ) ) );
        return $mixData === false ? $mixDefault : $mixData;
    }
    
    /**
     * 设置缓存
     *
     * memcache 0 表示永不过期
     *
     * @param string $sCacheName            
     * @param mixed $mixData            
     * @param array $arrOption            
     * @return void
     */
    public function set($sCacheName, $mixData, array $arrOption = []) {
        $arrOption = $this->option ( $arrOption, null, false );
        $arrOption ['expire'] = $this->cacheTime ( $sCacheName, $arrOption ['expire'] );
        $this->hHandle->set ( $this->getCacheName ( $sCacheName, $arrOption ), $mixData, $arrOption ['compressed'] ? MEMCACHE_COMPRESSED : 0, ( int ) $arrOption ['expire'] <= 0 ? 0 : ( int ) $arrOption ['expire'] );
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
     * 关闭 memcache
     *
     * @return void
     */
    public function close() {
        $this->hHandle->close ();
        $this->hHandle = null;
    }
    
    /**
     * 返回 memcache 对象
     *
     * @return \Memcache
     */
    protected function getMemcache() {
        return new Memcaches ();
    }
}
