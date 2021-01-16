<?php

declare(strict_types=1);

namespace Leevel\Router\Matching;

use Leevel\Http\Request;
use Leevel\Router\IRouter;

/**
 * 路由匹配抽象类.
 */
abstract class BaseMatching
{
    /**
     * Router.
     */
    protected IRouter $router;

    /**
     * HTTP Request.
     */
    protected Request $request;

    /**
     * 设置路由和请求.
     */
    protected function setRouterAndRequest(IRouter $router, Request $request): void
    {
        $this->request = $request;
        $this->router = $router;
    }

    /**
     * 取得 PathInfo.
     */
    protected function getPathInfo(): string
    {
        return rtrim($this->request->getPathInfo(), '/').'/';
    }
}
