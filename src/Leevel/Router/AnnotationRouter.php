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

namespace Leevel\Router;

use InvalidArgumentException;
use Leevel\Kernel\Utils\ClassParser;
use function Leevel\Support\Arr\normalize;
use Leevel\Support\Arr\normalize;
use function Leevel\Support\Type\arr;
use Leevel\Support\Type\arr;
use ReflectionClass;
use Symfony\Component\Finder\Finder;

/**
 * 注解路由.
 *
 * - 1.1.0-alpha2 之前在最新的 zircote/swagger-php 3 上构建的路由，支持最新的 OpenApi 3.0 规范.
 * - 新版本采用 PHP 8 属性作为数据源提供。
 */
class AnnotationRouter
{
    /**
     * 路由中间件分析器.
     */
    protected MiddlewareParser $middlewareParser;

    /**
     * 顶级域名.
    */
    protected ?string $domain = null;

    /**
     * 扫描目录.
     */
    protected array $scandirs = [];

    /**
     * 支持的方法.
     */
    protected array $methods = [
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
     */
    protected array $routerField = [
        'scheme',
        'domain',
        'port',
        'attributes',
        'bind',
        'middlewares',
    ];

    /**
     * 匹配基础路径.
     */
    protected array $basePaths = [];

    /**
     * 匹配分组.
     */
    protected array $groups = [];

    /**
     * 构造函数.
     */
    public function __construct(MiddlewareParser $middlewareParser, ?string $domain = null, array $basePaths = [], array $groups = [])
    {
        $this->middlewareParser = $middlewareParser;

        if ($domain) {
            $this->domain = $domain;
        }

        if ($groups) {
            $this->groups = $this->parseGroups(array_keys($groups));
            foreach ($groups as $k => $v) {
                $basePaths[$k.'*'] = $v;
            }
        }

        if ($basePaths) {
            $this->basePaths = $this->parseBasePaths($basePaths);
        }
    }

    /**
     * 添加一个扫描目录.
     *
     * @throws \InvalidArgumentException
     */
    public function addScandir(string $dir): void
    {
        if (!is_dir($dir)) {
            $e = sprintf('Annotation routing scandir %s is not exits.', $dir);

            throw new InvalidArgumentException($e);
        }

        $this->scandirs[] = $dir;
    }

    /**
     * 查找视图目录中的视图文件.
     */
    protected function findFiles(array $paths): Finder
    {
        return (new Finder())
            ->in($paths)
            ->exclude(['vendor', 'node_modules'])
            ->followLinks()
            ->name('*.php')
            ->sortByName()
            ->files();
    }

    /**
     * 处理注解路由.
     */
    public function handle(): array
    {
        $routers = $this->parseControllerAnnotationRouters();
        $routers = $this->parseRouters($routers);
        $routers = $this->normalizeFastRoute($routers);

        return $this->packageRouters($routers);
    }

    /**
     * 打包路由解析数据.
     */
    protected function packageRouters(array $routers): array
    {
        return [
            'base_paths' => $this->basePaths,
            'groups'     => $this->groups,
            'routers'    => $routers,
        ];
    }

    /**
     * 分析控制器注解路由.
     */
    protected function parseControllerAnnotationRouters(): array
    {
        $finder = $this->findFiles($this->scandirs);
        $classParser = new ClassParser();
        $routers = [];
        foreach ($finder as $file) {
            $content = file_get_contents($file->getRealPath());
            if (false !== strpos($content, '#[Route(')) {
                $controllerClassName = $classParser->handle($file->getRealPath());
                $this->parseEachControllerAnnotationRouters($routers, $controllerClassName);
            }
        }

        return $routers;
    }

    /**
     * 分析每一个控制器注解路由.
     */
    protected function parseEachControllerAnnotationRouters(array &$routers, string $controllerClassName): void
    {
        $ref = new ReflectionClass($controllerClassName);
        $routeAttribute = (substr($controllerClassName, 0, strrpos($controllerClassName, '\\')).'\\Route');
        foreach ($ref->getMethods() as $v) {
            if($routeAttributes = $v->getAttributes($routeAttribute)) {
                $temp = $routeAttributes[0]->getArguments();
                if (empty($temp['method'])) {
                    $temp['method'] = 'get';
                }
                $temp['method'] = strtolower($temp['method']);
                if (!array_key_exists('bind', $temp)) {
                    $temp['bind'] = $controllerClassName.'@'.$v->getName();
                }
                if ($temp['bind']) {
                    $temp['bind'] = '\\'.trim($temp['bind'], '\\');
                }
                $routers[$temp['method']][] = $temp;
            }
        }
    }

    /**
     * 解析路由.
     */
    protected function parseRouters(array $result): array
    {
        $routers = [];
        foreach ($result as $httpMethod => $items) {
            $this->parseHttpMethodAnnotationRouters($routers, $httpMethod, $items);
        }

        return $routers;
    }

    /**
     * 解析 HTTP 不同类型请求路由.
     */
    protected function parseHttpMethodAnnotationRouters(array &$routers, string $httpMethod, array $annotationRouters): void
    {
        if (!in_array($httpMethod, $this->methods, true)) {
            return;
        }

        foreach ($annotationRouters as $router) {
            // 忽略已删除和带有忽略标记的路由
            if ($this->isRouterIgnore($sourceRouterPath = $router['path'])) {
                continue;
            }

            // 支持的自定义路由字段
            $router = $this->parseRouterField($router);
            
            // 解析中间件
            $this->parseRouterMiddlewares($router);

            // 解析域名
            $this->parseRouterDomain($router);

            // 解析端口
            $this->parseRouterPort($router);

            // 解析基础路径
            list($prefix, $groupPrefix, $routerPath) = $this->parseRouterPath($sourceRouterPath, $this->groups);

            // 解析路由正则
            if ($this->isStaticRouter($routerPath)) {
                \ksort($router);
                $routers[$httpMethod]['static'][$routerPath] = $router;
            } else {
                $router = $this->parseRouterRegex($routerPath, $router);
                \ksort($router);
                $routers[$httpMethod][$prefix][$groupPrefix][$routerPath] = $router;
            }
        }
    }

    /**
     * 判断是否为忽略路由.
     * 
     * - 首页 `/` 默认提供 Home::index 需要过滤
     */
    protected function isRouterIgnore(string $path): bool
    {
        return '//' === $this->normalizePath($path);
    }

    /**
     * 解析自定义路由字段.
     */
    protected function parseRouterField(array $method): array
    {
        $result = [];
        foreach ($this->routerField as $f) {
            if (array_key_exists($f, $method)) {
                $result[$f] = $method[$f];
            }
        }

        return $result;
    }

    /**
     * 解析基础路径和分组.
     *
     * - 基础路径如 /api/v1、/web/v2 等等.
     * - 分组例如 goods、orders.
     */
    protected function parseRouterPath(string $path, array $groups): array
    {
        $routerPath = $this->normalizePath($path);
        $groupPrefix = '_';
        foreach ($groups as $g) {
            if (0 === strpos($routerPath, $g)) {
                $groupPrefix = $g;

                break;
            }
        }

        return [$routerPath[1], $groupPrefix, $routerPath];
    }

    /**
     * 解析中间件.
     */
    protected function parseRouterMiddlewares(array &$router): void
    {
        if (!empty($router['middlewares'])) {
            $router['middlewares'] = $this->middlewareParser->handle(
                normalize($router['middlewares'])
            );
        }
    }

    /**
     * 解析域名.
     */
    protected function parseRouterDomain(array &$router): void
    {
        $router['domain'] = $this->normalizeDomain($router['domain'] ?? '', $this->domain ?: '');
        if ($router['domain'] && false !== strpos($router['domain'], '{')) {
            list($router['domain_regex'], $router['domain_var']) =
                $this->ruleRegex($router['domain'], true);
        }

        if (!$router['domain']) {
            unset($router['domain']);
        }
    }

    /**
     * 解析端口.
     */
    protected function parseRouterPort(array &$router): void
    {
        if (isset($router['port'])) {
            $router['port'] = (int) $router['port'];
        }
    }

    /**
     * 是否为静态路由.
     */
    protected function isStaticRouter(string $router): bool
    {
        return false === strpos($router, '{');
    }

    /**
     * 解析路由正则.
     */
    protected function parseRouterRegex(string $path, array $router): array
    {
        list($router['regex'], $router['var']) = $this->ruleRegex($path);

        return $router;
    }

    /**
     * 格式化路径.
     */
    protected function normalizePath(string $path): string
    {
        return '/'.trim($path, '/').'/';
    }

    /**
     * 路由正则分组合并.
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

        return [implode('', $regex), $ruleMap];
    }

    /**
     * 计算初始最低的增长变量数量.
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
     * 格式化正则.
     */
    protected function ruleRegex(string $rule, bool $forSingleRegex = false): array
    {
        $routerVar = [];
        $mapRegex = [
            'find'    => [],
            'replace' => [],
        ];

        $rule = (string) preg_replace_callback('/{(.+?)}/', function ($matches) use (&$routerVar, &$mapRegex) {
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
     * 格式化域名.
     *
     * - 如果没有设置域名，则加上顶级域名.
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
     * 分析基础路径.
     *
     * @throws \InvalidArgumentException
     */
    protected function parseBasePaths(array $basePathsSource): array
    {
        if (!arr($basePathsSource, ['string:array'])) {
            $e = 'Router base paths and groups must be array:string:array.';

            throw new InvalidArgumentException($e);
        }

        $basePaths = [];
        foreach ($basePathsSource as $key => $value) {
            if (!empty($value['middlewares'])) {
                $value['middlewares'] = $this->middlewareParser->handle(
                    normalize($value['middlewares'])
                );
            }

            $this->filterBasePath($value);
            if (empty($value)) {
                continue;
            }

            // 值为 * 表示所有路径，其它带有的 * 为通配符
            $key = (string) $key;
            $key = '*' !== $key ? '/'.trim($key, '/') : $key;
            $key = '*' === $key ? '*' : $this->prepareRegexForWildcard($key.'/');

            $basePaths[$key] = $value;
        }

        return $basePaths;
    }

    /**
     * 过滤基础路径数据.
     */
    protected function filterBasePath(array &$basePath): void
    {
        if (empty($basePath)) {
            return;
        }

        if (isset($basePath['middlewares'])) {
            if (empty($basePath['middlewares']['handle'])) {
                unset($basePath['middlewares']['handle']);
            }
            if (empty($basePath['middlewares']['terminate'])) {
                unset($basePath['middlewares']['terminate']);
            }
        }

        if (empty($basePath['middlewares'])) {
            unset($basePath['middlewares']);
        }
    }

    /**
     * 分析分组标签.
     */
    protected function parseGroups(array $groupsSource): array
    {
        return array_map(fn (string $v): string => '/'.ltrim($v, '/'), $groupsSource);
    }

    /**
     * 通配符正则.
     */
    protected function prepareRegexForWildcard(string $regex): string
    {
        $regex = preg_quote($regex, '/');
        $regex = '/^'.str_replace('\*', '(\S*)', $regex).'$/';

        return $regex;
    }
}

// import fn.
class_exists(normalize::class);
class_exists(arr::class);
