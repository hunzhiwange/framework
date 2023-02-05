<?php

declare(strict_types=1);

namespace Leevel\Validate;

/**
 * 断言异常.
 */
class AssertException extends \RuntimeException
{
    /**
     * 异常是否需要上报.
     */
    public function reportable(): bool
    {
        return false;
    }
}
