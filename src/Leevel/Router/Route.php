<?php

declare(strict_types=1);

namespace Leevel\Router;

/**
 * 路由属性.
 */
#[\Attribute]
class Route
{
    public function __construct(...$args) // @phpstan-ignore-line
    {
    }
}
