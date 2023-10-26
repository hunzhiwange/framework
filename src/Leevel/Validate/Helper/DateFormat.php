<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

class DateFormat
{
    /**
     * 是否为时间.
     *
     * @throws \InvalidArgumentException
     */
    public static function handle(mixed $value, array $param): bool
    {
        if (!\array_key_exists(0, $param)) {
            throw new \InvalidArgumentException('Missing the first element of param.');
        }

        if (!\is_string($value)) {
            return false;
        }

        $parse = date_parse_from_format($param[0], $value);

        return 0 === $parse['error_count'] && 0 === $parse['warning_count'];
    }
}
