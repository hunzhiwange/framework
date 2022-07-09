<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

class NotNull
{
    /**
     * 是否不为 NULL.
     */
    public static function handle(mixed $value): bool
    {
        return null !== $value;
    }
}
