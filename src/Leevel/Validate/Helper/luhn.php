<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

use function strlen;

/**
 * 值是否为银行卡等符合 luhn 算法.
 */
function luhn(mixed $value): bool
{
    if (!is_scalar($value)) {
        return false;
    }

    $value = (string) ($value);
    if (!preg_match('/^[0-9]+$/', $value)) {
        return false;
    }

    $total = 0;
    for ($i = strlen($value); $i >= 1; $i--) {
        $index = $i - 1;
        if (0 === $i % 2) {
            $total += (int) $value[$index];
        } else {
            $m = (int) $value[$index] * 2;
            if ($m > 9) {
                $m = (int) ($m / 10) + $m % 10;
            }
            $total += $m;
        }
    }

    return 0 === $total % 10;
}

class luhn
{
}
