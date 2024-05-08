<?php

declare(strict_types=1);

namespace Leevel\Database\Ddd;

/**
 * 实体结构属性.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_FUNCTION)]
class Struct
{
    public function __construct(...$args) // @phpstan-ignore-line
    {}
}
