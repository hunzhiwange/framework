<?php

declare(strict_types=1);

namespace Leevel\Encryption\Helper;

/**
 * 添加模式转义.
 */
class CustomAddslashes
{
    public static function handle(mixed $data, bool $recursive = true): mixed
    {
        if (true === $recursive && is_array($data)) {
            $result = [];
            foreach ($data as $key => $value) {
                $result[self::handle($key)] = self::handle($value);
            }

            return $result;
        }

        if (is_string($data)) {
            $data = addslashes($data);
        }

        return $data;
    }
}
