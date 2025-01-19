<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

use Leevel\Validate\IValidator;
use Leevel\Validate\ValidatorException;

class DemoThree
{
    /**
     * @throws ValidatorException
     */
    public static function handle(mixed $value, array $param, IValidator $validator, string $field): bool
    {
        throw new ValidatorException('Demo is error.');
    }
}
