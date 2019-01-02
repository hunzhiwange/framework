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

namespace Leevel\Page;

use JsonSerializable;
use Leevel\Support\IArray;
use Leevel\Support\IHtml;
use Leevel\Support\IJson;
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
class Page implements IPage, IJson, IArray, IHtml, JsonSerializable
{
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
     * 缓存 url 地址
     *
     * @var string
     */
    protected $cachedUrl;

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
     * 构造函数.
     *
     * @param int   $currentPage
     * @param int   $perPage
     * @param int   $totalRecord
     * @param array $option
     */
    public function __construct(int $currentPage, ?int $perPage = null, ?int $totalRecord = null, array $option = [])
    {
        $this->currentPage = $currentPage;
        $this->perPage = $perPage;
        $this->totalRecord = $totalRecord;

        $this->option = array_merge($this->option, $option);
    }

    /**
     * 转化为字符串.
     *
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->render();
    }

    /**
     * 设置配置.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     */
    public function setOption(string $name, $value): IPage
    {
        $this->option[$name] = $value;

        return $this;
    }

    /**
     * 转化输出 HTML.
     *
     * @return string
     */
    public function toHtml(): string
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
    public function append(string $key, string $value): IPage
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
    public function appends(array $values): IPage
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
    public function parameter(array $parameter): IPage
    {
        return $this->setOption('parameter', $parameter);
    }

    /**
     * 添加分页条件.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function addParameter(string $key, $value): IPage
    {
        $tmp = $this->option['parameter'];
        $tmp[$key] = $value;

        $this->setOption('parameter', $tmp);

        return $this;
    }

    /**
     * 设置渲染参数.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function renderOption(string $key, $value): IPage
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
    public function renderOptions(array $option): IPage
    {
        foreach ($option as $key => $value) {
            $this->renderOption($key, $value);
        }

        return $this;
    }

    /**
     * 设置 url.
     *
     * @param null|string $url
     *
     * @return $this
     */
    public function url(?string $url = null): IPage
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
    public function setRender(?string $render = null): IPage
    {
        return $this->setOption('render', $render);
    }

    /**
     * 获取 render.
     *
     * @return null|string
     */
    public function getRender(): string
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
    public function range(?int $range = null): IPage
    {
        return $this->setOption('range', $range);
    }

    /**
     * 获取 range.
     *
     * @return int
     */
    public function getRange(): int
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
    public function fragment(?string $fragment = null): IPage
    {
        return $this->setOption('fragment', $fragment);
    }

