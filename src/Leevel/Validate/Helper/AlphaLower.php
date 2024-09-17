<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

class AlphaLower
{
    public const string MESSAGE = '{field} 只能是小写字母';

    public const string ANTISENSE_MESSAGE = '{field} 不能只是小写字母';

    /**
     * 是否为小写英文字母.
     */
    public static function handle(mixed $value): bool
    {
        if (!\is_string($value)) {
            return false;
        }

        return preg_match('/^[a-z]+$/', $value) > 0;
    }
}
