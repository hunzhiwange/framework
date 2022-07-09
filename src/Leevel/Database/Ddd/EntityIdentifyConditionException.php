<?php

declare(strict_types=1);

namespace Leevel\Database\Ddd;

use RuntimeException;

/**
 * 实体查询条件条件异常.
 */
class EntityIdentifyConditionException extends RuntimeException
{
    /**
     * 异常是否需要上报.
     */
    public function reportable(): bool
    {
        return false;
    }
}
