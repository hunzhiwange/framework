<?php

declare(strict_types=1);

namespace Leevel\Router;

/**
 * 路由中间件分析.
 */
class MiddlewareParser
{
    /**
     * 构造函数.
     */
    public function __construct(protected IRouter $router)
    {
    }

    /**
     * 解析中间件
     * 最多 2 个层级支持
     *
     * @throws \InvalidArgumentException
     */
    public function handle(array $middlewares): array
    {
        $middlewareGroups = $this->router->getMiddlewareGroups();
        $middlewareAlias = $this->router->getMiddlewareAlias();

        $result = [];
        foreach ($middlewares as $m) {
            if (!\is_string($m)) {
                $e = 'Middleware only allowed string.';

                throw new \InvalidArgumentException($e);
            }

            [$m, $params] = $this->parseMiddlewareParams($m);
            if (isset($middlewareGroups[$m])) {
                $temp = \is_array($middlewareGroups[$m]) ?
                    $middlewareGroups[$m] : [$middlewareGroups[$m]];
                foreach ($temp as $item) {
                    [$item, $params] = $this->parseMiddlewareParams($item);
                    $result[] = $this->packageMiddleware($middlewareAlias[$item] ?? $item, $params);
                }
            } else {
                $result[] = $this->packageMiddleware($middlewareAlias[$m] ?? $m, $params);
            }
        }

        $result = [
            'handle' => $this->normalizeMiddleware($result, 'handle'),
            'terminate' => $this->normalizeMiddleware($result, 'terminate'),
        ];

        if (empty($result['handle']) && empty($result['terminate'])) {
            return [];
        }

        return $result;
    }

    /**
     * 格式化中间件.
     */
    protected function normalizeMiddleware(array $middlewares, string $method): array
    {
        $middlewares = array_map(function ($item) use ($method) {
            if (!str_contains($item, ':')) {
                $realClass = $item;
            } else {
                [$realClass] = explode(':', $item);
            }

            // ignore group like `web` or `api`
            if (str_contains($realClass, '\\') && !class_exists($realClass)) {
                $e = sprintf('Middleware %s was not found.', $realClass);

                throw new \InvalidArgumentException($e);
            }

            if (!method_exists($realClass, $method)) {
                return false;
            }

            if (!str_contains($item, ':')) {
                return $item.'@'.$method;
            }

            return str_replace(':', '@'.$method.':', $item);
        }, $middlewares);

        return array_values(array_unique(array_filter($middlewares)));
    }

    /**
     * 分析中间件.
     */
    protected function parseMiddlewareParams(string $middleware): array
    {
        $params = '';
        if (str_contains($middleware, ':')) {
            [$middleware, $params] = explode(':', $middleware);
        }

        return [$middleware, $params];
    }

    /**
     * 打包中间件.
     */
    protected function packageMiddleware(string $middleware, string $params): string
    {
        return $middleware.($params ? ':'.$params : '');
    }
}
