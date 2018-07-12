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

namespace Leevel\Page;

use Leevel\Support\IHtml;
use RuntimeException;

/**
 * 分页处理.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.07.14
 *
 * @version 1.0
 */
abstract class Connect implements IHtml
{
    /**
     * 默认每页分页数量.
     *
     * @var int
     */
    const PER_PAGE = 15;

    /**
     * 无穷大记录数.
     *
     * @var int
     */
    const MACRO = 999999999;

    /**
     * 默认分页渲染.
     *
     * @var int
     */
    const RENDER = 'defaults';

    /**
     * 默认范围.
     *
     * @var int
     */
    const RANGE = 2;

    /**
     * 总记录数量.
     *
     * @var int
     */
    protected $totalRecord;

    /**
     * 每页分页数量.
     *
     * @var int
     */
    protected $perPage;

    /**
     * 当前分页页码
     *
     * @var int
     */
    protected $currentPage;

    /**
     * 总页数.
     *
     * @var int
     */
    protected $totalPage;

    /**
     * 分页开始位置.
     *
     * @var int
     */
    protected $pageStart;

    /**
     * 分页结束位置.
     *
     * @var int
     */
    protected $pageEnd;

    /**
     * 解析后参数变量.
     *
     * @var array
     */
    protected $resolveParameter;

    /**
     * 解析后 url 地址
     *
     * @var string
     */
    protected $resolveUrl;

    /**
     * 解析 url.
     *
     * @var callable
     */
    protected static $urlResolver;

    /**
     * 配置.
     *
     * @var array
     */
    protected $option = [
        'page'          => 'page',
        'range'         => 2,
        'render'        => 'defaults',
        'render_option' => [],
        'url'           => null,
        'parameter'     => [],
        'fragment'      => null,
    ];

    /**
     * 转化为字符串.
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->render();
    }

    /**
     * 设置配置.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function setOption(string $name, $value): void
    {
        $this->option[$name] = $value;
    }

    /**
     * 转化输出 HTML.
     *
     * @return string
     */
    public function toHtml()
    {
        return (string) $this->render();
    }

    /**
     * 追加分页条件.
     *
     * @param string $key
     * @param string $value
     *
     * @return $this
     */
    public function append($key, $value)
    {
        return $this->addParameter($key, $value);
    }

    /**
     * 批量追加分页条件.
     *
     * @param array $values
     *
     * @return $this
     */
    public function appends(array $values)
    {
        foreach ($values as $key => $value) {
            $this->addParameter($key, $value);
        }

        return $this;
    }

    /**
     * 设置分页条件.
     *
     * @param array $parameter
     *
     * @return $this
     */
    public function parameter(array $parameter)
    {
        return $this->setOption('parameter', $parameter);
    }

    /**
     * 添加分页条件.
     *
     * @param string $key
     * @param string $value
     *
     * @return $this
     */
    public function addParameter($key, $value)
    {
        if ($key !== $this->getPageName()) {
            $tmp = $this->option['parameter'];
            $tmp[$key] = $value;

            $this->setOption('parameter', $tmp);
        }

        return $this;
    }

    /**
     * 设置渲染参数.
     *
     * @param string $key
     * @param string $value
     *
     * @return $this
     */
    public function renderOption($key, $value)
    {
        $tmp = $this->option['render_option'];
        $tmp[$key] = $value;

        $this->setOption('render_option', $tmp);

        return $this;
    }

    /**
     * 批量设置渲染参数.
     *
     * @param string $key
     * @param string $value
     *
     * @return $this
     */
    public function renderOptions(array $option)
    {
        foreach ($option as $key => $value) {
            $this->renderOption($key, $value);
        }

        return $this;
    }

    /**
     * 获取渲染参数.
     *
     * @return $this
     */
    public function getRenderOption()
    {
        return $this->option['render_option'];
    }

    /**
     * 设置 url.
     *
     * @param null|string $url
     *
     * @return $this
     */
    public function url(?string $url = null)
    {
        return $this->setOption('url', $url);
    }

    /**
     * 设置 render.
     *
     * @param null|string $render
     *
     * @return $this
     */
    public function renders(?string $render = null)
    {
        return $this->setOption('render', $render);
    }

    /**
     * 获取 render.
     *
     * @return null|string
     */
    public function getRender()
    {
        return $this->option['render'] ?: static::RENDER;
    }

    /**
     * 设置 range.
     *
     * @param null|int $range
     *
     * @return $this
     */
    public function range(?int $range = null)
    {
        return $this->setOption('range', $range);
    }

    /**
     * 获取 range.
     *
     * @return int
     */
    public function getRange()
    {
        return $this->option['range'] ?
            (int) ($this->option['range']) :
            static::RANGE;
    }

    /**
     * 设置 url 描点.
     *
     * @param null|string $fragment
     *
     * @return $this
     */
    public function fragment(?string $fragment = null)
    {
        return $this->setOption('fragment', $fragment);
    }

