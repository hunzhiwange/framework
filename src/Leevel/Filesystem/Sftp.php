<?php

declare(strict_types=1);

namespace Leevel\Filesystem;

use League\Flysystem\FilesystemAdapter;
use League\Flysystem\PhpseclibV2\SftpAdapter;
use League\Flysystem\PhpseclibV2\SftpConnectionProvider;

/**
 * Filesystem sftp.
 *
 * @see https://flysystem.thephpleague.com/v2/docs/adapter/sftp/
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
     * {@inheritDoc}
     *
     * - 请执行 `composer require league/flysystem-sftp`.
     */
    protected function makeFilesystemAdapter(): FilesystemAdapter
    {
        return new SftpAdapter(
            SftpConnectionProvider::fromArray($this->option),
            $this->option['root'],
        );
    }
}
