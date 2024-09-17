<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

class Chinese
{
    public const string MESSAGE = '{field} 只能是汉字';

    public const string ANTISENSE_MESSAGE = '{field} 不能只是汉字';

    /**
     * 是否为中文.
     */
    public static function handle(mixed $value): bool
    {
        if (!\is_string($value)) {
            return false;
        }

        return preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $value) > 0;
    }
}
