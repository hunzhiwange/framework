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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Router;

use InvalidArgumentException;
use function Leevel\Support\Arr\normalize;
use Leevel\Support\Arr\normalize;
use OpenApi\Annotations\OpenApi;
use OpenApi\Annotations\PathItem;
use OpenApi\Context;
use function OpenApi\scan;

/**
 * OpenApi 注解路由
 * 1:忽略已删除的路由 deprecated 和带有 leevelIgnore 的路由
 * 2:如果没有绑定路由参数 leevelBind,系统会尝试自动解析注解所在控制器方法.
 * 3:只支持最新的 zircote/swagger-php 3，支持最新的 OpenApi 3.0 规范.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.04.10
 *
 * @version 1.0
 */
class OpenApiRouter
{
    /**
     * 路由中间件分析器.
     *
     * @var \Leevel\Router\MiddlewareParser
     */
    protected $middlewareParser;

    /**
     * 顶级域名.
     *
     * @var string
     */
    protected $domain;

    /**
     * 扫描目录.
     *
     * @var array
     */
    protected $scandirs = [];

    /**
     * 支持的方法.
     *
     * @var array
     */
    protected $methods = [
        'get',
        'delete',
        'post',
        'delete',
        'options',
        'head',
        'patch',
    ];

    /**
     * 支持的路由字段.
     *
     * @var array
     */
    protected $routerField = [
        'scheme',
        'domain',
        'params',
        'bind',
        'middlewares',
    ];

    /**
     * 匹配基础路径.
     *
     * @var array
     */
    protected $basePaths = [];

    /**
     * 匹配分组路径.
     *
     * @var array
     */
    protected $groupPaths = [];

    /**
     * 匹配分组.
     *
     * @var array
     */
    protected $groups = [];

    /**
     * 构造函数.
     *
     * @param \Leevel\Router\MiddlewareParser $middlewareParser
     * @param string                          $domain
     */
    public function __construct(MiddlewareParser $middlewareParser, ?string $domain = null)
    {
        $this->middlewareParser = $middlewareParser;

        if ($domain) {
            $this->domain = $domain;
        }

        // 忽略 OpenApi 扩展字段警告,改变 set_error_handler 抛出时机
        // 补充基于标准 OpenApi 路由，并可以扩展注解路由的功能
        error_reporting(E_ERROR | E_PARSE | E_STRICT);
    }

    /**
     * 添加一个扫描目录.
     *
     * @param string $dir
     */
    public function addScandir(string $dir): void
    {
        if (!is_dir($dir)) {
            throw new InvalidArgumentException(
                sprintf('OpenApi scandir %s is exits.', $dir)
            );
        }

        $this->scandirs[] = $dir;
    }

    /**
     * 处理 OpenApi 注解路由.
     *
     * @return array
     */
    public function handle(): array
    {
        $openApi = $this->makeOpenApi();

        $this->parseMainPath($openApi);

        $routers = $this->normalizeFastRoute($this->parseMainRouters($openApi));

        return $this->packageRouters($routers);
    }

    /**
     * 打包路由解析数据.
     *
     * @param array $routers
     *
     * @return array
     */
    protected function packageRouters(array $routers): array
    {
        return [
            'base_paths'      => $this->basePaths,
            'group_paths'     => $this->groupPaths,
            'groups'          => $this->groups,
            'routers'         => $routers,
        ];
    }

    /**
     * 解析主路径.
     *
     * @param \OpenApi\Annotations\OpenApi $openApi
     */
    protected function parseMainPath(OpenApi $openApi): void
    {
        list($this->basePaths, $this->groupPaths) = $this->parsePaths($openApi);

        $this->groups = $this->parseGroups($openApi);
    }

    /**
     * 解析主路由.
     *
     * @param \OpenApi\Annotations\OpenApi $openApi
     *
     * @return array
     */
    protected function parseMainRouters(OpenApi $openApi): array
    {
        $routers = [];

        if ($openApi->paths) {
            foreach ($openApi->paths as $path) {
                $routers = $this->parseOpenApiPath($path, $routers);
            }
        }

        return $routers;
    }

