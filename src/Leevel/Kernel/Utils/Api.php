<?php

declare(strict_types=1);

namespace Leevel\Kernel\Utils;

/**
 * 文档Api结构属性.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_FUNCTION)]
readonly class Api
{
    public function __construct(array $api = []) // @phpstan-ignore-line
    {}
}
