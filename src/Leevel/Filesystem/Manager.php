<?php

declare(strict_types=1);

namespace Leevel\Filesystem;

use League\Flysystem\Filesystem as LeagueFilesystem;
use Leevel\Support\Manager as Managers;

/**
 * 文件系统管理器.
 */
class Manager extends Managers implements IFilesystem
{
    public function getFilesystem(): LeagueFilesystem
    {
        // @phpstan-ignore-next-line
        return $this->__call(__FUNCTION__, []);
    }

    public function connect(?string $connect = null, bool $newConnect = false, ...$arguments): IFilesystem
    {
        return parent::connect($connect, $newConnect, ...$arguments);
    }

    public function reconnect(?string $connect = null, ...$arguments): IFilesystem
    {
        return parent::reconnect($connect, ...$arguments);
    }

    /**
     * 取得配置命名空间.
     */
    protected function getConfigNamespace(): string
    {
        return 'filesystem';
    }

    /**
     * 创建 local 连接.
     */
    protected function makeConnectLocal(string $connect, ?string $driverClass = null): Local
    {
        $driverClass = $this->getDriverClass(Local::class, $driverClass);

        return new $driverClass(
            $this->normalizeConnectConfig($connect)
        );
    }

    /**
     * 创建 sftp 连接.
     */
    protected function makeConnectSftp(string $connect, ?string $driverClass = null): Sftp
    {
        $driverClass = $this->getDriverClass(Sftp::class, $driverClass);

        return new $driverClass(
            $this->normalizeConnectConfig($connect)
        );
    }

    /**
     * 创建 zip 连接.
     */
    protected function makeConnectZip(string $connect, ?string $driverClass = null): Zip
    {
        $driverClass = $this->getDriverClass(Zip::class, $driverClass);

        return new $driverClass(
            $this->normalizeConnectConfig($connect)
        );
    }
}
