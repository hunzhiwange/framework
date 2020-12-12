<?php

declare(strict_types=1);

namespace Leevel\Filesystem;

use League\Flysystem\Ftp\FtpAdapter;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\Ftp\FtpConnectionOptions;

/**
 * Filesystem ftp.
 *
 * @see https://flysystem.thephpleague.com/v2/docs/adapter/ftp/
 */
class Ftp extends Filesystem implements IFilesystem
{
    /**
     * 配置.
     */
    protected array $option = [
        // 主机
        'host' => 'ftp.example.com',

        // 端口
        'port' => 21,

        // 用户名
        'username' => 'your-username',

        // 密码
        'password' => 'your-password',

        // 根目录
        'root' => '',

        // 被动、主动
        'passive' => true,

        // 加密传输
        'ssl' => false,

        // 超时设置
        'timeout' => 20,
    ];

    /**
     * {@inheritDoc}
     */
    protected function makeFilesystemAdapter(): FilesystemAdapter
    {
        return new FtpAdapter(FtpConnectionOptions::fromArray($this->option));
    }
}
