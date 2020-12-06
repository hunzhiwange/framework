<?php

declare(strict_types=1);

namespace Leevel\Validate;

/**
 * 验证在给定日期之后.
 */
class AfterRule
{
    use Date;

    /**
     * 校验.
     */
    public function validate(mixed $value, array $param, IValidator $validator, string $field): bool
    {
        return $this->validateDate($value, $param, $validator, $field);
    }
}
