<?php

declare(strict_types=1);

namespace Leevel\Router\Proxy;

use Leevel\Di\Container;
use Leevel\Router\Response as RouterResponse;

/**
 * 代理 response.
 */
class Response
{
    /**
     * 代理服务.
     */
    public static function proxy(): RouterResponse
    {
        // @phpstan-ignore-next-line
        return Container::singletons()->make('response');
    }
}
