<?php

declare(strict_types=1);

namespace Leevel\Support;

use Leevel\Support\Arr\ConvertJson;

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

    protected ?\Closure $callback = null;

    protected array $formatKey = [];

    protected int|string $topId = 0;

    /**
     * 构造函数.
     *
     * @throws \RuntimeException
     */
    public function __construct(array $nodes = [])
    {
        foreach ($nodes as $node) {
            if (!\is_array($node) || 3 !== \count($node)) {
                throw new \RuntimeException('The node must be an array of three elements.');
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
        return \count($this->getChild($id)) > 0;
    }

    /**
     * 验证是否存在子元素.
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
        if (!\array_key_exists($id, $this->map)) {
            return [];
        }

        $data = [];
        if (\array_key_exists($this->map[$id], $this->map)) {
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
        return \count($this->getParentsReal($id));
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
    public function normalize(?\Closure $callback = null, array $key = [], int|string $id = 0): array
    {
        $data = [];
        foreach ($this->getChild($id) as $value) {
            $item = [
                $key['value'] ?? 'value' => $value,
                $key['data'] ?? 'data' => $this->data[$value],
            ];

            if ($callback) {
                $result = $callback($item, $this);
                if (null !== $result) {
                    $item = $result;
                }
            }

            if ($children = $this->normalize($callback, $key, $value)) {
                $item[$key['children'] ?? 'children'] = $children;
            }

            $data[] = $item;
        }

        return $data;
    }

    /**
     * 设置回调.
     */
    public function setCallback(?\Closure $callback = null, array $formatKey = [], int|string $topId = 0): self
    {
        $this->callback = $callback;
        $this->formatKey = $formatKey;
        $this->topId = $topId;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function toJson(?int $option = null): string
    {
        return ConvertJson::handle($this->toArray(), $option);
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return $this->normalize($this->callback, $this->formatKey, $this->topId);
    }

    /**
     * 取得给定 ID 所有父级 ID.
     */
    protected function getParentsReal(int|string $id): array
    {
        if (!\array_key_exists($id, $this->map)) {
            return [];
        }

        $data = [];
        if (\array_key_exists($this->map[$id], $this->map)) {
            $data[] = $this->map[$id];
            $data = array_merge($data, $this->getParentsReal($this->map[$id]));
        }

        return $data;
    }
}
