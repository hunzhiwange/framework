<?php

declare(strict_types=1);

namespace Leevel\Router;

/**
 * 路由属性.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_FUNCTION)]
readonly class Route
{
    public function __construct(
        ?string $method = null,
        ?string $path = null,
        ?string $bind = null,
        ?array $attributes = null,
        null|array|string $middlewares = null
    ) {}
}
