<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

class ZipCode
{
    public const string MESSAGE = '{field} 必须是有效的中国邮政编码';

    public const string ANTISENSE_MESSAGE = '{field} 不能是有效的中国邮政编码';

    /**
     * 是否为中国邮政编码.
     */
    public static function handle(mixed $value): bool
    {
        if (!\is_scalar($value)) {
            return false;
        }

        return preg_match('/^[1-9]\d{5}$/', (string) $value) > 0;
    }
}
