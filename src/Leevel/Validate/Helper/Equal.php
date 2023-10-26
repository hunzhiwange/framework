<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

class Equal
{
    /**
     * 两个值是否相同.
     *
     * - 全等匹配，为了严禁.
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
