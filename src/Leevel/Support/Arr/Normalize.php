<?php

declare(strict_types=1);

namespace Leevel\Support\Arr;

class Normalize
{
    /**
     * 数组数据格式化.
     */
    public static function handle(mixed $inputs, string $delimiter = ',', array $allowedEmpty = [0, '0']): mixed
    {
        if (!\is_array($inputs) && !\is_string($inputs)) {
            return $inputs;
        }

        if (!\is_array($inputs)) {
            /** @phpstan-ignore-next-line */
            $inputs = explode($delimiter, $inputs);
        }

        $inputs = array_map(
            static fn (mixed $value) => \is_string($value) ? trim($value) : $value,
            $inputs,
        );

        if (!$allowedEmpty) {
            return array_filter($inputs);
        }

        return array_filter(
            $inputs,
            static fn (mixed $value) => $value || \in_array($value, $allowedEmpty, true),
        );
    }
}
