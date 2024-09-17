<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

class AlphaDash
{
    public const string MESSAGE = '{field} 只能是字母、数字、短横线和下划线';

    public const string ANTISENSE_MESSAGE = '{field} 不能只是字母、数字、短横线和下划线';

    /**
     * 字符串是否为数字、下划线、短横线和字母.
     */
    public static function handle(mixed $value): bool
    {
        if (\is_int($value)) {
            return true;
        }

        if (!\is_string($value)) {
            return false;
        }

        return preg_match('/^[A-Za-z0-9\-\_]+$/', $value) > 0;
    }
}