    /**
     * 解析 openApi 每一项路径.
     *
     * @param \OpenApi\Annotations\PathItem $path
     * @param array                         $routers
     *
     * @return array
     */
    protected function parseOpenApiPath(PathItem $path, array $routers): array
    {
        foreach ($this->methods as $m) {
            $method = $path->{$m};

            // 忽略已删除和带有忽略标记的路由
            if ($this->isRouterIgnore($method, $path->path)) {
                continue;
            }

            $router = [];

            // 支持的自定义路由字段
            $router = $this->parseRouterField($method);

            // 根据源代码生成绑定
            $router = $this->parseRouterBind($method, $router);

            // 解析中间件
            $router = $this->parseRouterMiddlewares($router);

            // 解析域名
            $router = $this->parseRouterDomain($router);

            // 解析基础路径
            list($prefix, $groupPrefix, $routerPath) = $this->parseRouterPath($path->path, $this->groupPaths, $this->groups);

            // 解析路由正则
            if ($this->isStaticRouter($routerPath)) {
                $routers[$m]['static'][$routerPath] = $router;
            } else {
                $routers[$m][$prefix][$groupPrefix][$routerPath] =
                    $this->parseRouterRegex($routerPath, $router);
            }
        }

        return $routers;
    }

    /**
     * 判断是否为忽略路由.
     *
     * @param object|string $method
     * @param string        $path
     *
     * @return bool
     */
    protected function isRouterIgnore($method, string $path): bool
    {
        if (!is_object($method) || true === $method->deprecated ||
            (property_exists($method, 'leevelIgnore') && $method->leevelIgnore)) {
            return true;
        }

        // 首页 `/` 默认提供 Home::index 需要过滤
        if ('//' === $this->normalizePath($path)) {
            return true;
        }

        return false;
    }

    /**
     * 解析自定义路由字段.
     *
     * @param object $method
     *
     * @return array
     */
    protected function parseRouterField($method): array
    {
        $result = [];

        foreach ($this->routerField as $f) {
            $field = 'leevel'.ucfirst($f);

            if (property_exists($method, $field)) {
                $result[$f] = $method->{$field};
            }
        }

        return $result;
    }

    /**
     * 解析路由绑定.
     *
     * @param object $method
     * @param array  $router
     *
     * @return array
     */
    protected function parseRouterBind($method, array $router): array
    {
        if (empty($router['bind'])) {
            $router['bind'] = $this->parseBindBySource($method->_context);
        }

        if ($router['bind']) {
            $router['bind'] = '\\'.trim($router['bind'], '\\');
        }

        return $router;
    }

    /**
     * 解析基础路径和分组.
     * 基础路径如 /api/v1、/web/v2 等等.
     * 分组例如 goods、orders.
     *
     * @param string $path
     * @param array  $groupPaths
     * @param array  $groups
     *
     * @return array
     */
    protected function parseRouterPath(string $path, array $groupPaths, array $groups): array
    {
        $routerPath = $this->normalizePath($path);
        $pathPrefix = '';

        if ($groupPaths) {
            foreach ($groupPaths as $key => $item) {
                if (0 === strpos($routerPath, $key)) {
                    $pathPrefix = $key;
                    $routerPath = substr($routerPath, strlen($key));

                    break;
                }
            }
        }

        $prefix = $routerPath[1];
        $groupPrefix = '_';

        foreach ($groups as $g) {
            if (0 === strpos($routerPath, $g)) {
                $groupPrefix = $g;

                break;
            }
        }

        $routerPath = $pathPrefix.$routerPath;

        return [$prefix, $groupPrefix, $routerPath];
    }

    /**
     * 解析中间件.
     *
     * @param array $router
     *
     * @return array
     */
    protected function parseRouterMiddlewares(array $router): array
    {
        if (!empty($router['middlewares'])) {
            $router['middlewares'] = $this->middlewareParser->handle(
                normalize($router['middlewares'])
            );
        }

        return $router;
    }

