<?php

declare(strict_types=1);

namespace Leevel\Router;

/**
 * 忽略路由属性.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_FUNCTION)]
class IgnoreRoute
{
    public function __construct(...$args) // @phpstan-ignore-line
    {}
}
