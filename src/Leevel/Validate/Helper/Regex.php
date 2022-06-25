<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

use InvalidArgumentException;

/**
 * 数据是否满足正则条件.
 */
class Regex
{
    /**
     * @throws \InvalidArgumentException
     */
    public static function handle(mixed $value, array $param): bool
    {
        if (!is_scalar($value)) {
            return false;
        }

        $value = (string) $value;

        if (!array_key_exists(0, $param)) {
            $e = 'Missing the first element of param.';

            throw new InvalidArgumentException($e);
        }

        return preg_match($param[0], $value) > 0;
    }
}
