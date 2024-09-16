<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

class ChineseAlphaDash
{
    public const string MESSAGE = '{field} 只能是汉字、字母、数字、短横线和下划线';

    /**
     * 是否为中文、数字、下划线、短横线和字母.
     */
    public static function handle(mixed $value): bool
    {
        if (\is_int($value)) {
            return true;
        }

        if (!\is_string($value)) {
            return false;
        }

        return preg_match('/^[\x{4e00}-\x{9fa5}A-Za-z0-9\-\_]+$/u', $value) > 0;
    }
}
