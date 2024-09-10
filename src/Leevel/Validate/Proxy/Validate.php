<?php

declare(strict_types=1);

namespace Leevel\Validate\Proxy;

use Leevel\Di\Container;
use Leevel\Validate\Validate as BaseValidate;

/**
 * 代理 validate.
 */
class Validate
{
    /**
     * 代理服务.
     */
    public static function proxy(): BaseValidate
    {
        // @phpstan-ignore-next-line
        return Container::singletons()->make('validate');
    }
}
