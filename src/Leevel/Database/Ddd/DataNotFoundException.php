<?php

declare(strict_types=1);

namespace Leevel\Database\Ddd;

/**
 * 数据未找到异常.
 */
class DataNotFoundException extends \RuntimeException
{
    /**
     * 异常是否需要上报.
     */
    public function reportable(): bool
    {
        return false;
    }
}
