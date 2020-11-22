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

use League\Flysystem\AdapterInterface;
use League\Flysystem\Sftp\SftpAdapter;

/**
 * Filesystem sftp.
 *
 * @see https://flysystem.thephpleague.com/adapter/sftp/
 */
class Sftp extends Filesystem implements IFilesystem
{
    /**
     * 配置.
     */
    protected array $option = [
        // 主机
        'host' => 'sftp.example.com',

        // 端口
        'port' => 22,

        // 用户名
        'username' => 'your-username',

        // 密码
        'password' => 'your-password',

        // 根目录
        'root' => '',

        // 私钥路径
        'privateKey' => '',

        // 超时设置
        'timeout' => 20,
    ];

    /**
     * 创建连接.
     *
     * - 请执行 `composer require league/flysystem-sftp`.
     *
     * @throws \InvalidArgumentException
     */
    protected function makeAdapter(): AdapterInterface
    {
        return new SftpAdapter($this->option);
    }
}
