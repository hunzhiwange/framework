<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

class AlphaNum
{
    public const string MESSAGE = '{field} 只能是字母和数字';

    public const string ANTISENSE_MESSAGE = '{field} 不能只是字母和数字';

    /**
     * 字符串是否为数字和字母.
     */
    public static function handle(mixed $value): bool
    {
        if (\is_int($value)) {
            return true;
        }

        if (!\is_string($value)) {
            return false;
        }

        return preg_match('/^[A-Za-z0-9]+$/', (string) $value) > 0;
    }
}
