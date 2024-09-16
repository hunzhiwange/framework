<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

class Between
{
    public const string MESSAGE = '{field} 只能在 {rule} 和 {rule1} 之间，不包含等于';

    /**
     * 处于 between 范围，不包含等于.
     *
     * @throws \InvalidArgumentException
     */
    public static function handle(mixed $value, array $param): bool
    {
        if (
            !\array_key_exists(0, $param)
            || !\array_key_exists(1, $param)
        ) {
            throw new \InvalidArgumentException('Missing the first or second element of param.');
        }

        return $value > $param[0] && $value < $param[1];
    }
}
