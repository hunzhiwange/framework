<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

/**
 * 是否不为 NULL.
 */
class NotNull
{
    public static function handle(mixed $value): bool
    {
        return null !== $value;
    }
}
