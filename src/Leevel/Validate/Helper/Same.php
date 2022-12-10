<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

use InvalidArgumentException;

class Same
{
    /**
     * 两个值是否完全相同.
     * @throws \InvalidArgumentException
     */
    public static function handle(mixed $value, array $param): bool
    {
        if (!array_key_exists(0, $param)) {
            $e = 'Missing the first element of param.';

            throw new InvalidArgumentException($e);
        }

        return $value === $param[0];
    }
}