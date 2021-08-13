<?php

declare(strict_types=1);

namespace Leevel\Router\Matching;

use Leevel\Http\Request;
use Leevel\Router\IRouter;

/**
 * PathInfo 路由匹配.
 */
class PathInfo extends BaseMatching implements IMatching
{
    /**
     * {@inheritDoc}
     */
    public function match(IRouter $router, Request $request): array
    {
        $this->setRouterAndRequest($router, $request);

        return $this->matchMain();
    }

    /**
     * 主匹配.
     */
    protected function matchMain(): array
    {
        $path = $this->normalizePath($this->getPathInfo());
        list($result, $path) = $this->matchApp($path);
        if (!$path) {
            return $result;
        }

        return array_merge($result, $this->matchMvc($path, $result[IRouter::PREFIX] ?? []));
    }

    /**
     * 格式化 PathInfo.
     */
    protected function normalizePath(string $pathInfo): array
    {
        $pathInfo = trim($pathInfo, '/');

        return $pathInfo ? explode('/', $pathInfo) : [];
    }

    /**
     * 匹配路由应用.
     */
    protected function matchApp(array $path): array
    {
        $result = [];
        if ($path && $this->isFindApp($path[0])) {
            $result[IRouter::APP] = substr(array_shift($path), 1);
        }

        if ($path && false !== ($prefixPosition = $this->getPrefixPosition($pathString = implode('/', $path)))) {
            $result[IRouter::PREFIX] = explode('/', substr($pathString, 0, $prefixPosition));
            $path = explode('/', substr($pathString, $prefixPosition + 1));
        }

        if ($restfulResult = $this->matchRestful($path)) {
            return [array_merge($result, $restfulResult), []];
        }

        if (!$path) {
            $result[IRouter::CONTROLLER] = IRouter::DEFAULT_CONTROLLER;
        }

        return [$result, $path];
    }

    /**
     * 匹配路由 MVC.
     */
    protected function matchMvc(array $path, array $prefix): array
    {
        $result = [];
        if (1 === count($path)) {
            $result[IRouter::CONTROLLER] = array_pop($path);

            return $result;
        }

        if ($path) {
            $result[IRouter::ACTION] = array_pop($path);
        }

        if ($path) {
            $result[IRouter::CONTROLLER] = array_pop($path);
        }

        if ($path) {
            $result[IRouter::PREFIX] = array_merge($prefix, $path);
        }

        return $result;
    }

    /**
     * 是否找到 app.
     */
    protected function isFindApp(string $path): bool
    {
        return 0 === strpos($path, ':');
    }

    /**
     * 找到路由前缀.
     */
    protected function getPrefixPosition(string $path): int|false
    {
        return strpos($path, ':');
    }

    /**
     * 匹配路由 Restful.
     */
    protected function matchRestful(array $path): array
    {
        $restfulPath = implode('/', $path);
        $regex = '/^(\S+)\/('.IRouter::RESTFUL_REGEX.')(\/*\S*)$/';
        if (!preg_match($regex, $restfulPath, $matches)) {
            return [];
        }

        $result[IRouter::CONTROLLER] = $matches[1];
        $result[IRouter::ATTRIBUTES][IRouter::RESTFUL_ID] = $matches[2];
        if ('' !== $matches[3]) {
            $result[IRouter::ACTION] = substr($matches[3], 1);
        }

        return $result;
    }
}
