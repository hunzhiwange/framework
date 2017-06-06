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

use InvalidArgumentException;
use queryyetsimple\filesystem\directory;
use queryyetsimple\cache\abstracts\cache as abstracts_cache;
use queryyetsimple\classs\faces as classs_faces;

/**
 * 文件缓存
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2017.02.15
 * @version 1.0
 */
class file extends abstracts_cache {
    
    use classs_faces;
    
    /**
     * 缓存文件头部
     *
     * @var string
     */
    const HEADER = '<?php die(); ?>';
    
    /**
     * 缓存文件头部长度
     *
     * @var int
     */
    const HEADER_LENGTH = 15;
    
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
            'cache\connect.file.path' => '',
            'cache\connect.file.serialize' => true,
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
        $this->initialization ( $arrOption );
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
        $sCachePath = $this->getCachePath ( $sCacheName, $arrOption );
        
        // 清理文件状态缓存 http://php.net/manual/zh/function.clearstatcache.php
        clearstatcache ();
        
        if (! is_file ( $sCachePath )) {
            return false;
        }
        
        $hFp = fopen ( $sCachePath, 'rb' );
        if (! $hFp) {
            return false;
        }
        flock ( $hFp, LOCK_SH );
        
        // 头部的 15 个字节存储了安全代码
        $nLen = filesize ( $sCachePath );
        $sHead = fread ( $hFp, static::HEADER_LENGTH );
        $nLen -= static::HEADER_LENGTH;
        
        do {
            // 检查缓存是否已经过期
            if ($this->isExpired ( $sCacheName, $arrOption )) {
                $strData = false;
                break;
            }
            
            if ($nLen > 0) {
                $strData = fread ( $hFp, $nLen );
            } else {
                $strData = false;
            }
        } while ( false );
        
        flock ( $hFp, LOCK_UN );
        fclose ( $hFp );
        
        if ($strData === false) {
            return false;
        }
        
        // 解码
        if ($arrOption ['serialize']) {
            $strData = unserialize ( $strData );
        }
        
        return $strData;
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
        $mixData = static::HEADER . $mixData;
        
        $sCachePath = $this->getCachePath ( $sCacheName, $arrOption );
        $this->writeData ( $sCachePath, $mixData );
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
        $sCachePath = $this->getCachePath ( $sCacheName, $arrOption );
        if ($this->exist ( $sCacheName, $arrOption )) {
            @unlink ( $sCachePath );
        }
    }
    
    /**
     * 验证缓存是否过期
     *
     * @param string $sCacheName            
     * @param array $arrOption            
     * @return boolean
     */
    private function isExpired($sCacheName, $arrOption) {
        $sFilePath = $this->getCachePath ( $sCacheName, $arrOption );
        if (! is_file ( $sFilePath )) {
            return true;
        }
        $arrOption ['expire'] = $this->cacheTime ( $sCacheName, $arrOption ['expire'] );
        return ( int ) $arrOption ['expire'] > 0 && filemtime ( $sFilePath ) + ( int ) $arrOption ['expire'] < time ();
    }
    
    /**
     * 获取缓存路径
     *
     * @param string $sCacheName            
     * @param array $arrOption            
     * @return string
     */
    private function getCachePath($sCacheName, $arrOption) {
        if (! $arrOption ['path'])
            throw new InvalidArgumentException ( 'Cache path is not allowed empty.' );
        
        if (! is_dir ( $arrOption ['path'] )) {
            directory::create ( $arrOption ['path'] );
        }
        return $arrOption ['path'] . '/' . $this->getCacheName ( $sCacheName, $arrOption ) . '.php';
    }
    
    /**
     * 写入缓存数据
     *
     * @param string $sFileName            
     * @param string $sData            
     * @return void
     */
    private function writeData($sFileName, $sData) {
        ! is_dir ( dirname ( $sFileName ) ) && directory::create ( dirname ( $sFileName ) );
        file_put_contents ( $sFileName, $sData, LOCK_EX );
    }
    
    /**
     * 验证缓存是否存在
     *
     * @param string $sCacheName            
     * @param array $arrOption            
     * @return boolean
     */
    private function exist($sCacheName, $arrOption) {
        return is_file ( $this->getCachePath ( $sCacheName, $arrOption ) );
    }
}
