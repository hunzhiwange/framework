<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

use InvalidArgumentException;

class AllowedIp
{
    /**
     * 验证 IP 许可.
     *  @throws \InvalidArgumentException
     */
    public static function handle(mixed $value, array $param): bool
    {
        if (!is_string($value)) {
            return false;
        }

        if (!array_key_exists(0, $param)) {
            $e = 'Missing the first element of param.';

            throw new InvalidArgumentException($e);
        }

        return in_array($value, $param, true);
    }
}
