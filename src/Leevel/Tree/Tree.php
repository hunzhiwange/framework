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
use Leevel\Support\IArray;
use Leevel\Support\IJson;
use RuntimeException;

/**
 * 树数据处理.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.09.11
 *
 * @version 1.0
 */
class Tree implements ITree, IJson, IArray
{
    /**
     * 子父关系映射.
     *
     * @var array
     */
    protected $map = [];

    /**
     * 节点数据.
     *
     * @var array
     */
    protected $data = [];

    /**
     * 构造函数.
     *
     * @param array $nodes
     */
    public function __construct(array $nodes = [])
    {
        foreach ($nodes as $node) {
            if (!is_array($node) || 3 !== count($node)) {
                throw new RuntimeException(
                    'The node must be an array of three elements.'
                );
            }

            $this->setNode($node[0], $node[1], $node[2]);
        }
    }

    /**
     * 设置节点数据.
     *
     * @param int   $id
     * @param int   $parent
     * @param mixed $value
     * @param bool  $priority
     */
    public function setNode($id, $parent, $value, bool $priority = false)
    {
        $this->data[$id] = $value;

        if ($priority) {
            $tmp = [
                $id => $parent,
            ];

            foreach ($this->map as $key => $value) {
                $tmp[$key] = $value;
            }

            $this->map = $tmp;

            unset($tmp);
        } else {
            $this->map[$id] = $parent;
        }
    }

    /**
     * 取得给定 ID 子树.
     *
     * @param int $id
     *
     * @return array
     */
    public function getChildrenTree($id = 0): array
    {
        return $this->normalize(null, [], $id);
    }

    /**
     * 取得给定 ID 一级子树 ID.
     *
     * @param int $id
     *
     * @return array
     */
    public function getChild($id): array
    {
        $data = [];

        foreach ($this->map as $key => $parent) {
            if ((string) $parent === (string) $id) {
                $data[$key] = $key;
            }
        }

        return $data;
    }

    /**
     * 取得给定 ID 所有子树 ID.
     *
     * @param int $id
     *
     * @return array
     */
    public function getChildren($id = 0): array
    {
        $data = [];

        foreach ($this->getChild($id) as $key) {
            $data[] = $key;
            $data = array_merge($data, $this->getChildren($key));
        }

        return $data;
    }

    /**
     * 取得给定 ID 是否包含子树.
     *
     * @param int $id
     *
     * @return bool
     */
    public function hasChild($id): bool
    {
        return count($this->getChild($id)) > 0;
    }

    /**
     * 验证是否存在子菜单.
     *
     * @param int   $id
     * @param array $validateChildren
     * @param bool  $strict
     *
     * @return bool
     */
    public function hasChildren($id, array $validateChildren, bool $strict = true): bool
    {
        if (empty($validateChildren)) {
            return false;
        }

        $children = $this->getChildren($id);

        if (true === $strict && array_diff($validateChildren, $children)) {
            return false;
        }

        if (false === $strict && array_intersect($validateChildren, $children)) {
            return true;
        }

        return false;
    }

    /**
     * 取得给定 ID 上级父级 ID.
     *
     * @param int  $id
     * @param bool $withItSelf
     *
     * @return array
     */
    public function getParent($id, bool $withItSelf = false): array
    {
        if (!array_key_exists($id, $this->map)) {
            return [];
        }

        $data = [];

        if (array_key_exists($this->map[$id], $this->map)) {
            $data[] = $this->map[$id];
        }

        if (true === $withItSelf) {
            $data[] = $id;
        }

        return $data;
    }

    /**
     * 取得给定 ID 所有父级 ID.
     *
     * @param int  $id
     * @param bool $withItSelf
     *
     * @return array
     */
    public function getParents($id, bool $withItSelf = false): array
    {
        $data = $this->getParentsReal($id);
        sort($data);

        if (true === $withItSelf) {
            $data[] = $id;
        }

        return $data;
    }

    /**
     * 判断级别.
     *
     * @param int $id
     *
     * @return int
     */
    public function getLevel($id): int
    {
        return count($this->getParentsReal($id));
    }

    /**
     * 取得节点的值
     *
     * @param int        $id
     * @param null|mixed $defaults
     *
     * @return mixed
     */
    public function getData($id, $defaults = null)
    {
        return $this->data[$id] ?? $defaults;
    }

    /**
     * 设置节点的值
     *
     * @param int   $id
     * @param mixed $value
     */
    public function setData($id, $value)
    {
        if (isset($this->data[$id])) {
            $this->data[$id] = $value;
        }
    }

    /**
     * 树转化为数组.
     *
     * @param \Closure   $callables
     * @param array      $key
     * @param int|string $id
     *
     * @return array
     */
    public function normalize(Closure $callables = null, array $key = [], $id = 0): array
    {
        $data = [];

        foreach ($this->getChild($id) as $value) {
            $item = [
                $key['value'] ?? 'value' => $value,
                $key['data'] ?? 'data'   => $this->data[$value],
            ];

            if ($callables) {
                $result = $callables($item, $this);

                if (null !== $result) {
                    $item = $result;
                }
            }

            if ($children = $this->normalize($callables, $key, $value)) {
                $item[$key['children'] ?? 'children'] = $children;
            }

            $data[] = $item;
        }

        return $data;
    }

    /**
     * 对象转 JSON.
     *
     * @param int $option
     *
     * @return string
     */
    public function toJson($option = null)
    {
        if (null === $option) {
            $option = JSON_UNESCAPED_UNICODE;
        }

        $args = func_get_args();
        array_shift($args);

        return json_encode($this->toArray(...$args), $option);
    }

    /**
     * 对象转数组.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->normalize(...func_get_args());
    }

    /**
     * 取得给定 ID 所有父级 ID.
     *
     * @param int $id
     *
     * @return array
     */
    protected function getParentsReal($id): array
    {
        if (!array_key_exists($id, $this->map)) {
            return [];
        }

        $data = [];

        if (array_key_exists($this->map[$id], $this->map)) {
            $data[] = $this->map[$id];
            $data = array_merge($data, $this->getParentsReal($this->map[$id]));
        }

        return $data;
    }
}
