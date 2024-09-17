<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

class Email
{
    public const string MESSAGE = '{field} 必须为正确的电子邮件格式';

    public const string ANTISENSE_MESSAGE = '{field} 不能为正确的电子邮件格式';

    /**
     * 是否为电子邮件.
     */
    public static function handle(mixed $value): bool
    {
        return false !== filter_var($value, FILTER_VALIDATE_EMAIL);
    }
}
