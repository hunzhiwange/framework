<?php

declare(strict_types=1);

namespace Leevel\Validate;

class DemoTwoRule
{
    public function handle(mixed $value, array $param, IValidator $validator, string $field): bool
    {
        if ('demo' !== $value) {
            throw new ValidatorException('Demo is error.');
        }

        return true;
    }
}
