<?php

declare(strict_types=1);

namespace Leevel\Router;

/**
 * 路由属性.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_FUNCTION)]
class Route
{
    public function __construct(...$args) // @phpstan-ignore-line
    {}
}
