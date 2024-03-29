<?php

declare(strict_types=1);

namespace Leevel\Support;

use Leevel\Support\Arr\ConvertJson;
use Leevel\Support\Type\These;

/**
 * 集合.
 */
class Collection implements IArray, IJson, \IteratorAggregate, \ArrayAccess, \Countable, \JsonSerializable
{
    /**
     * 元素合集.
     */
    protected array $elements = [];

    /**
     * 验证.
     */
    protected bool $valid = true;

    /**
     * 键类型.
     */
    protected array $keyTypes = [];

    /**
     * 值类型.
     */
    protected array $valueTypes = [];

    /**
     * 构造函数.
     */
    public function __construct(mixed $elements = [], array $valueTypes = [], array $keyTypes = [])
    {
        $elements = $this->elementsToArray($elements);

        if ($valueTypes) {
            $this->valueTypes = $valueTypes;
        }
        if ($keyTypes) {
            $this->keyTypes = $keyTypes;
        }

        foreach ($elements as $key => $value) {
            $this->offsetSet($key, $value);
        }
    }

    /**
     * 实现魔术方法 __toString.
     */
    public function __toString(): string
    {
        return $this->toJson();
    }

    /**
     * 实现魔术方法 __get.
     */
    public function __get(mixed $key): mixed
    {
        return $this->offsetGet($key);
    }

    /**
     * 实现魔术方法 __set.
     */
    public function __set(mixed $key, mixed $value): void
    {
        $this->offsetSet($key, $value);
    }

    /**
     * 实现魔术方法 __isset.
     */
    public function __isset(mixed $key): bool
    {
        return $this->offsetExists($key);
    }

    /**
     * 实现魔术方法 __unset.
     *
     * - 代码格式化工具自动将 unset($obj->foo) 修改为 $obj->foo = null
     *
     * @codeCoverageIgnore
     */
    public function __unset(mixed $key): void
    {
        $this->offsetUnset($key);
    }

    /**
     * 批量添加元素.
     */
    public function batchSet(mixed $elements = []): void
    {
        $elements = $this->elementsToArray($elements);
        foreach ($elements as $key => $value) {
            $this->offsetSet($key, $value);
        }
    }

    /**
     * 创建一个集合.
     */
    public static function make(mixed $elements = [], array $valueTypes = [], array $keyTypes = []): static
    {
        return new static($elements, $valueTypes, $keyTypes);
    }

    /**
     * 当前元素.
     */
    public function current(): mixed
    {
        return current($this->elements);
    }

    /**
     * 当前 key.
     */
    public function key(): mixed
    {
        return key($this->elements);
    }

    /**
     * 下一个元素.
     */
    public function next(): void
    {
        $next = next($this->elements);
        $this->valid = false !== $next;
    }

    /**
     * 指针重置.
     */
    public function rewind(): void
    {
        reset($this->elements);
        $this->valid = true;
    }

    /**
     * 验证.
     */
    public function valid(): bool
    {
        return $this->valid;
    }

    /**
     * 获取集合键数据类型.
     */
    public function getKeyTypes(): array
    {
        return $this->keyTypes;
    }

    /**
     * 获取集合值数据类型.
     */
    public function getValueTypes(): array
    {
        return $this->valueTypes;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->elements);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetExists(mixed $index): bool
    {
        return isset($this->elements[$index]);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetGet(mixed $index): mixed
    {
        return $this->elements[$index] ?? null;
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet(mixed $index, mixed $newval): void
    {
        $this->checkType($index, true);
        $this->checkType($newval);
        $this->elements[$index] = $newval;
        $this->watchElements();
    }

    /**
     * {@inheritDoc}
     */
    public function offsetUnset(mixed $offset): void
    {
        if (isset($this->elements[$offset])) {
            unset($this->elements[$offset]);
        }
        $this->watchElements();
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        return \count($this->elements);
    }

    /**
     * 是否为空集合.
     */
    public function isEmpty(): bool
    {
        return 0 === $this->count();
    }

    /**
     * 返回所有元素.
     */
    public function all(): array
    {
        return $this->elements;
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return array_map(function ($value) {
            return $value instanceof IArray ? $value->toArray() : $value;
        }, $this->elements);
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize(): array
    {
        return array_map(function ($value) {
            if ($value instanceof IArray) {
                return $value->toArray();
            }

            if ($value instanceof IJson) {
                return json_decode($value->toJson(), true, 512, JSON_THROW_ON_ERROR);
            }

            if ($value instanceof \JsonSerializable) {
                return $value->jsonSerialize();
            }

            if ($value instanceof \stdClass) {
                return json_decode(json_encode($value, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
            }

            return $value;
        }, $this->elements);
    }

    /**
     * {@inheritDoc}
     */
    public function toJson(?int $config = null): string
    {
        return ConvertJson::handle($this->jsonSerialize(), $config);
    }

    /**
     * 逐个回调处理.
     */
    public function each(\Closure $callback): void
    {
        foreach ($this->elements as $key => $item) {
            if (false === $callback($item, $key)) {
                break;
            }
        }
    }

    /**
     * 验证类型.
     *
     * @throws \UnexpectedValueException
     */
    protected function checkType(mixed $value, bool $isKey = false, null|int|string $typeIndex = null): void
    {
        $types = $isKey ? $this->keyTypes : $this->valueTypes;
        if (!$types) {
            return;
        }

        if (isset($typeIndex)) {
            if (!isset($types[$typeIndex])) {
                return;
            }

            $types = [$types[$typeIndex]];
        }

        if (These::handle($value, $types)) {
            return;
        }

        throw new \UnexpectedValueException(sprintf(
            'The value%s of a collection %s type requires the following types `%s`.',
            null === $typeIndex ? '' : ' with index '.$typeIndex,
            $isKey ? 'key' : 'value',
            implode(',', $types)
        ));
    }

    /**
     * 转换数据到数组.
     */
    protected function elementsToArray(mixed $elements): array
    {
        if (\is_array($elements)) {
            return $elements;
        }

        if ($elements instanceof self) {
            return $elements->all();
        }

        if ($elements instanceof IArray) {
            return $elements->toArray();
        }

        if ($elements instanceof IJson) {
            return (array) json_decode($elements->toJson(), true, 512, JSON_THROW_ON_ERROR);
        }

        if ($elements instanceof \JsonSerializable) {
            return (array) $elements->jsonSerialize();
        }

        if ($elements instanceof \stdClass) {
            return (array) json_decode(json_encode($elements, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
        }

        return [$elements];
    }

    /**
     * 监听集合元素发生变化.
     */
    protected function watchElements(): void
    {
    }
}
