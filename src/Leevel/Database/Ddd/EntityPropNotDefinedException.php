<?php

declare(strict_types=1);

namespace Leevel\Database\Ddd;

/**
 * 实体属性未定义异常.
 */
class EntityPropNotDefinedException extends \RuntimeException
{
    /**
     * 异常是否需要上报.
     */
    public function reportable(): bool
    {
        return false;
    }
}
