<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

class Alpha
{
    public const string MESSAGE = '{field} 只能是字母';

    public const string ANTISENSE_MESSAGE = '{field} 不能只是字母';

    /**
     * 是否为英文字母.
     */
    public static function handle(mixed $value): bool
    {
        if (!\is_string($value)) {
            return false;
        }

        return preg_match('/^[A-Za-z]+$/', $value) > 0;
    }
}
