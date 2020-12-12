<?php

declare(strict_types=1);

namespace Leevel\Filesystem;

use InvalidArgumentException;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;

/**
 * Filesystem local.
 *
 * @see https://flysystem.thephpleague.com/v2/docs/adapter/local/
 */
class Local extends Filesystem implements IFilesystem
{
    /**
     * 配置.
     */
    protected array $option = [
        'path'          => '',
        'permissions'   => [],
        'write_flags'   => LOCK_EX,
        'link_handling' => LocalFilesystemAdapter::DISALLOW_LINKS,
    ];

    /**
     * {@inheritDoc}
     *
     * @throws \InvalidArgumentException
     */
    protected function makeFilesystemAdapter(): FilesystemAdapter
    {
        if (empty($this->option['path'])) {
            throw new InvalidArgumentException('The local driver requires path option.');
        }

        return new LocalFilesystemAdapter(
            $this->option['path'],
            PortableVisibilityConverter::fromArray($this->option['permissions']),
            $this->option['write_flags'],
            $this->option['link_handling'],
        );
    }
}
