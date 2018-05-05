<?php declare(strict_types=1);
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
 * IPage 接口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.07.19
 * @version 1.0
 */
interface IPage
{

    /**
     * 追加分页条件
     *
     * @param string $strKey
     * @param string $strValue
     * @return $this
     */
    public function append($strKey, $strValue);

    /**
     * 批量追加分页条件
     *
     * @param array $arrValue
     * @return $this
     */
    public function appends(array $arrValue);

    /**
     * 设置分页条件
     *
     * @param array $arrParameter
     * @return $this
     */
    public function parameter(array $arrParameter);

    /**
     * 添加分页条件
     *
     * @param string $strKey
     * @param string $strValue
     * @return $this
     */
    public function addParameter($strKey, $strValue);

    /**
     * 设置渲染参数
     *
     * @param string $strKey
     * @param string $strValue
     * @return $this
     */
    public function renderOption($strKey, $strValue);

    /**
     * 批量设置渲染参数
     *
     * @param string $strKey
     * @param string $strValue
     * @return $this
     */
    public function renderOptions(array $arrOption);

    /**
     * 是否启用 CSS
     *
     * @param boolean $booOn
     * @return $this
     */
    public function css($booOn = true);

    /**
     * 获取渲染参数
     *
     * @return $this
     */
    public function getRenderOption();

    /**
     * 设置 url
     *
     * @param string|null $mixUrl
     * @return $this
     */
    public function url($mixUrl = null);

    /**
     * 设置 render
     *
     * @param string|null $mixRender
     * @return $this
     */
    public function renders($mixRender = null);

    /**
     * 获取 render
     *
     * @return string|null
     */
    public function getRender();

    /**
     * 设置 range
     *
     * @param int|null $intRange
     * @return $this
     */
    public function range($mixRange = null);

    /**
     * 获取 range
     *
     * @return int
     */
    public function getRange();

    /**
     * 设置 url 描点
     *
     * @param string|null $mixFragment
     * @return $this
     */
    public function fragment($mixFragment = null);

    /**
     * 获取 url 描点
     *
     * @return string|null
     */
    public function getFragment();

    /**
     * 设置每页分页数量
     *
     * @param string $strPageName
     * @return $this
     */
    public function perPage($strPageName);

    /**
     * 返回每页数量
     *
     * @return int
     */
    public function getPerPage();

    /**
     * 设置分页名字
     *
     * @param string $strPageName
     * @return $this
     */
    public function pageName($strPageName);

    /**
     * 获取分页名字
     *
     * @return string
     */
    public function getPageName();

    /**
     * 返回总记录数量
     *
     * @return int
     */
    public function getTotalRecord();

    /**
     * 是否为无限分页
     *
     * @return boolean
     */
    public function isTotalMacro();

    /**
     * 取得第一个记录的编号
     *
     * @return int
     */
    public function getFirstRecord();

    /**
     * 取得最后一个记录的编号
     *
     * @return int
     */
    public function getLastRecord();

    /**
     * 返回当前分页
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
     * 返回总分页数量
     *
     * @return int
     */
    public function getTotalPage();

    /**
     * 是否渲染 total
     *
     * @return boolean
     */
    public function canTotalRender();

    /**
     * 是否渲染 first
     *
     * @return boolean
     */
    public function canFirstRender();

    /**
     * 返回渲染 first.prev
     *
     * @return int
     */
    public function parseFirstRenderPrev();

    /**
     * 是否渲染 prev
     *
     * @return boolean
     */
    public function canPrevRender();

    /**
     * 返回渲染 prev.prev
     *
     * @return int
     */
    public function parsePrevRenderPrev();

    /**
     * 是否渲染 main
     *
     * @return boolean
     */
    public function canMainRender();

    /**
     * 是否渲染 next
     *
     * @return string
     */
    public function canNextRender();

    /**
     * 是否渲染 last
     *
     * @return string
     */
    public function canLastRender();

    /**
     * 是否渲染 last
     *
     * @return string
     */
    public function canLastRenderNext();

    /**
     * 返回渲染 last.next
     *
     * @return int
     */
    public function parseLastRenderNext();

    /**
     * 解析 url
     *
     * @return string
     */
    public function resolverUrl();

    /**
     * 设置 url 解析回调
     *
     * @param callable $calUrlResolver
     * @return void
     */
    public static function setUrlResolver(callable $calUrlResolver);

    /**
     * 替换分页变量
     *
     * @param mixed $mixPage
     * @return string
     */
    public function pageReplace($mixPage);
}
