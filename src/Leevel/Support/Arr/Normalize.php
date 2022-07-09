<?php

declare(strict_types=1);

namespace Leevel\Support\Arr;

class Normalize
{
    /**
     * 数组数据格式化.
     */
    public static function handle(mixed $inputs, string $delimiter = ',', bool $allowedEmpty = false): mixed
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

        $inputs = array_map(
            fn (mixed $value) => is_string($value) ? trim($value) : $value,
            $inputs,
        );

        return array_filter(
            $inputs,
            fn (mixed $value) => is_string($value) ? strlen($value) > 0 : true,
        );
    }
}
