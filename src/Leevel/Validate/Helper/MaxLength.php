<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

class MaxLength
{
    public const string MESSAGE = '{field} 不满足最大长度 {rule}';

    public const string ANTISENSE_MESSAGE = '{field} 必须小于等于 {rule}';

    /**
     * 验证数据最大长度.
     *
     * @throws \InvalidArgumentException
     */
    public static function handle(mixed $value, array $param): bool
    {
        // NULL值长度为0
        if (null === $value) {
            $value = '';
        }

        if (!\is_scalar($value)) {
            return false;
        }

        $value = (string) $value;

        if (!\array_key_exists(0, $param)) {
            throw new \InvalidArgumentException('Missing the first element of param.');
        }

        return mb_strlen($value, 'utf-8') <= (int) $param[0];
    }
}
