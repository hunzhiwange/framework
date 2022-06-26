<?php

declare(strict_types=1);

namespace Leevel\Validate;

/**
 * 验证在给定日期之前.
 */
class BeforeRule
{
    use Date;

    /**
     * 校验.
     */
    public function handle(mixed $value, array $param, IValidator $validator, string $field): bool
    {
        return $this->validateDate($value, $param, $validator, $field, true);
    }
}