    /**
     * 解析域名.
     *
     * @param array $router
     *
     * @return array
     */
    protected function parseRouterDomain(array $router): array
    {
        $router['domain'] = $this->normalizeDomain($router['domain'] ?? '', $this->domain ?: '');

        if ($router['domain'] && false !== strpos($router['domain'], '{')) {
            list($router['domain_regex'], $router['domain_var']) =
                $this->ruleRegex($router['domain'], $router, true);
        }

        if (!$router['domain']) {
            unset($router['domain']);
        }

        return $router;
    }

    /**
     * 是否为静态路由.
     *
     * @param string $router
     *
     * @return bool
     */
    protected function isStaticRouter(string $router): bool
    {
        return false === strpos($router, '{');
    }

    /**
     * 解析路由正则.
     *
     * @param string $path
     * @param array  $router
     *
     * @return array
     */
    protected function parseRouterRegex(string $path, array $router): array
    {
        list($router['regex'], $router['var']) = $this->ruleRegex($path, $router);

        return $router;
    }

    /**
     * 格式化路径.
     *
     * @param string $path
     *
     * @return string
     */
    protected function normalizePath(string $path): string
    {
        return '/'.trim($path, '/').'/';
    }

    /**
     * 路由正则分组合并.
     *
     * @param array $routers
     *
     * @return array
     */
    protected function normalizeFastRoute(array $routers): array
    {
        // 我和同事毛飞我们讨论了这个，基于 FastRoute 背后技术原理构建 @ 2018.05
        // 合并路由匹配规则提高匹配效率,10 个一分组
        // http://nikic.github.io/2014/02/18/Fast-request-routing-using-regular-expressions.html
        foreach ($routers as &$first) {
            foreach ($first as $firstKey => &$second) {
                if ('static' === $firstKey) {
                    continue;
                }

                foreach ($second as $secondKey => &$three) {
                    $groups = $this->parseToGroups($three);

                    foreach ($groups as $groupKey => $groupThree) {
                        list($three['regex'][$groupKey], $three['map'][$groupKey]) =
                            $this->parseGroupRegex($groupThree);
                    }
                }
            }
        }

        return $routers;
    }

    /**
     * 将路由进行分组.
     *
     * @param array $routers
     *
     * @return array
     */
    protected function parseToGroups(array &$routers): array
    {
        $groups = [];
        $groupIndex = 0;

        foreach ($routers as $key => &$item) {
            $groups[(int) ($groupIndex / 10)][$key] = $item;

            unset($item['regex']);

            $groupIndex++;
        }

        return $groups;
    }

    /**
     * 解析分组路由正则.
     *
     * @param array $routers
     *
     * @return array
     */
    protected function parseGroupRegex(array $routers): array
    {
        $minCount = $this->computeMinCountVar($routers);

        $regex = [];
        $ruleMap = [];
        $ruleKey = 0;
        $regex[] = '~^(?';

        foreach ($routers as $key => $router) {
            $countVar = $minCount + $ruleKey;
            $emptyMatche = $countVar - count($router['var']);

            $ruleMap[$countVar + 1] = $key;

            $regex[] = '|'.$router['regex'].($emptyMatche ? str_repeat('()', $emptyMatche) : '');

            $ruleKey++;
        }

        $regex[] = ')$~x';

        return [
            implode('', $regex),
            $ruleMap,
        ];
    }

    /**
     * 计算初始最低的增长变量数量.
     *
     * @param array $routers
     *
     * @return int
     */
    protected function computeMinCountVar(array $routers): int
    {
        $minCount = 1;

        foreach ($routers as $item) {
            if (($curCount = count($item['var'])) > $minCount) {
                $minCount = $curCount;
            }
        }

        return $minCount;
    }

    /**
     * 根据源代码生成绑定.
     *
     * @param \OpenApi\Context $context
     *
     * @return null|string
     */
    protected function parseBindBySource(Context $context): ?string
    {
        if (!$context->class || !$context->method) {
            return null;
        }

        return $context->fullyQualifiedName($context->class).'@'.$context->method;
    }

