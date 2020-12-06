<?php

declare(strict_types=1);

namespace Leevel\Filesystem;

use League\Flysystem\Adapter\Ftp as AdapterFtp;
use League\Flysystem\AdapterInterface;

/**
 * Filesystem ftp.
 *
 * @see https://flysystem.thephpleague.com/adapter/ftp/
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
    protected function makeAdapter(): AdapterInterface
    {
        return new AdapterFtp($this->option);
    }
}
