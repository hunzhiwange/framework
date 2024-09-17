<?php

declare(strict_types=1);

namespace Leevel\Validate\Helper;

use Leevel\Validate\Date;
use Leevel\Validate\IValidator;

class Before
{
    use Date;

    public const string MESSAGE = '{field} 日期不能超过 {rule}';

    /**
     * 验证在给定日期之前.
     */
    public static function handle(mixed $value, array $param, IValidator $validator, string $field): bool
    {
        return static::validateDate($value, $param, $validator, $field, true);
    }
}
