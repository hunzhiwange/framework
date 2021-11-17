<?php

declare(strict_types=1);

namespace Leevel\Filesystem;

use InvalidArgumentException;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\ZipArchive\FilesystemZipArchiveProvider;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;

/**
 * Filesystem zip.
 *
 * @see 旧版文档，新版本暂时没有 https://flysystem.thephpleague.com/adapter/zip-archive/
 */
class Zip extends Filesystem implements IFilesystem
{
    /**
     * 配置.
     */
    protected array $option = [
        'path' => '',
    ];

    /**
     * {@inheritDoc}
     *
     * - 请执行 `composer require league/flysystem-ziparchive`.
     *
     * @throws \InvalidArgumentException
     */
    protected function makeFilesystemAdapter(): FilesystemAdapter
    {
        if (empty($this->option['path'])) {
            throw new InvalidArgumentException('The zip driver requires path option.');
        }

        return new ZipArchiveAdapter(new FilesystemZipArchiveProvider($this->option['path']));
    }
}
