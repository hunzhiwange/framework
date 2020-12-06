<?php

declare(strict_types=1);

namespace Leevel\Filesystem;

use InvalidArgumentException;
use League\Flysystem\AdapterInterface;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;

/**
 * Filesystem zip.
 *
 * @see https://flysystem.thephpleague.com/adapter/zip-archive/
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
    protected function makeAdapter(): AdapterInterface
    {
        if (empty($this->option['path'])) {
            throw new InvalidArgumentException('The zip driver requires path option.');
        }

        return new ZipArchiveAdapter($this->option['path']);
    }
}
