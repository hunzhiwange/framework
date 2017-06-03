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
class filecache extends abstracts_cache {
    
    use classs_faces;
    
    /**
     * 缓存惯性配置
     *
     * @var array
     */
    private $arrDefaultOption = [ 
            'json' => true,
            'cache_path' => '' 
    ];
    
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
            'cache\connect.filecache.path' => '' 
    ];
    
    /**
     * 构造函数
     *
     * @param array $arrOption            
     * @return void
     */
    public function __construct(array $arrOption = []) {
        $this->arrOption = array_merge ( $this->arrOption, $this->arrDefaultOption );
        if (! empty ( $arrOption )) {
            $this->arrOption = array_merge ( $this->arrOption, $arrOption );
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
        if ($arrOption ['json']) {
            $strData = json_decode ( $strData, true );
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
        if ($arrOption ['json']) {
            $mixData = json_encode ( $mixData );
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
        ! isset ( $arrOption ['cache_time'] ) && $arrOption ['cache_time'] = - 1;
        return ( int ) $arrOption ['cache_time'] !== - 1 && filemtime ( $sFilePath ) + ( int ) $arrOption ['cache_time'] < time ();
    }
    
    /**
     * 获取缓存路径
     *
     * @param string $sCacheName            
     * @param array $arrOption            
     * @return string
     */
    private function getCachePath($sCacheName, $arrOption) {
        ! $arrOption ['cache_path'] && $arrOption ['cache_path'] = $this->classsFacesOption ( 'cache\connect.filecache.path' );
        if (! $arrOption ['cache_path'])
            throw new InvalidArgumentException ( 'Cache path is not allowed empty.' );
        
        if (! is_dir ( $arrOption ['cache_path'] )) {
            directory::create ( $arrOption ['cache_path'] );
        }
        return $arrOption ['cache_path'] . '/' . $this->getCacheName ( $sCacheName, $arrOption ) . '.php';
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
        $sCachePath = $this->getCachePath ( $sCacheName, $arrOption );
        return is_file ( $sCachePath );
    }
}
