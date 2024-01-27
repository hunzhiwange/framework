<?php

declare(strict_types=1);

namespace Leevel\Filesystem;

use League\Flysystem\FilesystemAdapter;
use League\Flysystem\Local\LocalFilesystemAdapter;
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
    protected array $config = [
        'path' => '',
        'permissions' => [],
        'write_flags' => LOCK_EX,
        'link_handling' => LocalFilesystemAdapter::DISALLOW_LINKS,
    ];

    /**
     * {@inheritDoc}
     *
     * @throws \InvalidArgumentException
     */
    protected function makeFilesystemAdapter(): FilesystemAdapter
    {
        if (empty($this->config['path'])) {
            throw new \InvalidArgumentException('The local driver requires path config.');
        }

        return new LocalFilesystemAdapter(
            $this->config['path'],
            PortableVisibilityConverter::fromArray($this->config['permissions']),
            $this->config['write_flags'],
            $this->config['link_handling'],
        );
    }
}
