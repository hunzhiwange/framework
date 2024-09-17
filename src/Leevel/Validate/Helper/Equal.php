<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

class Equal
{
    public const string MESSAGE = '{field} 必须等于 {rule}';

    public const string ANTISENSE_MESSAGE = '{field} 不能等于 {rule}';

    /**
     * 两个值是否相同.
     *
     * @throws \InvalidArgumentException
     */
    public static function handle(mixed $value, array $param): bool
    {
        if (!\array_key_exists(0, $param)) {
            throw new \InvalidArgumentException('Missing the first element of param.');
        }

        return $value == $param[0];
    }
}
