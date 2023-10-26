<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

use Leevel\Validate\IValidator;

class EqualTo
{
    /**
     * 两个字段是否相同.
     *
     * @throws \InvalidArgumentException
     */
    public static function handle(mixed $value, array $param, IValidator $validator): bool
    {
        if (!\array_key_exists(0, $param)) {
            throw new \InvalidArgumentException('Missing the first element of param.');
        }

        return $value == $validator->getFieldValue($param[0]);
    }
}
