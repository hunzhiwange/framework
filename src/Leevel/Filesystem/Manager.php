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
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Filesystem;

use Leevel\Manager\Manager as Managers;

/**
 * Filesystem 入口.
 *
 * @method static \Leevel\Filesystem\IFilesystem setOption(string $name, $value)                             设置配置.
 * @method static \League\Flysystem\Filesystem getFilesystem()                                               返回 Filesystem.
 * @method static bool has(string $path)                                                                     判断文件是否存在.
 * @method static read(string $path)                                                                         读取文件.
 * @method static readStream(string $path)                                                                   从路径读取流数据.
 * @method static array listContents(string $directory = '', bool $recursive = false)                        读取文件目录.
 * @method static getMetadata(string $path)                                                                  获取文件元数据.
 * @method static getSize(string $path)                                                                      获取文件大小.
 * @method static getMimetype(string $path)                                                                  获取文件的 mime 类型.
 * @method static getTimestamp(string $path)                                                                 获取文件的时间戳.
 * @method static getVisibility(string $path)                                                                获取文件的可见性.
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
 * @method static readAndDelete(string $path)                                                                读取并删除一个文件.
 * @method static \League\Flysystem\FilesystemInterface addPlugin(\League\Flysystem\PluginInterface $plugin) 注册一个插件.
 */
class Manager extends Managers
{
    /**
     * 取得配置命名空间.
     */
    protected function normalizeOptionNamespace(): string
    {
        return 'filesystem';
    }

    /**
     * 创建 local 连接.
     *
     * @return \Leevel\Filesystem\Local
     */
    protected function makeConnectLocal(): Local
    {
        return new Local(
            $this->normalizeConnectOption('local')
        );
    }

    /**
     * 创建 ftp 连接.
     *
     * @return \Leevel\Filesystem\Ftp
     * @codeCoverageIgnore
     */
    protected function makeConnectFtp(): Ftp
    {
        return new Ftp(
            $this->normalizeConnectOption('ftp')
        );
    }

    /**
     * 创建 sftp 连接.
     *
     * @return \Leevel\Filesystem\Sftp
     * @codeCoverageIgnore
     */
    protected function makeConnectSftp(): Sftp
    {
        return new Sftp(
            $this->normalizeConnectOption('sftp')
        );
    }

    /**
     * 创建 zip 连接.
     *
     * @return \Leevel\Filesystem\Zip
     * @codeCoverageIgnore
     */
    protected function makeConnectZip(): Zip
    {
        return new Zip(
            $this->normalizeConnectOption('zip')
        );
    }
}
