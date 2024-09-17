<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

class Upper
{
    public const string MESSAGE = '{field} 必须全部是大写';

    public const string ANTISENSE_MESSAGE = '{field} 不能全部是大写';

    /**
     * 验证是否都是大写.
     */
    public static function handle(mixed $value): bool
    {
        if (!\is_string($value)) {
            return false;
        }

        return ctype_upper($value);
    }
}
