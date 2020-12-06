<?php

declare(strict_types=1);

namespace Leevel\Support\Arr;

/**
 * 数组数据格式化.
 */
function normalize(mixed $inputs, string $delimiter = ',', bool $allowedEmpty = false): mixed
{
    if (!is_array($inputs) && !is_string($inputs)) {
        return $inputs;
    }

    if (!is_array($inputs)) {
        $inputs = (array) explode($delimiter, $inputs);
    }
    $inputs = array_filter($inputs);

    if (true === $allowedEmpty) {
        return $inputs;
    }

    $inputs = array_map('trim', $inputs);

    return array_filter($inputs, 'strlen');
}

class normalize
{
}
