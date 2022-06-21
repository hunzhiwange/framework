<?php

declare(strict_types=1);

namespace Leevel\Filesystem\Helper;

use Symfony\Component\Filesystem\Filesystem;

/**
 * 创建软连接.
 */
function link(string $originDir, string $targetDir): void
{
    (new Filesystem())->symlink($originDir, $targetDir, true);
}

class link
{
}
