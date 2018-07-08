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

/**
 * IPage 接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.07.19
 *
 * @version 1.0
 */
interface IPage
{
    /**
     * 追加分页条件.
     *
     * @param string $key
     * @param string $value
     *
     * @return $this
     */
    public function append($key, $value);

    /**
     * 批量追加分页条件.
     *
     * @param array $value
     *
     * @return $this
     */
    public function appends(array $value);

    /**
     * 设置分页条件.
     *
     * @param array $parameter
     *
     * @return $this
     */
    public function parameter(array $parameter);

    /**
     * 添加分页条件.
     *
     * @param string $key
     * @param string $value
     *
     * @return $this
     */
    public function addParameter($key, $value);

    /**
     * 设置渲染参数.
     *
     * @param string $key
     * @param string $value
     *
     * @return $this
     */
    public function renderOption($key, $value);

    /**
     * 批量设置渲染参数.
     *
     * @param string $key
     * @param string $value
     *
     * @return $this
     */
    public function renderOptions(array $option);

    /**
     * 是否启用 CSS.
     *
     * @param bool $on
     *
     * @return $this
     */
    public function css(bool $on = true);

    /**
     * 获取渲染参数.
     *
     * @return $this
     */
    public function getRenderOption();

    /**
     * 设置 url.
     *
     * @param null|string $url
     *
     * @return $this
     */
    public function url(?string $url = null);

    /**
     * 设置 render.
     *
     * @param null|string $render
     *
     * @return $this
     */
    public function renders(?string $render = null);

    /**
     * 获取 render.
     *
     * @return null|string
     */
    public function getRender();

    /**
     * 设置 range.
     *
     * @param null|int $range
     *
     * @return $this
     */
    public function range(?int $range = null);

    /**
     * 获取 range.
     *
     * @return int
     */
    public function getRange();

    /**
     * 设置 url 描点.
     *
     * @param null|string $fragment
     *
     * @return $this
     */
    public function fragment(?string $fragment = null);

    /**
     * 获取 url 描点.
     *
     * @return null|string
     */
    public function getFragment();

    /**
     * 设置每页分页数量.
     *
     * @param int $perPage
     *
     * @return $this
     */
    public function perPage(int $perPage);

    /**
     * 返回每页数量.
     *
     * @return int
     */
    public function getPerPage(): int;

    /**
     * 设置分页名字.
     *
     * @param string $pageName
     *
     * @return $this
     */
    public function pageName(string $pageName);

    /**
     * 获取分页名字.
     *
     * @return string
     */
    public function getPageName();

    /**
     * 返回总记录数量.
     *
     * @return int
     */
    public function getTotalRecord(): int;

    /**
     * 是否为无限分页.
     *
     * @return bool
     */
    public function isTotalMacro(): bool;

    /**
     * 取得第一个记录的编号.
     *
     * @return int
     */
    public function getFirstRecord();

    /**
     * 取得最后一个记录的编号.
     *
     * @return int
     */
    public function getLastRecord();

    /**
     * 返回当前分页.
     *
     * @return int
     */
    public function getCurrentPage();

    /**
     * 返回分页视图开始页码
     *
     * @return int
     */
    public function getPageStart();

    /**
     * 返回分页视图结束页码
     *
     * @return int
     */
    public function getPageEnd();

    /**
     * 返回总分页数量.
     *
     * @return int
     */
    public function getTotalPage();

    /**
     * 是否渲染 total.
     *
     * @return bool
     */
    public function canTotalRender();

    /**
     * 是否渲染 first.
     *
     * @return bool
     */
    public function canFirstRender();

    /**
     * 返回渲染 first.prev.
     *
     * @return int
     */
    public function parseFirstRenderPrev();

    /**
     * 是否渲染 prev.
     *
     * @return bool
     */
    public function canPrevRender();

    /**
     * 返回渲染 prev.prev.
     *
     * @return int
     */
    public function parsePrevRenderPrev();

    /**
     * 是否渲染 main.
     *
     * @return bool
     */
    public function canMainRender();

    /**
     * 是否渲染 next.
     *
     * @return string
     */
    public function canNextRender();

    /**
     * 是否渲染 last.
     *
     * @return string
     */
    public function canLastRender();

    /**
     * 是否渲染 last.
     *
     * @return string
     */
    public function canLastRenderNext();

    /**
     * 返回渲染 last.next.
     *
     * @return int
     */
    public function parseLastRenderNext();

    /**
     * 解析 url.
     *
     * @return string
     */
    public function resolverUrl();

    /**
     * 设置 url 解析回调.
     *
     * @param callable $urlResolver
     */
    public static function setUrlResolver(callable $urlResolver);

    /**
     * 替换分页变量.
     *
     * @param mixed $page
     *
     * @return string
     */
    public function pageReplace($page);
}
