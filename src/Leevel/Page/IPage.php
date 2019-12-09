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
     * @var string
     */
    const RENDER = 'render';

    /**
     * 默认范围.
     *
     * @var int
     */
    const RANGE = 2;

    /**
     * 设置配置.
     *
     * @param mixed $value
     *
     * @return \Leevel\Page\IPage
     */
    public function setOption(string $name, $value): self;

    /**
     * 追加分页条件.
     *
     * @return \Leevel\Page\IPage
     */
    public function append(string $key, string $value): self;

    /**
     * 批量追加分页条件.
     *
     * @return \Leevel\Page\IPage
     */
    public function appends(array $value): self;

    /**
     * 设置分页条件.
     *
     * @return \Leevel\Page\IPage
     */
    public function param(array $param): self;

    /**
     * 添加分页条件.
     *
     * @param mixed $value
     *
     * @return \Leevel\Page\IPage
     */
    public function addParam(string $key, $value): self;

    /**
     * 设置渲染参数.
     *
     * @param mixed $value
     *
     * @return \Leevel\Page\IPage
     */
    public function renderOption(string $key, $value): self;

    /**
     * 批量设置渲染参数.
     *
     * @return \Leevel\Page\IPage
     */
    public function renderOptions(array $option): self;

    /**
     * 设置 URL.
     *
     * @return \Leevel\Page\IPage
     */
    public function url(?string $url = null): self;

    /**
     * 设置渲染组件.
     *
     * @return \Leevel\Page\IPage
     */
    public function setRender(?string $render = null): self;

    /**
     * 获取渲染组件.
     */
    public function getRender(): ?string;

    /**
     * 设置 range.
     *
     * @return \Leevel\Page\IPage
     */
    public function range(?int $range = null): self;

    /**
     * 获取 range.
     *
     * @return int
     */
    public function getRange();

    /**
     * 设置 URL 描点.
     *
     * @return \Leevel\Page\IPage
     */
    public function fragment(?string $fragment = null): self;

    /**
     * 获取 URL 描点.
     */
    public function getFragment(): ?string;

    /**
     * 设置每页分页数量.
     *
     * @return \Leevel\Page\IPage
     */
    public function perPage(int $perPage): self;

    /**
     * 返回每页数量.
     */
    public function getPerPage(): int;

    /**
     * 设置分页名字.
     *
     * @return \Leevel\Page\IPage
     */
    public function pageName(string $pageName): self;

    /**
     * 获取分页名字.
     */
    public function getPageName(): string;

    /**
     * 返回总记录数量.
     */
    public function getTotalRecord(): ?int;

    /**
     * 是否为无限分页.
     */
    public function isTotalMacro(): bool;

    /**
     * 取得第一个记录的编号.
     */
    public function getFromRecord(): int;

    /**
     * 取得最后一个记录的编号.
     */
    public function getToRecord(): ?int;

    /**
     * 设置当前分页.
     */
    public function currentPage(int $page): void;

    /**
     * 返回当前分页.
     */
    public function getCurrentPage(): int;

    /**
     * 返回分页视图开始页码.
     */
    public function getPageStart(): int;

    /**
     * 返回分页视图结束页码.
     */
    public function getPageEnd(): int;

    /**
     * 返回总分页数量.
     */
    public function getTotalPage(): ?int;

    /**
     * 是否渲染 total.
     */
    public function canTotalRender(): bool;

    /**
     * 是否渲染 first.
     */
    public function canFirstRender(): bool;

    /**
     * 返回渲染 first.prev.
     */
    public function parseFirstRenderPrev(): int;

    /**
     * 是否渲染 prev.
     */
    public function canPrevRender(): bool;

    /**
     * 返回渲染 prev.prev.
     */
    public function parsePrevRenderPrev(): int;

    /**
     * 是否渲染 main.
     */
    public function canMainRender(): bool;

    /**
     * 是否渲染 next.
     */
    public function canNextRender(): bool;

    /**
     * 是否渲染 last.
     *
     * @return string
     */
    public function canLastRender(): bool;

    /**
     * 是否渲染 last.
     */
    public function canLastRenderNext(): bool;

    /**
     * 返回渲染 last.next.
     */
    public function parseLastRenderNext(): int;

    /**
     * 替换分页变量.
     *
     * @param int|string $page
     */
    public function pageReplace($page): string;

    /**
     * 渲染分页.
     *
     * @param null|\Leevel\Page\IRender|string $render
     *
     * @throws \RuntimeException
     */
    public function render($render = null, array $option = []): string;
}
