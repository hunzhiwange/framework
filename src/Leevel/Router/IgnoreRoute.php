<?php

declare(strict_types=1);

namespace Leevel\Router;

/**
 * 忽略路由属性.
 */
#[\Attribute]
class IgnoreRoute
{
    public function __construct(...$args)
    {
    }
}
