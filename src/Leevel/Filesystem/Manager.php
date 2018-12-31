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

use Leevel\Manager\Manager as Managers;

/**
 * filesystem 入口.
 *
 * @method bool                                  has(string $path)
 * @method false|string                          read(string $path)
 * @method false|resource                        readStream(string $path)
 * @method array                                 listContents(string $directory = '', bool $recursive = false)
 * @method array|false                           getMetadata(string $path)
 * @method false|int                             getSize(string $path)
 * @method false|string                          getMimetype(string $path)
 * @method false|int                             getTimestamp(string $path)
 * @method false|string                          getVisibility(string $path)
 * @method bool                                  write(string $path, string $contents, array $config = [])
 * @method bool                                  writeStream(string $path, resource $resource, array $config = [])
 * @method bool                                  update(string $path, string $contents, array $config = [])
 * @method bool                                  updateStream(string $path, resource $resource, array $config = [])
 * @method bool                                  rename(string $path, string $newpath)
 * @method bool                                  copy(string $path, string $newpath)
 * @method bool                                  delete(string $path)
 * @method bool                                  deleteDir(string $dirname)
 * @method bool                                  createDir(string $dirname, array $config = [])
 * @method bool                                  setVisibility(sring $path, string $visibility)
 * @method bool                                  put(string $path, string $contents, array $config = [])
 * @method bool                                  putStream(string $path, resource $resource, array $config = [])
 * @method string                                readAndDelete(string $path)
 * @method \League\Flysystem\Handler             get(string $path, \League\Flysystem\Handler $handler = null)
 * @method \League\Flysystem\FilesystemInterface addPlugin(\League\Flysystem\PluginInterface $plugin)
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.08.29
 *
 * @version 1.0
 */
class Manager extends Managers
{
    /**
     * 取得配置命名空间.
     *
     * @return string
     */
    protected function normalizeOptionNamespace()
    {
        return 'filesystem';
    }

    /**
     * 创建连接对象
     *
     * @param object $connect
     *
     * @return object
     */
    protected function createConnect($connect)
    {
        return new Filesystem($connect);
    }

    /**
     * 创建 local 连接.
     *
     * @param array $options
     *
     * @return \Leevel\Filesystem\Local
     */
    protected function makeConnectLocal(array $options = []): Local
    {
        return new Local(
            $this->normalizeConnectOption('local', $options)
        );
    }

    /**
     * 创建 ftp 连接.
     *
     * @param array $options
     *
     * @return \Leevel\Filesystem\Ftp
     */
    protected function makeConnectFtp(array $options = []): Ftp
    {
        return new Ftp(
            $this->normalizeConnectOption('ftp', $options)
        );
    }

    /**
     * 创建 sftp 连接.
     *
     * @param array $options
     *
     * @return \Leevel\Filesystem\Sftp
     */
    protected function makeConnectSftp(array $options = []): Sftp
    {
        return new Sftp(
            $this->normalizeConnectOption('sftp', $options)
        );
    }

    /**
     * 创建 zip 连接.
     *
     * @param array $options
     *
     * @return \Leevel\Filesystem\Zip
     */
    protected function makeConnectZip(array $options = []): Zip
    {
        return new Zip(
            $this->normalizeConnectOption('zip', $options)
        );
    }
}
