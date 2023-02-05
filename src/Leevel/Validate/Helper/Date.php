<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

class Date
{
    /**
     * 是否为日期.
     */
    public static function handle(mixed $value): bool
    {
        if ($value instanceof \DateTime) {
            return true;
        }

        if (!\is_scalar($value)) {
            return false;
        }

        $value = (string) $value;
        if (false === strtotime($value)) {
            return false;
        }

        $value = date_parse($value);
        if (
            false === $value['year']
            || false === $value['month']
            || false === $value['day']
        ) {
            return false;
        }

        return checkdate($value['month'], $value['day'], $value['year']);
    }
}
