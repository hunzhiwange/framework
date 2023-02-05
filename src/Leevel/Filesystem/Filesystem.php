<?php

declare(strict_types=1);

namespace Leevel\Filesystem;

use League\Flysystem\Filesystem as LeagueFilesystem;
use League\Flysystem\FilesystemAdapter;

/**
 * 文件系统抽象类.
 *
 * @see https://flysystem.thephpleague.com/api/
 *
 * @method static bool                               fileExists(string $location)
 * @method static void                               write(string $location, string $contents, array $config = [])
 * @method static void                               writeStream(string $location, $contents, array $config = [])
 * @method static string                             read(string $location)
 * @method static mixed                              readStream(string $location)
 * @method static void                               delete(string $location)
 * @method static void                               deleteDirectory(string $location)
 * @method static void                               createDirectory(string $location, array $config = [])
 * @method static \League\Flysystem\DirectoryListing listContents(string $location, bool $deep = false)
 * @method static void                               move(string $source, string $destination, array $config = [])
 * @method static void                               copy(string $source, string $destination, array $config = [])
 * @method static int                                lastModified(string $path)
 * @method static int                                fileSize(string $path)
 * @method static string                             mimeType(string $path)
 * @method static void                               setVisibility(string $path, string $visibility)
 * @method static string                             visibility(string $path)
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
    protected array $option = [];

    /**
     * 构造函数.
     */
    public function __construct(array $option = [])
    {
        $this->option = array_merge($this->option, $option);
        $this->filesystem();
    }

    /**
     * 实现魔术方法 __call.
     */
    public function __call(string $method, array $args): mixed
    {
        return $this->filesystem->{$method}(...$args);
    }

    /**
     * {@inheritDoc}
     */
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
            $this->normalizeOptions()
        );
    }

    /**
     * 整理配置.
     */
    protected function normalizeOptions(array $option = []): array
    {
        return $option ? array_merge($this->option, $option) : $this->option;
    }
}
