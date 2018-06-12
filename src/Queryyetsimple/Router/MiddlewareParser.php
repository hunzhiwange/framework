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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Router;

use InvalidArgumentException;

/**
 * 路由中间件分析.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.04.21
 *
 * @version 1.0
 */
class MiddlewareParser
{
    /**
     * 路由.
     *
     * @var \Leevel\Router\Router
     */
    protected $router;

    /**
     * 构造函数.
     *
     * @param \Leevel\Router\Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * 解析中间件
     * 最多 2 个层级支持
     *
     * @param array|string $middlewares
     *
     * @return array
     */
    public function handle($middlewares)
    {
        $middlewareGroups = $this->router->getMiddlewareGroups();
        $middlewareAlias = $this->router->getMiddlewareAlias();

        $result = [];

        foreach ((array) $middlewares as $m) {
            if (!is_string($m)) {
                throw new InvalidArgumentException('Middleware only allowed string.');
            }

            list($m, $params) = $this->parseMiddleware($m);

            if (isset($middlewareGroups[$m])) {
                foreach ((array) $middlewareGroups[$m] as $item) {
                    list($item, $params) = $this->parseMiddleware($item);

                    $result[] = $this->middlewareName($middlewareAlias[$item] ?? $item, $params);
                }
            } else {
                $result[] = $this->middlewareName($middlewareAlias[$m] ?? $m, $params);
            }
        }

        $result = [
            'handle'    => $this->normalizeMiddleware($result, 'handle'),
            'terminate' => $this->normalizeMiddleware($result, 'terminate'),
        ];

        return $result;
    }

    /**
     * 格式化中间件.
     *
     * @param array  $middlewares
     * @param string $method
     *
     * @return array
     */
    protected function normalizeMiddleware(array $middlewares, string $method)
    {
        $middlewares = array_map(function ($item) use ($method) {
            if (!class_exists($item) || !method_exists($item, $method)) {
                return '';
            }

            if (false === strpos($item, ':')) {
                return $item.'@'.$method;
            }

            return str_replace(':', '@'.$method.':', $item);
        }, $middlewares);

        $middlewares = array_filter($middlewares);

        return $middlewares;
    }

    /**
     * 分析中间件.
     *
     * @param string $middleware
     *
     * @return array
     */
    protected function parseMiddleware($middleware)
    {
        $params = '';

        if (false !== strpos($middleware, ':')) {
            list($middleware, $params) = explode(':', $middleware);
        }

        return [
            $middleware,
            $params,
        ];
    }

    /**
     * 中间件名字.
     *
     * @param string $middleware
     * @param string $params
     *
     * @return string
     */
    protected function middlewareName($middleware, $params)
    {
        return $middleware.($params ? ':'.$params : '');
    }
}
