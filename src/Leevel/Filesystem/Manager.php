<?php

declare(strict_types=1);

namespace Leevel\Filesystem;

use Leevel\Support\Manager as Managers;

/**
 * 文件系统管理器.
 *
 * @method static bool                               fileExists(string $location)
 * @method static void                               write(string $location, string $contents, array $config = [])
 * @method static void                               writeStream(string $location, $contents, array $config = [])
 * @method static string                             read(string $location)
 * @method static mixed                              readStream(string $location)
 * @method static void                               delete(string $location)
 * @method static void                               deleteDirectory(string $location)
 * @method static void                               createDirectory(string $location, array $config = [])
 * @method static \League\Flysystem\DirectoryListing listContents(string $location, bool $deep = false)
 * @method static void                               move(string $source, string $destination, array $config = [])
 * @method static void                               copy(string $source, string $destination, array $config = [])
 * @method static int                                lastModified(string $path)
 * @method static int                                fileSize(string $path)
 * @method static string                             mimeType(string $path)
 * @method static void                               setVisibility(string $path, string $visibility)
 * @method static string                             visibility(string $path)
 * @method static \Leevel\Di\IContainer              container()                                                   返回 IOC 容器.
 * @method static void                               disconnect(?string $connect = null)                           删除连接.
 * @method static array                              getConnects()                                                 取回所有连接.
 * @method static string                             getDefaultConnect()                                           返回默认连接.
 * @method static void                               setDefaultConnect(string $name)                               设置默认连接.
 * @method static mixed                              getContainerConfig(?string $name = null)                      获取容器配置值.
 * @method static void                               setContainerConfig(string $name, mixed $value)                设置容器配置值.
 * @method static void                               extend(string $connect, \Closure $callback)                   扩展自定义连接.
 * @method static array                              normalizeConnectConfig(string $connect)                       整理连接配置.
 */
class Manager extends Managers
{
    /**
     * {@inheritDoc}
     */
    public function connect(?string $connect = null, bool $newConnect = false, ...$arguments): IFilesystem
    {
        return parent::connect($connect, $newConnect, ...$arguments);
    }

    /**
     * {@inheritDoc}
     */
    public function reconnect(?string $connect = null, ...$arguments): IFilesystem
    {
        return parent::reconnect($connect, ...$arguments);
    }

    /**
     * 取得配置命名空间.
     */
    protected function getConfigNamespace(): string
    {
        return 'filesystem';
    }

    /**
     * 创建 local 连接.
     */
    protected function makeConnectLocal(string $connect, ?string $driverClass = null): Local
    {
        $driverClass = $this->getDriverClass(Local::class, $driverClass);

        return new $driverClass(
            $this->normalizeConnectConfig($connect)
        );
    }

    /**
     * 创建 ftp 连接.
     */
    protected function makeConnectFtp(string $connect, ?string $driverClass = null): Ftp
    {
        $driverClass = $this->getDriverClass(Ftp::class, $driverClass);

        return new $driverClass(
            $this->normalizeConnectConfig($connect)
        );
    }

    /**
     * 创建 sftp 连接.
     */
    protected function makeConnectSftp(string $connect, ?string $driverClass = null): Sftp
    {
        $driverClass = $this->getDriverClass(Sftp::class, $driverClass);

        return new $driverClass(
            $this->normalizeConnectConfig($connect)
        );
    }

    /**
     * 创建 zip 连接.
     */
    protected function makeConnectZip(string $connect, ?string $driverClass = null): Zip
    {
        $driverClass = $this->getDriverClass(Zip::class, $driverClass);

        return new $driverClass(
            $this->normalizeConnectConfig($connect)
        );
    }
}
