<?php
/*
 * This file is part of the ************************ package.
 * ##########################################################
 * #   ____                          ______  _   _ ______   #
 * #  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
 * # |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
 * #  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
 * #       \__   | \___ |_|    \__  || |    | | | || |      #
 * #     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
 * #                          |___ /  Since 2010.10.03      #
 * ##########################################################
 * 
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2017 http://queryphp.com All rights reserved.
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace queryyetsimple\filesystem;

use queryyetsimple\support\manager as support_manager;

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
class manager extends support_manager
{
    
    /**
     * 取得配置命名空间
     *
     * @return string
     */
    protected function getOptionNamespace()
    {
        return 'filesystem';
    }
    
    /**
     * 创建连接对象
     *
     * @param object $objConnect
     * @return object
     */
    protected function createConnect($objConnect)
    {
        return new filesystem($objConnect);
    }
    
    /**
     * 创建 local 连接
     *
     * @param array $arrOption
     * @return \queryyetsimple\filesystem\local
     */
    protected function makeConnectLocal($arrOption = [])
    {
        return new local(array_merge($this->getOption('local', $arrOption)));
    }
    
    /**
     * 创建 ftp 连接
     *
     * @param array $arrOption
     * @return \queryyetsimple\filesystem\ftp
     */
    protected function makeConnectFtp($arrOption = [])
    {
        return new ftp(array_merge($this->getOption('ftp', $arrOption)));
    }
    
    /**
     * 创建 sftp 连接
     *
     * @param array $arrOption
     * @return \queryyetsimple\filesystem\sftp
     */
    protected function makeConnectSftp($arrOption = [])
    {
        return new sftp(array_merge($this->getOption('sftp', $arrOption)));
    }
    
    /**
     * 创建 zip 连接
     *
     * @param array $arrOption
     * @return \queryyetsimple\filesystem\zip
     */
    protected function makeConnectZip($arrOption = [])
    {
        return new zip(array_merge($this->getOption('zip', $arrOption)));
    }
}
