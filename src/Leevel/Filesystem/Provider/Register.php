<?php

declare(strict_types=1);

namespace Leevel\Filesystem\Provider;

use Leevel\Di\IContainer;
use Leevel\Di\Provider;
use Leevel\Filesystem\Filesystem;
use Leevel\Filesystem\IFilesystem;
use Leevel\Filesystem\Manager;

/**
 * 文件系统服务提供者.
 */
class Register extends Provider
{
    /**
     * {@inheritDoc}
     */
    public function register(): void
    {
        $this->filesystems();
        $this->filesystem();
    }

    /**
     * {@inheritDoc}
     */
    public static function providers(): array
    {
        return [
            'filesystems' => Manager::class,
            'filesystem' => [IFilesystem::class, Filesystem::class],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public static function isDeferred(): bool
    {
        return true;
    }

    /**
     * 注册 filesystems 服务.
     */
    protected function filesystems(): void
    {
        $this->container
            ->singleton(
                'filesystems',
                fn (IContainer $container): Manager => new Manager($container),
            )
        ;
    }

    /**
     * 注册 filesystem 服务.
     */
    protected function filesystem(): void
    {
        $this->container
            ->singleton(
                'filesystem',
                fn (IContainer $container): Filesystem => $container['filesystems']->connect(),
            )
        ;
    }
}
