<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

class Luhn
{
    public const string MESSAGE = '{field} 必须为正确的符合 luhn 格式算法银行卡';

    public const string ANTISENSE_MESSAGE = '{field} 不能为正确的符合 luhn 格式算法银行卡';

    /**
     * 值是否为银行卡等符合 luhn 算法.
     */
    public static function handle(mixed $value): bool
    {
        if (!\is_scalar($value)) {
            return false;
        }

        $value = (string) $value;
        if (!preg_match('/^[0-9]+$/', $value)) {
            return false;
        }

        $total = 0;
        for ($i = \strlen($value); $i >= 1; --$i) {
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
}
