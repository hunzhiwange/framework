<?php

declare(strict_types=1);

namespace Leevel\Kernel\Utils;

/**
 * 文档Api结构属性.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Api
{
    public function __construct(...$args) // @phpstan-ignore-line
    {}
}
