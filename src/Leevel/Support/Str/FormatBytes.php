<?php

declare(strict_types=1);

namespace Leevel\Support\Str;

class FormatBytes
{
    /**
     * 文件大小格式化.
     */
    public static function handle(int $fileSize, bool $withUnit = true): string
    {
        if ($fileSize >= 1073741824) {
            $fileSize = round($fileSize / 1073741824, 2).($withUnit ? 'G' : '');
        } elseif ($fileSize >= 1048576) {
            $fileSize = round($fileSize / 1048576, 2).($withUnit ? 'M' : '');
        } elseif ($fileSize >= 1024) {
            $fileSize = round($fileSize / 1024, 2).($withUnit ? 'K' : '');
        } else {
            $fileSize = $fileSize.($withUnit ? 'B' : '');
        }

        return $fileSize;
    }
}
