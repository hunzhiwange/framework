<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

class In
{
    public const string MESSAGE = '{field} 必须在 {rule} 范围内';

    public const string ANTISENSE_MESSAGE = '{field} 不能在 {rule} 范围内';

    /**
     * 是否处于某个范围.
     *
     * @throws \InvalidArgumentException
     */
    public static function handle(mixed $value, array $param): bool
    {
        if (!\array_key_exists(0, $param)) {
            throw new \InvalidArgumentException('Missing the first element of param.');
        }

        return \in_array($value, $param);
    }
}
