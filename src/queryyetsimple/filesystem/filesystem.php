<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\filesystem;

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
use queryyetsimple\support\interfaces\container;

/**
 * filesystem 入口
 *
 * @method bool has(string $path)
 * @method false|string read(string $path)
 * @method false|resource readStream(string $path)
 * @method array listContents(string $directory = '', bool $recursive = false)
 * @method false|array getMetadata(string $path)
 * @method false|int getSize(string $path)
 * @method false|string getMimetype(string $path)
 * @method false|int getTimestamp(string $path)
 * @method false|string getVisibility(string $path)
 * @method bool write(string $path, string $contents, array $config = [])
 * @method bool writeStream(string $path, resource $resource, array $config = [])
 * @method bool update(string $path, string $contents, array $config = [])
 * @method bool updateStream(string $path, resource $resource, array $config = [])
 * @method bool rename(string $path, string $newpath)
 * @method bool copy(string $path, string $newpath)
 * @method bool delete(string $path)
 * @method bool deleteDir(string $dirname)
 * @method bool createDir(string $dirname, array $config = [])
 * @method bool setVisibility(sring $path, string $visibility)
 * @method bool put(string $path, string $contents, array $config = [])
 * @method bool putStream(string $path, resource $resource, array $config = [])
 * @method string readAndDelete(string $path)
 * @method \League\Flysystem\Handler get(string $path, \League\Flysystem\Handler $handler = null)
 * @method \League\Flysystem\FilesystemInterface addPlugin(\League\Flysystem\PluginInterface $plugin)
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.08.29
 * @version 1.0
 */
class filesystem implements ifilesystem {
    
    /**
     * IOC Container
     *
     * @var \queryyetsimple\support\interfaces\container
     */
    protected $objContainer;
    
    /**
     * filesystem 连接对象
     *
     * @var \queryyetsimple\filesystem\store[]
     */
    protected static $arrConnect;
    
    /**
     * 构造函数
     *
     * @param \queryyetsimple\support\interfaces\container $objContainer            
     * @return void
     */
    public function __construct(container $objContainer) {
        $this->objContainer = $objContainer;
    }
    
    /**
     * 连接 filesystem 并返回连接对象
     *
     * @param array|string $mixOption            
     * @return \queryyetsimple\filesystem\store
     */
    public function connect($mixOption = []) {
        if (is_string ( $mixOption ) && ! is_array ( ($mixOption = $this->objContainer ['option'] ['filesystem\\connect.' . $mixOption]) )) {
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
     * 创建 filesystem store
     *
     * @param \queryyetsimple\filesystem\iconnect $oConnect            
     * @return \queryyetsimple\filesystem\store
     */
    public function store($oConnect) {
        return new store ( $oConnect );
    }
    
    /**
     * 返回默认驱动
     *
     * @return string
     */
    public function getDefaultDriver() {
        return $this->objContainer ['option'] ['filesystem\default'];
    }
    
    /**
     * 设置默认驱动
     *
     * @param string $strName            
     * @return void
     */
    public function setDefaultDriver($strName) {
        $this->objContainer ['option'] ['filesystem\default'] = $strName;
    }
    
    /**
     * 创建连接
     *
     * @param string $strConnect            
     * @param array $arrOption            
     * @return \queryyetsimple\filesystem\iconnect
     */
    protected function makeConnect($strConnect, $arrOption = []) {
        if (is_null ( $this->objContainer ['option'] ['filesystem\connect.' . $strConnect] ))
            throw new Exception ( sprintf ( 'Filesystem driver %s not exits', $strConnect ) );
        return $this->{'makeConnect' . ucfirst ( $strConnect )} ( $arrOption );
    }
    
    /**
     * 创建 local 连接
     *
     * @param array $arrOption            
     * @return \queryyetsimple\filesystem\local
     */
    protected function makeConnectLocal($arrOption = []) {
        return new local ( array_merge ( $this->getOption ( 'local', $arrOption ) ) );
    }
    
    /**
     * 创建 ftp 连接
     *
     * @param array $arrOption            
     * @return \queryyetsimple\filesystem\ftp
     */
    protected function makeConnectFtp($arrOption = []) {
        return new ftp ( array_merge ( $this->getOption ( 'ftp', $arrOption ) ) );
    }
    
    /**
     * 创建 sftp 连接
     *
     * @param array $arrOption            
     * @return \queryyetsimple\filesystem\sftp
     */
    protected function makeConnectSftp($arrOption = []) {
        return new sftp ( array_merge ( $this->getOption ( 'sftp', $arrOption ) ) );
    }
    
    /**
     * 创建 zip 连接
     *
     * @param array $arrOption            
     * @return \queryyetsimple\filesystem\zip
     */
    protected function makeConnectZip($arrOption = []) {
        return new zip ( array_merge ( $this->getOption ( 'zip', $arrOption ) ) );
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
     * 读取默认 filesystem 配置
     *
     * @param string $strConnect            
     * @param array $arrExtendOption            
     * @return array
     */
    protected function getOption($strConnect, array $arrExtendOption = []) {
        $arrOption = $this->objContainer ['option'] ['filesystem\\'];
        unset ( $arrOption ['default'], $arrOption ['connect'] );
        return array_merge ( $this->objContainer ['option'] ['filesystem\connect.' . $strConnect], $arrOption, $arrExtendOption );
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
