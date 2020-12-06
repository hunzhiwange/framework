<?php

declare(strict_types=1);

namespace Leevel\Router\Matching;

use Leevel\Http\Request;
use Leevel\Router\IRouter;

/**
 * 路由匹配接口.
 */
interface IMatching
{
    /**
     * 匹配数据项.
     */
    public function match(IRouter $router, Request $request): array;
}
