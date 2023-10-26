<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

use function strlen as base_strlen;

class Strlen
{
    /**
     * 长度验证.
     *
     * @throws \InvalidArgumentException
     */
    public static function handle(mixed $value, array $param): bool
    {
        if (!\is_scalar($value)) {
            return false;
        }

        $value = (string) $value;

        if (!\array_key_exists(0, $param)) {
            throw new \InvalidArgumentException('Missing the first element of param.');
        }

        return base_strlen($value) === (int) $param[0];
    }
}
