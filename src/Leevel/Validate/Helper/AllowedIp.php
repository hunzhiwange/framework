<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

class AllowedIp
{
    /**
     * 验证 IP 许可.
     *
     *  @throws \InvalidArgumentException
     */
    public static function handle(mixed $value, array $param): bool
    {
        if (!\is_string($value)) {
            return false;
        }

        if (!\array_key_exists(0, $param)) {
            throw new \InvalidArgumentException('Missing the first element of param.');
        }

        return \in_array($value, $param, true);
    }
}
