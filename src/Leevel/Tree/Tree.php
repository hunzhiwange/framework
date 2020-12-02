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

namespace Leevel\Tree;

use Closure;
use function Leevel\Support\Arr\convert_json;
use Leevel\Support\Arr\convert_json;
use Leevel\Support\IArray;
use Leevel\Support\IJson;
use RuntimeException;

/**
 * 树数据处理.
 */
class Tree implements IJson, IArray
{
    /**
     * 子父关系映射.
     */
    protected array $map = [];

    /**
     * 节点数据.
     */
    protected array $data = [];

    /**
     * 构造函数.
     *
     * @throws \RuntimeException
     */
    public function __construct(array $nodes = [])
    {
        foreach ($nodes as $node) {
            if (!is_array($node) || 3 !== count($node)) {
                $e = 'The node must be an array of three elements.';

                throw new RuntimeException($e);
            }

            $this->setNode($node[0], $node[1], $node[2]);
        }
    }

    /**
     * 设置节点数据.
     */
    public function setNode(int|string $id, int|string $parent, mixed $value, bool $priority = false): void
    {
        $this->data[$id] = $value;

        if ($priority) {
            $map = [$id => $parent];
            foreach ($this->map as $key => $value) {
                $map[$key] = $value;
            }
            $this->map = $map;
        } else {
            $this->map[$id] = $parent;
        }
    }

    /**
     * 取得给定 ID 子树.
     */
    public function getChildrenTree(int|string $id = 0): array
    {
        return $this->normalize(null, [], $id);
    }

    /**
     * 取得给定 ID 一级子树 ID.
     */
    public function getChild(int|string $id): array
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
     */
    public function getChildren(int|string $id = 0): array
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
     */
    public function hasChild(int|string $id): bool
    {
        return count($this->getChild($id)) > 0;
    }

    /**
     * 验证是否存在子菜单.
     */
    public function hasChildren(int|string $id, array $validateChildren, bool $strict = true): bool
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
     */
    public function getParent(int|string $id, bool $withItSelf = false): array
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
     */
    public function getParents(int|string $id, bool $withItSelf = false): array
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
     */
    public function getLevel(int|string $id): int
    {
        return count($this->getParentsReal($id));
    }

    /**
     * 取得节点的值.
     */
    public function getData(int|string $id, mixed $defaults = null): mixed
    {
        return $this->data[$id] ?? $defaults;
    }

    /**
     * 设置节点的值.
     */
    public function setData(int|string $id, mixed $value): void
    {
        if (isset($this->data[$id])) {
            $this->data[$id] = $value;
        }
    }

    /**
     * 树转化为数组.
     */
    public function normalize(?Closure $callables = null, array $key = [], int|string $id = 0): array
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
     * {@inheritdoc}
     */
    public function toJson(?int $option = null): string
    {
        $args = func_get_args();
        array_shift($args);

        return convert_json($this->toArray(...$args), $option);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return $this->normalize(...func_get_args());
    }

    /**
     * 取得给定 ID 所有父级 ID.
     */
    protected function getParentsReal(int|string $id): array
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

// import fn.
class_exists(convert_json::class);
