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

namespace Leevel\Tree;

use Closure;

/**
 * ITree 接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.09.13
 *
 * @version 1.0
 */
interface ITree
{
    /**
     * 设置节点数据.
     *
     * @param int   $id
     * @param int   $parent
     * @param mixed $value
     * @param bool  $priority
     */
    public function setNode($id, $parent, $value, bool $priority = false);

    /**
     * 取得给定 ID 子树.
     *
     * @param int $id
     *
     * @return array
     */
    public function getChildrenTree($id = 0): array;

    /**
     * 取得给定 ID 一级子树 ID.
     *
     * @param int $id
     *
     * @return array
     */
    public function getChild($id): array;

    /**
     * 取得给定 ID 所有子树 ID.
     *
     * @param int $id
     *
     * @return array
     */
    public function getChildren($id = 0): array;

    /**
     * 取得给定 ID 是否包含子树.
     *
     * @param int $id
     *
     * @return bool
     */
    public function hasChild($id): bool;

    /**
     * 验证是否存在子菜单.
     *
     * @param int   $id
     * @param array $validateChildren
     * @param bool  $strict
     *
     * @return bool
     */
    public function hasChildren($id, array $validateChildren, bool $strict = true): bool;

    /**
     * 取得给定 ID 上级父级 ID.
     *
     * @param int  $id
     * @param bool $withItSelf
     *
     * @return array
     */
    public function getParent($id, bool $withItSelf = false): array;

    /**
     * 取得给定 ID 所有父级 ID.
     *
     * @param int  $id
     * @param bool $withItSelf
     *
     * @return array
     */
    public function getParents($id, bool $withItSelf = false): array;

    /**
     * 判断级别.
     *
     * @param int $id
     *
     * @return int
     */
    public function getLevel($id): int;

    /**
     * 取得节点的值
     *
     * @param int        $id
     * @param null|mixed $defaults
     *
     * @return mixed
     */
    public function getData($id, $defaults = null);

    /**
     * 设置节点的值
     *
     * @param int   $id
     * @param mixed $value
     */
    public function setData($id, $value);

    /**
     * 树转化为数组.
     *
     * @param \Closure   $callables
     * @param array      $key
     * @param int|string $id
     *
     * @return array
     */
    public function normalize(Closure $callables = null, array $key = [], $id = 0): array;
}
