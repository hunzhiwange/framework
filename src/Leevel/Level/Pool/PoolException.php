<?php

declare(strict_types=1);

namespace Leevel\Level\Pool;

use RuntimeException;

/**
 * 连接池异常.
 */
class PoolException extends RuntimeException
{
    /**
     * 异常是否需要上报.
     */
    public function reportable(): bool
    {
        return false;
    }
}
