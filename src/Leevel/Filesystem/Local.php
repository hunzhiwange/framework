<?php

declare(strict_types=1);

namespace Leevel\Filesystem;

use InvalidArgumentException;
use League\Flysystem\Adapter\Local as AdapterLocal;
use League\Flysystem\AdapterInterface;

/**
 * Filesystem local.
 *
 * @see https://flysystem.thephpleague.com/adapter/local/
 */
class Local extends Filesystem implements IFilesystem
{
    /**
     * 配置.
     */
    protected array $option = [
        'path'          => '',
        'write_flags'   => LOCK_EX,
        'link_handling' => AdapterLocal::DISALLOW_LINKS,
        'permissions'   => [],
    ];

    /**
     * {@inheritDoc}
     *
     * @throws \InvalidArgumentException
     */
    protected function makeAdapter(): AdapterInterface
    {
        if (empty($this->option['path'])) {
            throw new InvalidArgumentException('The local driver requires path option.');
        }

        return new AdapterLocal(
            $this->option['path'],
            $this->option['write_flags'],
            $this->option['link_handling'],
            $this->option['permissions']
        );
    }
}
