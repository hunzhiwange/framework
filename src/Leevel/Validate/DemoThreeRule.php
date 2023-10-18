<?php

declare(strict_types=1);

namespace Leevel\Validate;

class DemoThreeRule
{
    public function handle(mixed $value, array $param, IValidator $validator, string $field): bool
    {
        throw new ValidatorException('Demo is error.');
    }
}
