<?php

declare(strict_types=1);

namespace Leevel\Database\Ddd;

/**
 * 实体结构属性.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Struct
{
    public function __construct(array $struct) // @phpstan-ignore-line
    {}
}
