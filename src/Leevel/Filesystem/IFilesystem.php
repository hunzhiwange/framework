<?php

declare(strict_types=1);

namespace Leevel\Filesystem;

use League\Flysystem\Filesystem as LeagueFilesystem;

/**
 * 文件系统接口.
 *
 * @method static bool fileExists(string $location) 
 * @method static void write(string $location, string $contents, array $config = []) 
 * @method static void writeStream(string $location, $contents, array $config = []) 
 * @method static string read(string $location) 
 * @method static mixed readStream(string $location) 
 * @method static void delete(string $location) 
 * @method static void deleteDirectory(string $location) 
 * @method static void createDirectory(string $location, array $config = []) 
 * @method static \League\Flysystem\DirectoryListing listContents(string $location, bool $deep = false) 
 * @method static void move(string $source, string $destination, array $config = []) 
 * @method static void copy(string $source, string $destination, array $config = []) 
 * @method static int lastModified(string $path) 
 * @method static int fileSize(string $path) 
 * @method static string mimeType(string $path) 
 * @method static void setVisibility(string $path, string $visibility) 
 * @method static string visibility(string $path)
 */
interface IFilesystem
{
    /**
     * 返回 Filesystem.
     */
    public function getFilesystem(): LeagueFilesystem;
}
