<?php

declare(strict_types=1);

namespace Leevel\Filesystem\Helper;

/**
 * 获取上传文件扩展名.
 */
function get_extension(string $fileName, int $case = 0): string
{
    $fileName = pathinfo($fileName, PATHINFO_EXTENSION);
    if (1 === $case) {
        return strtoupper($fileName);
    }
    if (2 === $case) {
        return strtolower($fileName);
    }

    return $fileName;
}

class get_extension
{
}
