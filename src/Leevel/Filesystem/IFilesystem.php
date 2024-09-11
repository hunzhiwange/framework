<?php

declare(strict_types=1);

namespace Leevel\Filesystem;

use League\Flysystem\Filesystem as LeagueFilesystem;

/**
 * 文件系统接口.
 */
interface IFilesystem
{
    /**
     * 返回 Filesystem.
     */
    public function getFilesystem(): LeagueFilesystem;
}
