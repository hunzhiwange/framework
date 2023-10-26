<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

class Max
{
    /**
     * 验证值上限.
     *
     * - 小于或者全等.
     *
     * @throws \InvalidArgumentException
     */
    public static function handle(mixed $value, array $param): bool
    {
        if (!\array_key_exists(0, $param)) {
            throw new \InvalidArgumentException('Missing the first element of param.');
        }

        return $value <= $param[0];
    }
}
