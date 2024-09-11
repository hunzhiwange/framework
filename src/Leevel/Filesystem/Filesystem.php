<?php

declare(strict_types=1);

namespace Leevel\Filesystem;

use League\Flysystem\Filesystem as LeagueFilesystem;
use League\Flysystem\FilesystemAdapter;

/**
 * 文件系统抽象类.
 *
 * @see https://flysystem.thephpleague.com/api/
 */
abstract class Filesystem implements IFilesystem
{
    /**
     * Filesystem.
     */
    protected LeagueFilesystem $filesystem;

    /**
     * 配置.
     */
    protected array $config = [];

    /**
     * 构造函数.
     */
    public function __construct(array $config = [])
    {
        $this->config = array_merge($this->config, $config);
        $this->filesystem();
    }

    public function getFilesystem(): LeagueFilesystem
    {
        return $this->filesystem;
    }

    /**
     * 创建连接.
     */
    abstract protected function makeFilesystemAdapter(): FilesystemAdapter;

    /**
     * 生成 Filesystem.
     */
    protected function filesystem(): LeagueFilesystem
    {
        return $this->filesystem = new LeagueFilesystem(
            $this->makeFilesystemAdapter(),
            $this->normalizeConfigs()
        );
    }

    /**
     * 整理配置.
     */
    protected function normalizeConfigs(array $config = []): array
    {
        return $config ? array_merge($this->config, $config) : $this->config;
    }
}
