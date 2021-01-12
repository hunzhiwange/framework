<?php

declare(strict_types=1);

namespace Leevel\Collection;

use ArrayAccess;
use ArrayIterator;
use Closure;
use Countable;
use UnexpectedValueException;
use IteratorAggregate;
use JsonSerializable;
use function Leevel\Support\Arr\convert_json;
use Leevel\Support\Arr\convert_json;
use Leevel\Support\IArray;
use Leevel\Support\IJson;
use function Leevel\Support\Type\these;
use Leevel\Support\Type\these;
use stdClass;

/**
 * 集合.
 */
class Collection implements IArray, IJson, IteratorAggregate, ArrayAccess, Countable, JsonSerializable
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

        if ($this->valueTypes || $this->keyTypes) {
            foreach ($elements as $key => $value) {
                $this->offsetSet($key, $value);
            }
        } else {
            $this->elements = $elements;
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
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->elements);
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
    }

    /**
     * {@inheritDoc}
     */
    public function offsetUnset(mixed $index): void
    {
        if (isset($this->elements[$index])) {
            unset($this->elements[$index]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        return count($this->elements);
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
     * 
     * @todo 修复一下 toArray
     */
    public function toArray(): array
    {
        $args = func_get_args();

        return array_map(function ($value) use ($args) {
            return $value instanceof IArray ? $value->toArray(...$args) : $value;
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

            if ($value instanceof JsonSerializable) {
                return $value->jsonSerialize();
            }

            if ($value instanceof stdClass) {
                return json_decode(json_encode($value), true);
            }

            return $value;
        }, $this->elements);
    }

    /**
     * {@inheritDoc}
     */
    public function toJson(?int $option = null): string
    {
        return convert_json($this->jsonSerialize(), $option);
    }

    /**
     * 逐个回调处理.
     */
    public function each(Closure $callback): void
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
    protected function checkType(mixed $value, bool $isKey = false): void
    {
        $types = $isKey ? $this->keyTypes : $this->valueTypes;
        if (!$types) {
            return;
        }

        if (these($value, $types)) {
            return;
        }

        $e = sprintf('The value of a collection %s type requires the following types `%s`.', $isKey ? 'key' : 'value', implode(',', $types));

        throw new UnexpectedValueException($e);
    }

    /**
     * 转换数据到数组.
     */
    protected function elementsToArray(mixed $elements): array
    {
        if (is_array($elements)) {
            return $elements;
        }

        if ($elements instanceof self) {
            return $elements->all();
        }

        if ($elements instanceof IArray) {
            return $elements->toArray();
        }

        if ($elements instanceof IJson) {
            return json_decode($elements->toJson(), true, 512, JSON_THROW_ON_ERROR);
        }

        if ($elements instanceof JsonSerializable) {
            return $elements->jsonSerialize();
        }

        if ($elements instanceof stdClass) {
            return json_decode(json_encode($elements), true);
        }

        return [$elements];
    }
}

// import fn.
class_exists(these::class);
class_exists(convert_json::class);
