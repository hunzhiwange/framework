<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

use InvalidArgumentException;

/**
 * 处于 betweenEqual 范围，包含全等.
 */
class BetweenEqual
{
    /**
     * @throws \InvalidArgumentException
     */
    public static function handle(mixed $value, array $param): bool
    {
        if (
            !array_key_exists(0, $param) ||
            !array_key_exists(1, $param)
        ) {
            $e = 'Missing the first or second element of param.';

            throw new InvalidArgumentException($e);
        }

        return ($value > $param[0] || $value === $param[0]) &&
            ($value < $param[1] || $value === $param[1]);
    }
}
