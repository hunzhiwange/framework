<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

class Accepted
{
    public const string MESSAGE = '{field} 必须是 yes、on、true 或者 1';

    /**
     * 是否可接受的.
     */
    public static function handle(mixed $value): bool
    {
        return Required::handle($value)
            && \in_array($value, [
                'yes',
                'on',
                't',
                '1',
                1,
                true,
                'true',
            ], true);
    }
}
