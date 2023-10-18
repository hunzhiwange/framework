<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

use Leevel\Validate\ValidatorException;

class Demo
{
    /**
     * 抛出异常验证.
     */
    public static function handle(mixed $value): bool
    {
        if ('demo' !== $value) {
            throw new ValidatorException('Demo is error.');
        }

        return true;
    }
}
