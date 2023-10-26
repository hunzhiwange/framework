<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

class NotIn
{
    /**
     * 是否不处于某个范围.
     *
     * @throws \InvalidArgumentException
     */
    public static function handle(mixed $value, array $param): bool
    {
        if (!\array_key_exists(0, $param)) {
            throw new \InvalidArgumentException('Missing the first element of param.');
        }

        return !\in_array($value, $param);
    }
}
