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
use Leevel\Support\Arr;
use OpenApi\Annotations\OpenApi;
use OpenApi\Context;

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
     * 构造函数.
     *
     * @param \Leevel\Router\MiddlewareParser $middlewareParser
     * @param string                          $domain
     */
    public function __construct(MiddlewareParser $middlewareParser, $domain = null)
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
    public function addScandir(string $dir)
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
    public function handle()
    {
        $openApi = $this->makeOpenApi();
        list($basePaths, $groupPaths) = $this->parsePaths($openApi);
        $groups = $this->parseGroups($openApi);
        $routers = [];

        if ($openApi->paths) {
            foreach ($openApi->paths as $path) {
                foreach ($this->methods as $m) {
                    $method = $path->{$m};

                    // 忽略已删除和带有忽略标记的路由
                    if (!is_object($method) || true === $method->deprecated ||
                        (property_exists($method, 'leevelIgnore') && $method->leevelIgnore)) {
                        continue;
                    }

                    $routerTmp = [];

                    // 支持的自定义路由字段
                    foreach ($this->routerField as $f) {
                        $field = 'leevel'.ucfirst($f);

                        if (property_exists($method, $field)) {
                            $routerTmp[$f] = $method->{$field};
                        }
                    }

                    // 根据源代码生成绑定
                    if (empty($routerTmp['bind'])) {
                        $routerTmp['bind'] = $this->parseBindBySource($method->_context);
                    }

                    if ($routerTmp['bind']) {
                        $routerTmp['bind'] = '\\'.trim($routerTmp['bind'], '\\');
                    }

                    // 解析基础路径和分组
                    // 基础路径如 /api/v1、/web/v2 等等
                    // 分组例如 goods、orders
                    // 首页 `/` 默认提供 Home::show 需要过滤
                    $routerPath = '/'.trim($path->path, '/').'/';
                    $pathPrefix = '';

                    if ('//' === $routerPath) {
                        continue;
                    }

                    if ($groupPaths) {
                        foreach ($groupPaths as $groupPath => $item) {
                            if (0 === strpos($routerPath, $groupPath)) {
                                $pathPrefix = $groupPath;
                                $routerPath = substr($routerPath, strlen($groupPath));

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

                    // 解析中间件
                    if (!empty($routerTmp['middlewares'])) {
                        $routerTmp['middlewares'] = $this->middlewareParser->handle(
                            Arr::normalize($routerTmp['middlewares'])
                        );
                    }

                    // 解析域名
                    $routerTmp['domain'] = $this->normalizeDomain($routerTmp['domain'] ?? '', $this->domain);

                    if ($routerTmp['domain'] && false !== strpos($routerTmp['domain'], '{')) {
                        list($routerTmp['domain_regex'], $routerTmp['domain_var']) =
                            $this->ruleRegex($routerTmp['domain'], $routerTmp, true);
                    }

                    if (!$routerTmp['domain']) {
                        unset($routerTmp['domain']);
                    }

                    // 解析路由正则
                    $isStaticRoute = false;

                    $routerPath = $pathPrefix.$routerPath;

                    if (false !== strpos($routerPath, '{')) {
                        list($routerTmp['regex'], $routerTmp['var']) =
                            $this->ruleRegex($routerPath, $routerTmp);
                    } else {
                        $isStaticRoute = true;
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
            'base_paths'      => $basePaths ?: [],
            'group_paths'     => $groupPaths ? $groupPaths : [],
            'groups'          => $groups,
            'routers'         => $routers,
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
    protected function parseBindBySource(Context $context)
    {
        if (!$context->class || !$context->method) {
            return;
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
    protected function parseGroups(OpenApi $openApi)
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
            $regexEncode = '#'.md5($regex).'#';

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

        return [
            $rule,
            $routerVar,
        ];
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
    protected function normalizeDomain(?string $domain, ?string $topDomain)
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
            return [];
        }

        $externalDocs = $openApi->externalDocs;

        if (!property_exists($externalDocs, 'leevels')) {
            return [];
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
                    Arr::normalize($value['middlewares'])
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
    protected function prepareRegexForWildcard(string $regex)
    {
        $regex = preg_quote($regex, '/');
        $regex = '/^'.str_replace('\*', '(\S+)', $regex).'$/';

        return $regex;
    }

    /**
     * 生成 OpenApi.
     *
     * @return \OpenApi\Annotations\OpenApi
     */
    protected function makeOpenApi(): OpenApi
    {
        // @codeCoverageIgnoreStart
        if (!function_exists('\\OpenApi\\scan')) {
            require_once dirname(__DIR__, 5).'/zircote/swagger-php/src/functions.php';
        }
        // @codeCoverageIgnoreEnd

        return \OpenApi\scan($this->scandirs);
    }
}
