<?php

declare(strict_types=1);

namespace Leevel\Filesystem;

use Leevel\Manager\Manager as Managers;

/**
 * 文件系统管理器.
 *
 * @method static \League\Flysystem\Filesystem getFilesystem()                                               返回 Filesystem.
 * @method static bool has(string $path)                                                                     判断文件是否存在.
 * @method static mixed read(string $path)                                                                   读取文件.
 * @method static mixed readStream(string $path)                                                             从路径读取流数据.
 * @method static array listContents(string $directory = '', bool $recursive = false)                        读取文件目录.
 * @method static mixed getMetadata(string $path)                                                            获取文件元数据.
 * @method static mixed getSize(string $path)                                                                获取文件大小.
 * @method static mixed getMimetype(string $path)                                                            获取文件的 mime 类型.
 * @method static mixed getTimestamp(string $path)                                                           获取文件的时间戳.
 * @method static mixed getVisibility(string $path)                                                          获取文件的可见性.
 * @method static bool write(string $path, string $contents, array $config = [])                             写一个新文件.
 * @method static bool writeStream(string $path, $resource, array $config = [])                              使用流写入新文件.
 * @method static bool update(string $path, string $contents, array $config = [])                            更新现有文件.
 * @method static bool updateStream(string $path, $resource, array $config = [])                             使用流更新现有文件.
 * @method static bool rename(string $path, string $newpath)                                                 重命名文件.
 * @method static bool copy(string $path, string $newpath)                                                   复制文件.
 * @method static bool delete(string $path)                                                                  删除文件.
 * @method static bool deleteDir(string $dirname)                                                            删除文件夹.
 * @method static bool createDir(string $dirname, array $config = [])                                        创建一个文件夹.
 * @method static bool setVisibility(string $path, $visibility)                                              设置文件的可见性.
 * @method static bool put(string $path, string $contents, array $config = [])                               创建或者更新文件.
 * @method static bool putStream(string $path, $resource, array $config = [])                                使用流创建或者更新文件.
 * @method static mixed readAndDelete(string $path)                                                          读取并删除一个文件.
 * @method static \League\Flysystem\FilesystemInterface addPlugin(\League\Flysystem\PluginInterface $plugin) 注册一个插件.
 * @method static \Leevel\Di\IContainer container() 返回 IOC 容器. 
 * @method static object connect(?string $connect = null, bool $onlyNew = false) 连接并返回连接对象. 
 * @method static object reconnect(?string $connect = null) 重新连接. 
 * @method static void disconnect(?string $connect = null) 删除连接. 
 * @method static array getConnects() 取回所有连接. 
 * @method static string getDefaultConnect() 返回默认连接. 
 * @method static void setDefaultConnect(string $name) 设置默认连接. 
 * @method static mixed getContainerOption(?string $name = null) 获取容器配置值. 
 * @method static void setContainerOption(string $name, mixed $value) 设置容器配置值. 
 * @method static void extend(string $connect, \Closure $callback) 扩展自定义连接. 
 * @method static array normalizeConnectOption(string $connect) 整理连接配置. 
 */
class Manager extends Managers
{
    /**
     * 取得配置命名空间.
     */
    protected function getOptionNamespace(): string
    {
        return 'filesystem';
    }

    /**
     * 创建 local 连接.
     */
    protected function makeConnectLocal(string $connect): Local
    {
        return new Local(
            $this->normalizeConnectOption($connect)
        );
    }

    /**
     * 创建 ftp 连接.
     */
    protected function makeConnectFtp(string $connect): Ftp
    {
        return new Ftp(
            $this->normalizeConnectOption($connect)
        );
    }

    /**
     * 创建 sftp 连接.
     */
    protected function makeConnectSftp(string $connect): Sftp
    {
        return new Sftp(
            $this->normalizeConnectOption($connect)
        );
    }

    /**
     * 创建 zip 连接.
     */
    protected function makeConnectZip(string $connect): Zip
    {
        return new Zip(
            $this->normalizeConnectOption($connect)
        );
    }
}
