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
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.08.29
 *
 * @version 1.0
 */
class Manager extends Managers implements IFilesystem
{
    use Proxy;

    /**
     * 返回代理.
     *
     * @return \Leevel\Filesystem\IFilesystem
     */
    public function proxy(): IFilesystem
    {
        return $this->connect();
    }

    /**
     * 取得配置命名空间.
     *
     * @return string
     */
    protected function normalizeOptionNamespace(): string
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
    protected function createConnect(object $connect): object
    {
        return $connect;
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
