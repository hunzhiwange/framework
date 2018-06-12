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

use Swagger\Context;
use InvalidArgumentException;
use Swagger\Annotations\Swagger;

/*
 * Swagger 注解路由
 * 1:忽略已删除的路由 deprecated 和带有 _ignore 的路由
 * 2:如果没有绑定路由参数 _bind,系统会尝试自动解析注解所在控制器方法
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2018.04.10
 * @version 1.0
 */
class SwaggerRouter
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
     * 控制器相对目录.
     *
     * @var string
     */
    protected $controllerDir = 'App\Controller';

    /**
     * swagger 扫描目录.
     *
     * @var array
     */
    protected $swaggerScan = [];

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
        'strict',
        'bind',
        'middlewares',
    ];

    /**
     * 构造函数.
     *
     * @param \Leevel\Router\MiddlewareParser $middlewareParser
     * @param string                          $domain
     * @param string                          $controllerDir
     */
    public function __construct(MiddlewareParser $middlewareParser, $domain = null, $controllerDir = null)
    {
        $this->middlewareParser = $middlewareParser;

        if ($domain) {
            $this->domain = $domain;
        }

        if ($controllerDir) {
            $controllerDir = str_replace('/', '\\', $controllerDir);
            $this->controllerDir = $controllerDir;
        }

        // 忽略 swagger 扩展字段警告,改变 set_error_handler 抛出时机
        // 补充基于标准 swagger 路由，并可以扩展注解路由的功能
        error_reporting(E_ERROR | E_PARSE | E_STRICT);
    }

    /**
     * 添加一个扫描目录.
     *
     * @param string $dir
     */
    public function addSwaggerScan(string $dir)
    {
        if (! is_dir($dir)) {
            throw new InvalidArgumentException('Dir is exits.');
        }

        $this->swaggerScan[] = $dir;
    }

    /**
     * 处理 swagger 注解路由.
     *
     * @return array
     */
    public function handle()
    {
        $swagger = $this->makeSwagger();

        list($basepaths, $basepathPrefix) = $this->parseBasepaths($swagger);
        $groups = $this->parseGroups($swagger);

        $routers = [];

        if ($swagger->paths) {
            foreach ($swagger->paths as $path) {
                foreach ($this->methods as $m) {
                    $method = $path->$m;

                    // 忽略已删除和带有忽略标记的路由
                    if (! $method || true === $method->deprecated || (property_exists($method, '_ignore') && $method->_ignore)) {
                        continue;
                    }

                    $routerTmp = [];

                    // 支持的自定义路由字段
                    foreach ($this->routerField as $f) {
                        $field = '_' . $f;
                        $routerTmp[$f] = property_exists($method, $field) ? $method->$field : null;
                    }

                    // 根据源代码生成绑定
                    if (! $routerTmp['bind']) {
                        $routerTmp['bind'] = $this->parseBindBySource($method->_context);
                    }

                    // 解析中间件
                    if ($routerTmp['middlewares']) {
                        $routerTmp['middlewares'] = $this->middlewareParser->handle($routerTmp['middlewares']);
                    }

                    $routerPath = $path->path;

                    if (strlen($routerPath) > 1 && preg_match('/^[A-Za-z]+$/', $routerPath[1])) {
                        $prefix = $routerPath[1];
                    } else {
                        $prefix = '_';
                    }

                    $groupPrefix = '_';
                    foreach ($groups as $g) {
                        if (0 === strpos($routerPath, $g)) {
                            $groupPrefix = $g;
                            break;
                        }
                    }

                    // 解析域名
                    $routerTmp['domain'] = $this->normalizeDomain($routerTmp['domain'], $this->domain);

                    if ($routerTmp['domain'] && false !== strpos($routerTmp['domain'], '{')) {
                        list($routerTmp['domain_regex'], $routerTmp['domain_var']) = $this->ruleRegex($routerTmp['domain'], $routerTmp, true);
                    } else {
                        list($routerTmp['domain_regex'], $routerTmp['domain_var']) = [null, null];
                    }

                    // 解析路由正则
                    $isStaticRoute = false;

                    $routerPath = $basepathPrefix . $routerPath;
                    if (false !== strpos($routerPath, '{')) {
                        list($routerTmp['regex'], $routerTmp['var']) = $this->ruleRegex($routerPath, $routerTmp);
                    } else {
                        $isStaticRoute = true;
                        list($routerTmp['regex'], $routerTmp['var']) = [null, null];
                    }

                    if (true === $isStaticRoute) {
                        $routers[$m]['static'][$routerPath] = $routerTmp;
                    } else {
                        $routers[$m][$prefix][$groupPrefix][$routerPath] = $routerTmp;
                    }
                }
            }
        }

        $routers = $this->normalizeFastRoute($routers);

        return [
            'basepaths' => $basepaths,
            'groups' => $groups,
            'routers' => $routers,
        ];
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
                if ('static' == $firstKey) {
                    continue;
                }

                foreach ($second as $secondKey => &$three) {
                    $groups = $this->parseToGroups($three);

                    foreach ($groups as $groupKey => $groupThree) {
                        list($three['regex'][$groupKey], $three['map'][$groupKey]) = $this->parseGroupRegex($groupThree);
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

            ++$groupIndex;
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

            $regex[] = '|' . $router['regex'] . ($emptyMatche ? str_repeat('()', $emptyMatche) : '');

            ++$ruleKey;
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
     * @param \Swagger\Context $context
     *
     * @return string|null
     */
    protected function parseBindBySource(Context $context)
    {
        if (! $context->class || ! $context->method) {
            return null;
        }

        $className = $context->fullyQualifiedName($context->class);
        $segmentation = '\\' . $this->controllerDir . '\\';

        if (strpos($className, $segmentation) < 1) {
            return null;
        } else {
            $tmp = explode($segmentation, $className);
            $router = ':' . ltrim($tmp[0], '\\') . '\\' . $tmp[1] . '\\' . $context->method;
            $method = str_replace('\\', '/', $router);

            return $method;
        }
    }

    /**
     * 分析分组标签.
     *
     * @param \Swagger\Annotations\Swagger
     *
     * @return array
     */
    protected function parseGroups(Swagger $swagger)
    {
        $groups = [];

        if ($swagger->tags) {
            foreach ($swagger->tags as $tag) {
                if (property_exists($tag, '_group')) {
                    $groups[] = '/' . $tag->_group;
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
            'find' => [],
            'replace' => [],
        ];

        $rule = preg_replace_callback('/{(.+?)}/', function ($matches) use ($routers, &$routerVar, &$mapRegex) {
            if (false !== strpos($matches[1], ':')) {
                list($routerVar[], $regex) = explode(':', $matches[1]);
            } else {
                $routerVar[] = $matches[1];
                $regex = Router::DEFAULT_REGEX;
            }

            $regex = '(' . $regex . ')';
            $regexEncode = '#' . md5($regex) . '#';

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
            $strict = ($routers['strict'] ?? Router::DEFAULT_STRICT) ? '$' : '';
            $rule = '/^' . $rule . $strict . '/';
        }

        return [
            $rule,
            $routerVar,
        ];
    }

    /*
     * 格式化域名
     * 如果没有设置域名，则加上顶级域名
     *
     * @param string $domain
     * @param string $topDomain
     * @return string
     */
    protected function normalizeDomain(?string $domain, ?string $topDomain)
    {
        if (! $domain || ! $this->domain) {
            return $domain;
        }

        if ($topDomain !== substr($domain, -strlen($topDomain))) {
            $domain .= '.' . $topDomain;
        }

        return $domain;
    }

    /**
     * 分析基础路径.
     *
     * @param \Swagger\Annotations\Swagger $swagger
     *
     * @return array
     */
    protected function parseBasepaths(Swagger $swagger): array
    {
        $basepaths = [];
        $basepathPrefix = '';

        if ($swagger->basePath) {
            $basepaths[] = $swagger->basePath;
            $basepathPrefix = $swagger->basePath;
        }

        return [
            $basepaths,
            $basepathPrefix,
        ];
    }

    /**
     * 生成 swagger.
     *
     * @return \Swagger\Annotations\Swagger
     */
    protected function makeSwagger()
    {
        return \Swagger\scan($this->swaggerScan);
    }
}
