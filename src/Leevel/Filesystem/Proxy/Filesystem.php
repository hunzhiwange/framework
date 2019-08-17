<?php

declare(strict_types=1);

/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Filesystem\Proxy;

use League\Flysystem\FilesystemInterface;
use League\Flysystem\PluginInterface;
use Leevel\Di\Container;
use Leevel\Filesystem\IFilesystem as IBaseFilesystem;
use Leevel\Filesystem\Manager;

/**
 * 代理 filesystem.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.08.29
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
class Filesystem implements IFilesystem
{
    /**
     * 设置配置.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return \Leevel\Filesystem\IFilesystem
     */
    public static function setOption(string $name, $value): IBaseFilesystem
    {
        return self::proxy()->setOption($name, $value);
    }

    /**
     * 判断文件是否存在.
     *
     * @param string $path
     *
     * @return bool
     */
    public static function has(string $path): bool
    {
        return self::proxy()->has($path);
    }

    /**
     * 读取文件.
     *
     * @param string $path
     *
     * @throws \League\Flysystem\FileNotFoundException
     *
     * @return false|string
     */
    public static function read(string $path)
    {
        return self::proxy()->read($path);
    }

    /**
     * 从路径读取流数据.
     *
     * @param string $path
     *
     * @throws \League\Flysystem\FileNotFoundException
     *
     * @return false|resource
     */
    public static function readStream(string $path)
    {
        return self::proxy()->readStream($path);
    }

    /**
     * 读取文件目录.
     *
     * @param string $directory
     * @param bool   $recursive
     *
     * @return array
     */
    public static function listContents(string $directory = '', bool $recursive = false): array
    {
        return self::proxy()->listContents($directory, $recursive);
    }

    /**
     * 获取文件元数据.
     *
     * @param string $path
     *
     * @throws \League\Flysystem\FileNotFoundException
     *
     * @return array|false
     */
    public static function getMetadata(string $path)
    {
        return self::proxy()->getMetadata($path);
    }

    /**
     * 获取文件大小.
     *
     * @param string $path
     *
     * @throws \League\Flysystem\FileNotFoundException
     *
     * @return false|int
     */
    public static function getSize(string $path)
    {
        return self::proxy()->getSize($path);
    }

    /**
     * 获取文件的 mime 类型.
     *
     * @param string $path
     *
     * @throws \League\Flysystem\FileNotFoundException
     *
     * @return false|string
     */
    public static function getMimetype(string $path)
    {
        return self::proxy()->getMimetype($path);
    }

    /**
     * 获取文件的时间戳.
     *
     * @param string $path
     *
     * @throws \League\Flysystem\FileNotFoundException
     *
     * @return false|string
     */
    public static function getTimestamp(string $path)
    {
        return self::proxy()->getTimestamp($path);
    }

    /**
     * 获取文件的可见性.
     *
     * @param string $path
     *
     * @throws \League\Flysystem\FileNotFoundException
     *
     * @return false|string public|private|false
     */
    public static function getVisibility(string $path)
    {
        return self::proxy()->getVisibility($path);
    }

    /**
     * 写一个新文件.
     *
     * @param string $path
     * @param string $contents
     * @param array  $config
     *
     * @throws \League\Flysystem\FileNotFoundException
     *
     * @return bool
     */
    public static function write(string $path, string $contents, array $config = []): bool
    {
        return self::proxy()->write($path, $contents, $config);
    }

    /**
     * 使用流写入新文件.
     *
     * @param string   $path
     * @param resource $resource
     * @param array    $config
     *
     * @throws \InvalidArgumentException
     * @throws \League\Flysystem\FileNotFoundException
     *
     * @return bool
     */
    public static function writeStream(string $path, $resource, array $config = []): bool
    {
        return self::proxy()->writeStream($path, $resource, $config);
    }

    /**
     * 更新现有文件.
     *
     * @param string $path
     * @param string $contents
     * @param array  $config
     *
     * @throws \League\Flysystem\FileNotFoundException
     *
     * @return bool
     */
    public static function update(string $path, string $contents, array $config = []): bool
    {
        return self::proxy()->update($path, $contents, $config);
    }

    /**
     * 使用流更新现有文件.
     *
     * @param string   $path
     * @param resource $resource
     * @param array    $config
     *
     * @throws \InvalidArgumentException
     * @throws \League\Flysystem\FileNotFoundException
     *
     * @return bool
     */
    public static function updateStream(string $path, $resource, array $config = []): bool
    {
        return self::proxy()->updateStream($path, $resource, $config);
    }

    /**
     * 重命名文件.
     *
     * @param string $path
     * @param string $newpath
     *
     * @throws \League\Flysystem\FileExistsException
     * @throws \League\Flysystem\FileNotFoundException
     *
     * @return bool
     */
    public static function rename(string $path, string $newpath): bool
    {
        return self::proxy()->rename($path, $newpath);
    }

    /**
     * 复制文件.
     *
     * @param string $path
     * @param string $newpath
     *
     * @throws \League\Flysystem\FileExistsException
     * @throws \League\Flysystem\FileNotFoundException
     *
     * @return bool
     */
    public static function copy(string $path, string $newpath): bool
    {
        return self::proxy()->copy($path, $newpath);
    }

    /**
     * 删除文件.
     *
     * @param string $path
     *
     * @throws \League\Flysystem\FileNotFoundException
     *
     * @return bool
     */
    public static function delete(string $path): bool
    {
        return self::proxy()->delete($path);
    }

    /**
     * 删除文件夹.
     *
     * @param string $dirname
     *
     * @throws \League\Flysystem\RootViolationException
     *
     * @return bool
     */
    public static function deleteDir(string $dirname): bool
    {
        return self::proxy()->deleteDir($dirname);
    }

    /**
     * 创建一个文件夹.
     *
     * @param string $dirname
     * @param array  $config
     *
     * @return bool
     */
    public static function createDir(string $dirname, array $config = []): bool
    {
        return self::proxy()->createDir($dirname, $config);
    }

    /**
     * 设置文件的可见性.
     *
     * @param string $path
     * @param string $visibility public|private
     *
     * @throws \League\Flysystem\FileNotFoundException
     *
     * @return bool
     */
    public static function setVisibility(string $path, $visibility): bool
    {
        return self::proxy()->setVisibility($path, $visibility);
    }

    /**
     * 创建或者更新文件.
     *
     * @param string $path
     * @param string $contents
     * @param array  $config
     *
     * @return bool
     */
    public static function put(string $path, string $contents, array $config = []): bool
    {
        return self::proxy()->put($path, $contents, $config);
    }

    /**
     * 使用流创建或者更新文件.
     *
     * @param string   $path
     * @param resource $resource
     * @param array    $config
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    public static function putStream(string $path, $resource, array $config = []): bool
    {
        return self::proxy()->putStream($path, $resource, $config);
    }

    /**
     * 读取并删除一个文件.
     *
     * @param string $path
     *
     * @throws \League\Flysystem\FileNotFoundException
     *
     * @return false|string
     */
    public static function readAndDelete(string $path)
    {
        return self::proxy()->readAndDelete($path);
    }

    /**
     * 注册一个插件.
     *
     * @param \League\Flysystem\PluginInterface $plugin
     *
     * @return \League\Flysystem\FilesystemInterface
     */
    public static function addPlugin(PluginInterface $plugin): FilesystemInterface
    {
        return self::proxy()->addPlugin($plugin);
    }

    /**
     * 代理服务.
     *
     * @return \Leevel\Filesystem\Manager
     */
    public static function proxy(): Manager
    {
        return Container::singletons()->make('filesystems');
    }
}