    /**
     * 获取 url 描点.
     *
     * @return null|string
     */
    public function getFragment()
    {
        return $this->option['fragment'];
    }

    /**
     * 设置每页分页数量.
     *
     * @param int perPage
     *
     * @return $this
     */
    public function perPage(int $perPage)
    {
        return $this->perPage = $perPage;
    }

    /**
     * 返回每页数量.
     *
     * @return int
     */
    public function getPerPage(): int
    {
        if (null === $this->perPage) {
            $this->perPage = static::PER_PAGE;
        }

        return $this->perPage;
    }

    /**
     * 设置分页名字.
     *
     * @param string $pageName
     *
     * @return $this
     */
    public function pageName(string $pageName)
    {
        return $this->setOption('page', $pageName);
    }

    /**
     * 获取分页名字.
     *
     * @return string
     */
    public function getPageName()
    {
        return $this->option['page'];
    }

    /**
     * 返回总记录数量.
     *
     * @return int
     */
    public function getTotalRecord(): int
    {
        return true === $this->totalRecord ?
            static::MACRO :
            $this->totalRecord;
    }

    /**
     * 是否为无限分页.
     *
     * @return bool
     */
    public function isTotalMacro(): bool
    {
        return $this->getTotalRecord() === static::MACRO;
    }

    /**
     * 取得第一个记录的编号.
     *
     * @return int
     */
    public function getFirstRecord()
    {
        if (!$this->canTotalRender()) {
            return;
        }

        return ($this->getCurrentPage() - 1) * $this->getPerPage() + 1;
    }

    /**
     * 取得最后一个记录的编号.
     *
     * @return int
     */
    public function getLastRecord()
    {
        if (!$this->canTotalRender()) {
            return;
        }

        return $this->getFirstRecord() + $this->getTotalRecord() - 1;
    }

    /**
     * 返回当前分页.
     *
     * @return int
     */
    public function getCurrentPage()
    {
        if (null === $this->currentPage) {
            $parameter = $this->option['parameter'];

            if (isset($parameter[$this->option['page']])) {
                $this->currentPage = abs((int) ($parameter[$this->option['page']]));

                if ($this->currentPage < 1) {
                    $this->currentPage = 1;
                }
            } else {
                $this->currentPage = 1;
            }
        }

        return $this->currentPage;
    }

    /**
     * 返回分页视图开始页码
     *
     * @return int
     */
    public function getPageStart()
    {
        if (null === $this->pageStart) {
            $this->pageStart = $this->getCurrentPage() - $this->getRange();

            if ($this->pageStart < $this->getRange() * 2) {
                $this->pageStart = 1;
            }
        }

        return $this->pageStart;
    }

    /**
     * 返回分页视图结束页码
     *
     * @return int
     */
    public function getPageEnd()
    {
        if (null === $this->pageEnd) {
            $this->pageEnd = $this->getCurrentPage() + $this->getRange();
            if (1 === $this->getPageStart()) {
                $this->pageEnd = $this->getRange() * 2 + 2;
            }

            if ($this->getTotalPage() &&
                $this->pageEnd > $this->getTotalPage()) {
                $this->pageEnd = $this->getTotalPage();
            }
        }

        return $this->pageEnd;
    }

    /**
     * 返回总分页数量.
     *
     * @return int
     */
    public function getTotalPage()
    {
        if (null === $this->totalPage && $this->getTotalRecord()) {
            $this->totalPage = ceil($this->getTotalRecord() / $this->getPerPage());

            if ($this->totalPage < 1) {
                $this->totalPage = 1;
            }
        }

        return $this->totalPage;
    }

    /**
     * 是否渲染 total.
     *
     * @return bool
     */
    public function canTotalRender()
    {
        return null !== $this->getTotalRecord() &&
            !$this->isTotalMacro();
    }

    /**
     * 是否渲染 first.
     *
     * @return bool
     */
    public function canFirstRender()
    {
        return $this->getTotalPage() > 1 &&
            $this->getCurrentPage() >= ($this->getRange() * 2 + 2);
    }

    /**
     * 返回渲染 first.prev.
     *
     * @return int
     */
    public function parseFirstRenderPrev()
    {
        return $this->getCurrentPage() - ($this->getRange() * 2 + 1);
    }

    /**
     * 是否渲染 prev.
     *
     * @return bool
     */
    public function canPrevRender()
    {
        return (null === $this->getTotalPage() ||
                $this->getTotalPage() > 1) &&
            1 !== $this->getCurrentPage();
    }

    /**
     * 返回渲染 prev.prev.
     *
     * @return int
     */
    public function parsePrevRenderPrev()
    {
        return $this->getCurrentPage() - 1;
    }

    /**
     * 是否渲染 main.
     *
     * @return bool
     */
    public function canMainRender()
    {
        return $this->getTotalPage() > 1;
    }

