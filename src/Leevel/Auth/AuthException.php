<?php

declare(strict_types=1);

namespace Leevel\Auth;

use RuntimeException;

/**
 * 验证异常.
 */
class AuthException extends RuntimeException
{
    /**
     * 异常是否需要上报.
     */
    public function reportable(): bool
    {
        return false;
    }
}
