<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

class Regex
{
    /**
     * 数据是否满足正则条件.
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

        return preg_match($param[0], $value) > 0;
    }
}