    /**
     * 是否渲染 next.
     *
     * @return string
     */
    public function canNextRender()
    {
        return null === $this->getTotalPage() ||
            ($this->getTotalPage() > 1 &&
                $this->getCurrentPage() !== $this->getTotalPage());
    }

    /**
     * 是否渲染 last.
     *
     * @return string
     */
    public function canLastRender()
    {
        return $this->getTotalPage() > 1 &&
            $this->getCurrentPage() !== $this->getTotalPage() &&
            $this->getTotalPage() > $this->getPageEnd();
    }

    /**
     * 是否渲染 last.
     *
     * @return string
     */
    public function canLastRenderNext()
    {
        return $this->getTotalPage() > $this->getPageEnd() + 1;
    }

    /**
     * 返回渲染 last.next.
     *
     * @return int
     */
    public function parseLastRenderNext()
    {
        $next = $this->getCurrentPage() +
            $this->getRange() * 2 + 1;

        if (!$this->isTotalMacro() &&
            $next > $this->getTotalPage()) {
            $next = $this->getTotalPage();
        }

        return $next;
    }

    /**
     * 设置 url 解析回调.
     *
     * @param callable $urlResolver
     */
    public static function setUrlResolver(callable $urlResolver)
    {
        static::$urlResolver = $urlResolver;
    }

    /**
     * 替换分页变量.
     *
     * @param mixed $page
     *
     * @return string
     */
    public function pageReplace($page)
    {
        return str_replace([
            urlencode('{page}'),
            '{page}',
        ], $page, $this->getUrl());
    }

    /**
     * 统计元素数量 count($obj).
     *
     * @return int
     */
    public function count()
    {
    }

    /**
     * 实现 ArrayAccess::offsetExists.
     *
     * @param string $offset
     *
     * @return mixed
     */
    public function offsetExists($offset)
    {
    }

    /**
     * 实现 ArrayAccess::offsetGet.
     *
     * @param string $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
    }

    /**
     * 实现 ArrayAccess::offsetSet.
     *
     * @param string $offset
     * @param mixed  $value
     */
    public function offsetSet($offset, $value)
    {
    }

    /**
     * 实现 ArrayAccess::offsetUnset.
     *
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
    }

    /**
     * 分析分页 url 地址
     *
     * {page} 表示自定义分页变量替换
     * 带有 @ 表示使用 url 函数进行二次解析 @/list-{page}
     * foo@ 表示具有子域名 subdomain@blog/list/{page}
     *
     * @return string
     */
    protected function getUrl()
    {
        if (null !== $this->resolveUrl) {
            return $this->resolveUrl;
        }

        $withUrl = false;
        $subdomain = 'www';

        $url = (string) ($this->option['url']);

        if (false !== strpos($url, '@')) {
            $withUrl = true;

            if (0 !== strpos($url, '@')) {
                $temp = explode('@', $url);
                $subdomain = $temp[0];
                $url = $temp[1];
            }
        }

        if ($withUrl) {
            $this->resolveUrl = $this->resolverUrl(
                $url,
                $this->getDefaultPageParameter(
                    false === strpos($url, '{page}')
                ),
                $subdomain
            );
        } else {
            if (false === strpos($url, '{page}')) {
                $this->resolveUrl = (false === strpos($url, '?') ? '?' : '&').
                    $this->option['page'].'={page}';
            }
        }

        $this->resolveUrl = $this->resolveUrl.
            $this->buildFragment();

        return $this->resolveUrl;
    }

    /**
     * 解析 url.
     *
     * @return string
     */
    protected function resolverUrl()
    {
        if (!static::$urlResolver) {
            throw new RuntimeException('Page not set url resolver');
        }

        return call_user_func_array(
            static::$urlResolver,
            func_get_args()
        );
    }

    /**
     * 默认分页参数.
     *
     * @param bool $withDefaults
     *
     * @return array
     */
    protected function getDefaultPageParameter($withDefaults = true)
    {
        return $withDefaults ? [
            $this->option['page'] => '{page}',
        ] : [];
    }

    /**
     * 解析参数.
     *
     * @param array $extend
     *
     * @return array
     */
    protected function getParameter(array $extend)
    {
        if (null === $this->resolveParameter) {
            if ($this->option['parameter']) {
                if (is_string($this->option['parameter'])) {
                    parse_str($this->option['parameter'], $this->resolveParameter);
                } elseif (is_array($this->option['parameter'])) {
                    $this->resolveParameter = $this->option['parameter'];
                }
            } else {
                $this->resolveParameter = [];
            }
        }

        return array_merge($this->resolveParameter, $extend);
    }

    /**
     * 创建描点.
     *
     * @return string
     */
    protected function buildFragment()
    {
        return $this->getFragment() ?
            '#'.$this->getFragment() :
            '';
    }
}
