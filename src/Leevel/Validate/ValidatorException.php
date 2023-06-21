<?php

declare(strict_types=1);

namespace Leevel\Validate;

/**
 * 验证异常.
 */
class ValidatorException extends \RuntimeException
{
    /**
     * 异常是否需要上报.
     */
    public function reportable(): bool
    {
        return false;
    }
}
