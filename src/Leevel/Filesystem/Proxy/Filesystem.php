<?php

declare(strict_types=1);

namespace Leevel\Filesystem\Proxy;

use Leevel\Di\Container;
use Leevel\Filesystem\Manager;

/**
 * 代理 filesystem.
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
 * @method static \Leevel\Filesystem\IFilesystem     connect(?string $connect = null, bool $newConnect = false)    连接并返回连接对象.
 * @method static \Leevel\Filesystem\IFilesystem     reconnect(?string $connect = null)                            重新连接.
 * @method static void                               disconnect(?string $connect = null)                           删除连接.
 * @method static array                              getConnects()                                                 取回所有连接.
 * @method static string                             getDefaultConnect()                                           返回默认连接.
 * @method static void                               setDefaultConnect(string $name)                               设置默认连接.
 * @method static mixed                              getContainerConfig(?string $name = null)                      获取容器配置值.
 * @method static void                               setContainerConfig(string $name, mixed $value)                设置容器配置值.
 * @method static void                               extend(string $connect, \Closure $callback)                   扩展自定义连接.
 * @method static array                              normalizeConnectConfig(string $connect)                       整理连接配置.
 */
class Filesystem
{
    /**
     * 实现魔术方法 __callStatic.
     */
    public static function __callStatic(string $method, array $args): mixed
    {
        return self::proxy()->{$method}(...$args);
    }

    /**
     * 代理服务.
     */
    public static function proxy(): Manager
    {
        // @phpstan-ignore-next-line
        return Container::singletons()->make('filesystems');
    }
}
