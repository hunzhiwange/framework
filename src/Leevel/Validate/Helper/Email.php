<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

class Email
{
    /**
     * 是否为电子邮件.
     */
    public static function handle(mixed $value): bool
    {
        return false !== filter_var($value, FILTER_VALIDATE_EMAIL);
    }
}
