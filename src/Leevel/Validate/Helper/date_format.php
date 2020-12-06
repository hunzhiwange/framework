<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

use InvalidArgumentException;

/**
 * 是否为时间.
 * @throws \InvalidArgumentException
 */
function date_format(mixed $value, array $param): bool
{
    if (!array_key_exists(0, $param)) {
        $e = 'Missing the first element of param.';

        throw new InvalidArgumentException($e);
    }

    $parse = date_parse_from_format($param[0], $value);

    return 0 === $parse['error_count'] && 0 === $parse['warning_count'];
}

class date_format
{
}
