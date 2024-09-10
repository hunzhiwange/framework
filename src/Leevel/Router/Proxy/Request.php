<?php

declare(strict_types=1);

namespace Leevel\Router\Proxy;

use Leevel\Di\Container;
use Leevel\Http\Request as BaseRequest;

/**
 * 代理 request.
 */
class Request
{
    /**
     * 代理服务.
     */
    public static function proxy(): BaseRequest
    {
        // @phpstan-ignore-next-line
        return Container::singletons()->make('request');
    }
}
