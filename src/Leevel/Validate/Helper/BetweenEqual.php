<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

class BetweenEqual
{
    public const string MESSAGE = '{field} 只能在 {rule} 和 {rule1} 之间，包含等于';

    public const string ANTISENSE_MESSAGE = '{field} 不能在 {rule} 和 {rule1} 之间，不包含等于';

    /**
     * 处于 betweenEqual 范围，包含全等.
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

        return $value >= $param[0] && $value <= $param[1];
    }
}