    /**
     * 分析分组标签.
     *
     * @param \OpenApi\Annotations\OpenApi $openApi
     *
     * @return array
     */
    protected function parseGroups(OpenApi $openApi): array
    {
        $groups = [];

        if ($openApi->tags) {
            foreach ($openApi->tags as $tag) {
                if (property_exists($tag, 'leevelGroup')) {
                    $groups[] = '/'.$tag->leevelGroup;
                }
            }
        }

        return $groups;
    }

    /**
     * 格式化正则.
     *
     * @param string $rule
     * @param array  $routers
     * @param bool   $forSingleRegex
     *
     * @return array
     */
    protected function ruleRegex(string $rule, array $routers, bool $forSingleRegex = false): array
    {
        $routerVar = [];

        $mapRegex = [
            'find'    => [],
            'replace' => [],
        ];

        $rule = preg_replace_callback('/{(.+?)}/', function ($matches) use ($routers, &$routerVar, &$mapRegex) {
            if (false !== strpos($matches[1], ':')) {
                list($routerVar[], $regex) = explode(':', $matches[1]);
            } else {
                $routerVar[] = $matches[1];
                $regex = IRouter::DEFAULT_REGEX;
            }

            $regex = '('.$regex.')';
            $regexEncode = '`'.md5($regex).'`';

            $mapRegex['find'][] = $regexEncode;
            $mapRegex['replace'][] = $regex;

            return $regexEncode;
        }, $rule);

        if (false === $forSingleRegex) {
            $rule = preg_quote($rule);
        } else {
            $rule = preg_quote($rule, '/');
        }

        if ($mapRegex['find']) {
            $rule = str_replace($mapRegex['find'], $mapRegex['replace'], $rule);
        }

        if (true === $forSingleRegex) {
            $rule = '/^'.$rule.'$/';
        }

        return [$rule, $routerVar];
    }

    /**
     * 格式化域名
     * 如果没有设置域名，则加上顶级域名.
     *
     * @param string $domain
     * @param string $topDomain
     *
     * @return string
     */
    protected function normalizeDomain(string $domain, string $topDomain): string
    {
        if (!$domain || !$this->domain) {
            return $domain;
        }

        if ($topDomain !== substr($domain, -strlen($topDomain))) {
            $domain .= '.'.$topDomain;
        }

        return $domain;
    }

    /**
     * 分析路径.
     *
     * @param \OpenApi\Annotations\OpenApi $openApi
     *
     * @return array
     */
    protected function parsePaths(OpenApi $openApi): array
    {
        if (\OpenApi\UNDEFINED === $openApi->externalDocs) {
            return [[], []];
        }

        $externalDocs = $openApi->externalDocs;

        if (!property_exists($externalDocs, 'leevels')) {
            return [[], []];
        }

        $leevels = $externalDocs->leevels;
        $tmps = is_array($leevels) ? $leevels : [$leevels];
        $basePaths = $groupPaths = [];

        foreach ($tmps as $key => $value) {
            // * 表示所有路径，group 为 true 的表示分组路径，其余为基础正则匹配路径
            // 分组路径将会在路由匹配成功后移除自身进行接下来的匹配
            $newKey = '*' !== $key ? '/'.trim($key, '/') : $key;

            if (!empty($value['middlewares'])) {
                $value['middlewares'] = $this->middlewareParser->handle(
                    normalize($value['middlewares'])
                );
            }

            if (isset($value['group']) && true === $value['group']) {
                unset($value['group']);
                $groupPaths[$newKey] = $value;
            } else {
                $newKey = '*' === $newKey ? '*' : $this->prepareRegexForWildcard($newKey.'/');
                $basePaths[$newKey] = $value;
            }
        }

        return [$basePaths, $groupPaths];
    }

    /**
     * 通配符正则.
     *
     * @param string $regex
     *
     * @return string
     */
    protected function prepareRegexForWildcard(string $regex): string
    {
        $regex = preg_quote($regex, '/');
        $regex = '/^'.str_replace('\*', '(\S*)', $regex).'$/';

        return $regex;
    }

    /**
     * 生成 OpenApi.
     *
     * @return \OpenApi\Annotations\OpenApi
     */
    protected function makeOpenApi(): OpenApi
    {
        return scan($this->scandirs);
    }
}

fns(normalize::class);
