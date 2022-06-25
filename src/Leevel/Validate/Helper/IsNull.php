<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

/**
 * 是否为 NULL.
 */
class IsNull
{
    public static function handle(mixed $value): bool
    {
        return null === $value;
    }
}
