<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

use Throwable;

class Timezone
{
    /**
     * 是否为正确的时区.
     */
    public static function handle(mixed $value): bool
    {
        try {
            if (!\is_string($value)) {
                return false;
            }

            new \DateTimeZone($value);
        } catch (Throwable) {
            return false;
        }

        return true;
    }
}
