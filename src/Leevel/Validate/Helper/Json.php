<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

use Throwable;

class Json
{
    /**
     * 验证是否为正常的 JSON 数据.
     */
    public static function handle(mixed $value): bool
    {
        if (\is_object($value) && !method_exists($value, '__toString')) {
            return false;
        }

        if (
            \is_string($value) && class_exists($value)
            && !method_exists($value, '__toString')
        ) {
            return false;
        }

        try {
            json_decode((string) $value, true, 512, JSON_THROW_ON_ERROR);

            return true;
        } catch (Throwable) {
            return false;
        }
    }
}
