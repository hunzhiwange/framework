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
use queryyetsimple\cache\abstracts\cache as abstracts_cache;
use queryyetsimple\classs\faces as classs_faces;

/**
 * memcache 扩展缓存
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2017.02.15
 * @version 1.0
 */
class memcache extends abstracts_cache {
    
    use classs_faces;
    
    /**
     * 缓存惯性配置
     *
     * @var array
     */
    private $arrDefaultOption = [ 
            'servers' => [ ],
            
            // 是否压缩缓存数据
            'compressed' => false,
            
            // 是否使用持久连接
            'persistent' => true 
    ];
    
    /**
     * 缓存服务句柄
     *
     * @var handle
     */
    private $hHandel;
    
    /**
     * 配置
     *
     * @var array
     */
    protected $arrClasssFacesOption = [ 
            'cache\connect.memcache.servers' => [ ],
            'cache\connect.memcache.host' => '127.0.0.1',
            'cache\connect.memcache.port' => 11211,
            'cache\connect.memcache.compressed' => false,
            'cache\connect.memcache.persistent' => true 
    ];
    
    /**
     * 构造函数
     *
     * @param array $arrOption            
     * @return void
     */
    public function __construct(array $arrOption = []) {
        if (! extension_loaded ( 'memcache' )) {
            throw new RuntimeException ( 'memcache extension must be loaded before use.' );
        }
        
        // 合并默认配置
        $this->arrOption = array_merge ( $this->arrOption, $this->arrDefaultOption );
        $this->arrOption ['compressed'] = $this->classsFacesOption ( 'cache\connect.memcache.compressed' );
        $this->arrOption ['persistent'] = $this->classsFacesOption ( 'cache\connect.memcache.persistent' );
        
        if (is_array ( $arrOption )) {
            $this->arrOption = array_merge ( $this->arrOption, $arrOption );
        }
        
        if (empty ( $this->arrOption ['servers'] )) {
            if (! empty ( $this->classsFacesOption ( 'cache\connect.memcache.servers' ) )) {
                $this->arrOption ['servers'] = $this->classsFacesOption ( 'cache\connect.memcache.servers' );
            } else {
                $this->arrOption ['servers'] [] = [ 
                        'host' => $this->classsFacesOption ( 'cache\connect.memcache.host' ),
                        'port' => $this->classsFacesOption ( 'cache\connect.memcache.port' ) 
                ];
            }
        }
        
        // 连接缓存服务器
        $this->hHandel = new Memcache ();
        
        foreach ( $this->arrOption ['servers'] as $arrServer ) {
            $bResult = $this->hHandel->addServer ( $arrServer ['host'], $arrServer ['port'], $this->arrOption ['persistent'] );
            if (! $bResult) {
                throw new RuntimeException ( sprintf ( 'Unable to connect the memcached server [%s:%s] failed.', $arrServer ['host'], $arrServer ['port'] ) );
            }
        }
    }
    
    /**
     * 获取缓存
     *
     * @param string $sCacheName            
     * @param array $arrOption            
     * @return mixed
     */
    public function get($sCacheName, array $arrOption = []) {
        $arrOption = $this->option ( $arrOption, null, false );
        return $this->hHandel->get ( $this->getCacheName ( $sCacheName, $arrOption ) );
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
        $this->hHandel->set ( $this->getCacheName ( $sCacheName, $arrOption ), $mixData, $arrOption ['compressed'] ? MEMCACHE_COMPRESSED : 0, ( int ) $arrOption ['cache_time'] === - 1 ? 0 : ( int ) $arrOption ['cache_time'] );
    }
    
    /**
     * 清除缓存
     *
     * @param string $sCacheName            
     * @param array $arrOption            
     * @return void
     */
    public function delele($sCacheName, array $arrOption = []) {
        $arrOption = $this->option ( $arrOption, null, false );
        $this->hHandel->delete ( $this->getCacheName ( $sCacheName, $arrOption ) );
    }
}