    /**
     * 获取 url 描点.
     *
     * @return null|string
     */
    public function getFragment(): ?string
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
    public function perPage(int $perPage): IPage
    {
        $this->perPage = $perPage;

        return $this;
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
    public function pageName(string $pageName): IPage
    {
        return $this->setOption('page', $pageName);
    }

    /**
     * 获取分页名字.
     *
     * @return string
     */
    public function getPageName(): string
    {
        return $this->option['page'];
    }

    /**
     * 返回总记录数量.
     *
     * @return null|int
     */
    public function getTotalRecord(): ?int
    {
        return $this->totalRecord;
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
    public function getFromRecord(): int
    {
        return ($this->getCurrentPage() - 1) * $this->getPerPage();
    }

    /**
     * 取得最后一个记录的编号.
     *
     * @return null|int
     */
    public function getToRecord(): ?int
    {
        if (!$this->canTotalRender()) {
            return null;
        }

        $to = $this->getFromRecord() + $this->getPerPage();

        return $to <= $this->getTotalRecord() ? $to : $this->getTotalRecord();
    }

    /**
     * 设置当前分页.
     *
     * @param int $page
     */
    public function currentPage(int $page): void
    {
        $this->currentPage = $page;
    }

    /**
     * 返回当前分页.
     *
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * 返回分页视图开始页码
     *
     * @return int
     */
    public function getPageStart(): int
    {
        if (null !== $this->pageStart) {
            return $this->pageStart;
        }

        $this->pageStart = $this->getCurrentPage() - $this->getRange();

        if ($this->pageStart < $this->getRange() * 2) {
            $this->pageStart = 1;
        }

        return $this->pageStart;
    }

    /**
     * 返回分页视图结束页码
     *
     * @return int
     */
    public function getPageEnd(): int
    {
        if (null !== $this->pageEnd) {
            return $this->pageEnd;
        }

        $this->pageEnd = $this->getCurrentPage() + $this->getRange();

        if (1 === $this->getPageStart()) {
            $this->pageEnd = $this->getRange() * 2 + 2;
        }

        if ($this->getTotalPage() &&
            $this->pageEnd > $this->getTotalPage()) {
            $this->pageEnd = $this->getTotalPage();
        }

        return $this->pageEnd;
    }

    /**
     * 返回总分页数量.
     *
     * @return null|int
     */
    public function getTotalPage(): ?int
    {
        if (null !== $this->totalPage || !$this->getTotalRecord()) {
            return $this->totalPage;
        }

        $this->totalPage = (int) (
            ceil($this->getTotalRecord() / $this->getPerPage())
        );

        return $this->totalPage;
    }

    /**
     * 是否渲染 total.
     *
     * @return bool
     */
    public function canTotalRender(): bool
    {
        return null !== $this->getTotalRecord() &&
            !$this->isTotalMacro();
    }

    /**
     * 是否渲染 first.
     *
     * @return bool
     */
    public function canFirstRender(): bool
    {
        return $this->getTotalPage() > 1 &&
            $this->getCurrentPage() >= ($this->getRange() * 2 + 2);
    }

    /**
     * 返回渲染 first.prev.
     *
     * @return int
     */
    public function parseFirstRenderPrev(): int
    {
        return $this->getCurrentPage() - ($this->getRange() * 2 + 1);
    }

    /**
     * 是否渲染 prev.
     *
     * @return bool
     */
    public function canPrevRender(): bool
    {
        return (null === $this->getTotalPage() || $this->getTotalPage() > 1) &&
            1 !== $this->getCurrentPage();
    }

    /**
     * 返回渲染 prev.prev.
     *
     * @return int
     */
    public function parsePrevRenderPrev(): int
    {
        return $this->getCurrentPage() - 1;
    }

    /**
     * 是否渲染 main.
     *
     * @return bool
     */
    public function canMainRender(): bool
    {
        return $this->getTotalPage() > 1;
    }

    /**
     * 是否渲染 next.
     *
     * @return bool
     */
    public function canNextRender(): bool
    {
        return null === $this->getTotalPage() ||
            ($this->getTotalPage() > 1 &&
                $this->getCurrentPage() !== $this->getTotalPage());
    }

    /**
     * 是否渲染 last.
     *
     * @return bool
     */
    public function canLastRender(): bool
    {
        return $this->getTotalPage() > 1 &&
            $this->getCurrentPage() !== $this->getTotalPage() &&
            $this->getTotalPage() > $this->getPageEnd();
    }

    /**
     * 是否渲染 last.
     *
     * @return bool
     */
    public function canLastRenderNext(): bool
    {
        return $this->getTotalPage() > ($this->getPageEnd() + 1);
    }

    /**
     * 返回渲染 last.next.
     *
     * @return int
     */
    public function parseLastRenderNext(): int
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
     * 替换分页变量.
     *
     * @param int|string $page
     *
     * @return string
     */
    public function pageReplace($page): string
    {
        return str_replace([
            urlencode('{page}'),
            '{page}',
        ], $page, $this->getUrl());
    }

    /**
     * 渲染分页.
     *
     * @param null|\Leevel\Page\IRender|string $render
     * @param array                            $option
     *
     * @return string
     */
    public function render($render = null, array $option = []): string
    {
        $option = array_merge($this->option['render_option'], $option);

        if (null === $render || is_string($render)) {
            $render = $render ?: $this->getRender();
            $render = 'Leevel\Page\\'.ucfirst($render);
            $render = new $render($this);
        } elseif (!$render instanceof IRender) {
            throw new RuntimeException('Unsupported render type.');
        }

        $result = $render->render($option);

        $this->cachedUrl = null;

        return $result;
    }

    /**
     * 对象转数组.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'per_page'     => $this->getPerPage(),
            'current_page' => $this->getCurrentPage(),
            'total_page'   => $this->getTotalPage(),
            'total_record' => $this->getTotalRecord(),
            'total_macro'  => $this->isTotalMacro(),
            'from'         => $this->getFromRecord(),
            'to'           => $this->getToRecord(),
        ];
    }

    /**
     * 实现 JsonSerializable::jsonSerialize.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * 对象转 JSON.
     *
     * @param int $option
     *
     * @return string
     */
    public function toJson($option = null): string
    {
        if (null === $option) {
            $option = JSON_UNESCAPED_UNICODE;
        }

        return json_encode($this->jsonSerialize(), $option);
    }

    /**
     * 分析分页 url 地址.
     * {page} 表示自定义分页变量替换.
     *
     * @return string
     */
    protected function getUrl(): string
    {
        if (null !== $this->cachedUrl) {
            return $this->cachedUrl;
        }

        $url = (string) ($this->option['url']);

        $parameter = $this->option['parameter'];

        if (isset($parameter[$this->option['page']])) {
            unset($parameter[$this->option['page']]);
        }

        if (false === strpos($url, '{page}')) {
            $parameter[$this->option['page']] = '{page}';
        }

        $this->cachedUrl = $url.
            (false === strpos($url, '?') ? '?' : '&').
            http_build_query($parameter, '', '&');

        return $this->cachedUrl .= $this->buildFragment();
    }

    /**
     * 创建描点.
     *
     * @return string
     */
    protected function buildFragment(): string
    {
        return $this->getFragment() ? '#'.$this->getFragment() : '';
    }
}
