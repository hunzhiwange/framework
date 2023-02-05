<?php

declare(strict_types=1);

namespace Leevel\Encryption\Helper;

class CustomStripslashes
{
    /**
     * 移除魔术方法转义.
     */
    public static function handle(mixed $data, bool $recursive = true): mixed
    {
        if (true === $recursive && \is_array($data)) {
            $result = [];
            foreach ($data as $key => $value) {
                $result[self::handle($key)] = self::handle($value);
            }

            return $result;
        }

        if (\is_string($data)) {
            $data = stripslashes($data);
        }

        return $data;
    }
}
