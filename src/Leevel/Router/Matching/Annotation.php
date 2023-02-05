<?php

declare(strict_types=1);

namespace Leevel\Router\Matching;

use Leevel\Http\Request;
use Leevel\Router\IRouter;

/**
 * 注解路由匹配.
 */
class Annotation extends BaseMatching implements IMatching
{
    /**
     * 匹配变量.
     */
    protected array $matchedVars = [];

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
        if (!($routers = $this->router->getRouters())) {
            return [];
        }

        // 匹配路由请求方法
        if (false === ($routers = $this->matchMethod($routers))) {
            return [];
        }

        // 获取 PathInfo
        $pathInfo = $this->getPathInfo();

        // 静态路由匹配
        if (false !== ($result = $this->matchStatic($routers))) {
            return $result;
        }

        // 匹配首字母
        if (false === ($routers = $this->matchFirstLetter($pathInfo, $routers))) {
            return [];
        }

        // 匹配分组
        if (!$routers = $this->matchGroups($pathInfo, $routers)) {
            return [];
        }

        // 路由匹配
        if (false !== ($result = $this->matchRegexGroups($routers))) {
            return $result;
        }

        return [];
    }

    /**
     * 匹配路由方法.
     */
    protected function matchMethod(array $routers): array|false
    {
        return $routers[strtolower($this->request->getMethod())] ?? false;
    }

    /**
     * 匹配静态路由.
     */
    protected function matchStatic(array $routers): array|false
    {
        $pathInfo = $this->getPathInfo();
        if (isset($routers['static'][$pathInfo])) {
            return $this->matchSuccessed($routers['static'][$pathInfo]);
        }

        return false;
    }

    /**
     * 匹配首字母.
     */
    protected function matchFirstLetter(string $pathInfo, array $routers): array|false
    {
        return $routers[$pathInfo[1]] ?? false;
    }

    /**
     * 匹配路由分组.
     */
    protected function matchGroups(string $pathInfo, array $routers): array
    {
        $matchGroup = false;
        foreach ($this->router->getGroups() as $group) {
            if (str_starts_with($pathInfo, $group)) {
                $routers = $routers[$group];
                $matchGroup = true;

                break;
            }
        }

        if (false === $matchGroup) {
            $routers = $routers['_'] ?? [];
        }

        return $routers;
    }

    /**
     * 匹配路由正则分组.
     */
    protected function matchRegexGroups(array $routers): array|false
    {
        $pathInfo = $this->getPathInfo();
        foreach ($routers['regex'] as $key => $regex) {
            if (!preg_match($regex, $pathInfo, $matches)) {
                continue;
            }

            $matchedRouter = $routers['map'][$key][\count($matches)];
            $router = $routers[$matchedRouter];

            return $this->matchSuccessed($router, $this->matchVariable($router, $matches));
        }

        return false;
    }

    /**
     * 注解路由匹配成功处理.
     */
    protected function matchSuccessed(array $router, array $matcheVars = []): array
    {
        // 协议匹配
        if (!empty($router['scheme'])
            && false === $this->matchScheme($router['scheme'])) {
            return [];
        }

        // 端口匹配
        if (!empty($router['port'])
            && false === $this->matchPort($router['port'])) {
            return [];
        }

        // 域名匹配
        if (false === ($domainVars = $this->matchDomain($router))) {
            return [];
        }

        // 未绑定
        if (!$router['bind']) {
            return [];
        }

        $result = [];
        $result[IRouter::BIND] = $router['bind'];
        $result[IRouter::APP] = $this->findApp($router['bind']);
        $result[$attributesKey = IRouter::ATTRIBUTES] = [];

        // 域名匹配参数 {subdomain}.{domain}
        if ($domainVars) {
            $result[$attributesKey] = (array) $domainVars;
        }

        // 路由匹配参数 /v1/pet/{id}
        if ($matcheVars) {
            $result[$attributesKey] = array_merge($result[$attributesKey], $matcheVars);
        }

        // 额外参数 ['extend1' => 'foo']
        if (isset($router['attributes']) && \is_array($router['attributes'])) {
            $result[$attributesKey] = array_merge($result[$attributesKey], $router['attributes']);
        }

        // 中间件
        if (isset($router['middlewares'])) {
            $result[IRouter::MIDDLEWARES] = $router['middlewares'];
        }

        // 匹配的变量
        $result[IRouter::VARS] = $this->matchedVars;

        return $result;
    }

    /**
     * 协议匹配.
     */
    protected function matchScheme(string $scheme): bool
    {
        return $scheme === $this->request->getScheme();
    }

    /**
     * 端口匹配.
     */
    protected function matchPort(int $port): bool
    {
        return $port === (int) $this->request->getPort();
    }

    /**
     * 域名匹配.
     */
    protected function matchDomain(array $router): array|bool
    {
        if (empty($router['domain'])) {
            return [];
        }

        if (!empty($router['domain_regex'])) {
            if (!preg_match($router['domain_regex'], $this->request->getHost(), $matches)) {
                return false;
            }

            $domainVars = [];
            array_shift($matches);
            foreach ($router['domain_var'] as $var) {
                $value = array_shift($matches);
                $domainVars[$var] = $value;
                $this->addVariable($var, $value);
            }

            return $domainVars;
        }

        return $this->request->getHost() !== $router['domain'] ? false : [];
    }

    /**
     * 变量匹配处理.
     */
    protected function matchVariable(array $router, array $matches): array
    {
        $result = [];
        array_shift($matches);
        foreach ($router['var'] as $key => $var) {
            $result[$var] = $matches[$key];
            $this->addVariable($var, $matches[$key]);
        }

        return $result;
    }

    /**
     * 添加解析变量.
     */
    protected function addVariable(string $name, mixed $value): void
    {
        $this->matchedVars[$name] = $value;
    }

    /**
     * 查找 App.
     */
    protected function findApp(string $path): string
    {
        $path = explode('\\', $path);

        return (string) array_shift($path);
    }
}
