<?php

declare(strict_types=1);

namespace Leevel\Filesystem\Helper;

/**
 * 根据 ID 获取打散目录.
 */
function distributed(int $dataId): array
{
    $dataId = abs((int) $dataId);
    $dataId = sprintf('%09d', $dataId); // 格式化为 9 位数，前面不够填充 0

    return [
        substr($dataId, 0, 3).'/'.
            substr($dataId, 3, 2).'/'.
            substr($dataId, 5, 2).'/',
        substr($dataId, -2),
    ];
}

class distributed
{
}
