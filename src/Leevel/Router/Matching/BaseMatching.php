<?php

declare(strict_types=1);

/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
     *
     * @var \Leevel\Router\IRouter
     */
    protected IRouter $router;

    /**
     * HTTP Request.
     */
    protected Request $request;

    /**
     * 匹配中间件.
     */
    protected array $middlewares = [];

    /**
     * 设置路由和请求.
     */
    protected function setRouterAndRequest(IRouter $router, Request $request): void
    {
        $this->request = $request;
        $this->router = $router;
    }

    /**
     * 匹配 PathInfo.
     */
    protected function matchPathInfo(): string
    {
        $matcheData = $this->matchBasePaths($pathInfo = $this->getPathInfo());
        $this->middlewares = $matcheData['middlewares'];

        return $pathInfo;
    }

    /**
     * 取得 PathInfo.
     */
    protected function getPathInfo(): string
    {
        return rtrim($this->request->getPathInfo(), '/').'/';
    }

    /**
     * 匹配基础路径.
     */
    protected function matchBasePaths(string $pathInfo): array
    {
        $result = ['middlewares' => []];
        foreach ($this->router->getBasePaths() as $item => $option) {
            if ('*' === $item || preg_match((string) $item, $pathInfo, $matches)) {
                if (isset($option['middlewares'])) {
                    $result['middlewares'] = $this->mergeMiddlewares($result['middlewares'], $option['middlewares']);
                }
            }
        }

        return $result;
    }

    /**
     * 合并中间件.
     */
    protected function mergeMiddlewares(array $middlewares, array $newMiddlewares): array
    {
        return $this->router->mergeMiddlewares($middlewares, $newMiddlewares);
    }
}
