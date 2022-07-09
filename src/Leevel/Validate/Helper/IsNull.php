<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

class IsNull
{
    /**
     * 是否为 NULL.
     */
    public static function handle(mixed $value): bool
    {
        return null === $value;
    }
}
