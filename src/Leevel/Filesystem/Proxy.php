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

namespace Leevel\Filesystem;

use League\Flysystem\AdapterInterface;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\PluginInterface;

/**
 * 代理.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.05.09
 *
 * @version 1.0
 */
trait Proxy
{
    /**
     * 设置配置.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     */
    public function setOption(string $name, $value): IFilesystem
    {
        return $this->proxy()->setOption($name, $value);
    }

    /**
     * 创建连接.
     *
     * @return \League\Flysystem\AdapterInterface
     */
    public function makeAdapter(): AdapterInterface
    {
        return $this->proxy()->makeAdapter();
    }

    /**
     * 判断文件是否存在.
     *
     * @param string $path
     *
     * @return bool
     */
    public function has(string $path): bool
    {
        return $this->proxy()->has($path);
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
    public function read(string $path)
    {
        return $this->proxy()->read($path);
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
    public function readStream(string $path)
    {
        return $this->proxy()->readStream($path);
    }

    /**
     * 读取文件目录.
     *
     * @param string $directory
     * @param bool   $recursive
     *
     * @return array
     */
    public function listContents(string $directory = '', bool $recursive = false): array
    {
        return $this->proxy()->listContents($directory, $recursive);
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
    public function getMetadata(string $path)
    {
        return $this->proxy()->getMetadata($path);
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
    public function getSize(string $path)
    {
        return $this->proxy()->getSize($path);
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
    public function getMimetype(string $path)
    {
        return $this->proxy()->getMimetype($path);
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
    public function getTimestamp(string $path)
    {
        return $this->proxy()->getTimestamp($path);
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
    public function getVisibility(string $path)
    {
        return $this->proxy()->getVisibility($path);
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
    public function write(string $path, string $contents, array $config = []): bool
    {
        return $this->proxy()->write($path, $contents, $config);
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
    public function writeStream(string $path, $resource, array $config = []): bool
    {
        return $this->proxy()->writeStream($path, $resource, $config);
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
    public function update(string $path, string $contents, array $config = []): bool
    {
        return $this->proxy()->update($path, $contents, $config);
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
    public function updateStream(string $path, $resource, array $config = []): bool
    {
        return $this->proxy()->updateStream($path, $resource, $config);
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
    public function rename(string $path, string $newpath): bool
    {
        return $this->proxy()->rename($path, $newpath);
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
    public function copy(string $path, string $newpath): bool
    {
        return $this->proxy()->copy($path, $newpath);
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
    public function delete(string $path): bool
    {
        return $this->proxy()->delete($path);
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
    public function deleteDir(string $dirname): bool
    {
        return $this->proxy()->deleteDir($dirname);
    }

    /**
     * 创建一个文件夹.
     *
     * @param string $dirname
     * @param array  $config
     *
     * @return bool
     */
    public function createDir(string $dirname, array $config = []): bool
    {
        return $this->proxy()->createDir($dirname, $config);
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
    public function setVisibility(string $path, $visibility): bool
    {
        return $this->proxy()->setVisibility($path, $visibility);
    }

    /**
     * 创建获取更新文件.
     *
     * @param string $path
     * @param string $contents
     * @param array  $config
     *
     * @return bool
     */
    public function put(string $path, string $contents, array $config = []): bool
    {
        return $this->proxy()->put($path, $contents, $config);
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
    public function putStream(string $path, $resource, array $config = []): bool
    {
        return $this->proxy()->putStream($path, $resource, $config);
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
    public function readAndDelete(string $path)
    {
        return $this->proxy()->readAndDelete($path);
    }

    /**
     * 注册一个插件.
     *
     * @param \League\Flysystem\PluginInterface $plugin
     *
     * @return \League\Flysystem\FilesystemInterface
     */
    public function addPlugin(PluginInterface $plugin): FilesystemInterface
    {
        return $this->proxy()->addPlugin($plugin);
    }
}
