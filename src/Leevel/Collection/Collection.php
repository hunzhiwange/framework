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

namespace Leevel\Collection;

use ArrayAccess;
use ArrayIterator;
use Closure;
use Countable;
use InvalidArgumentException;
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
     *
     * @var array
     */
    protected array $elements = [];

    /**
     * 验证.
     *
     * @var bool
     */
    protected bool $valid = true;

    /**
     * 类型.
     *
     * @var array
     */
    protected ?array $type = [];

    /**
     * 构造函数.
     *
     * @param mixed $elements
     */
    public function __construct($elements = [], ?array $type = null)
    {
        if ($type) {
            $this->type = $type;
        }

        $elements = $this->elementsToArray($elements);

        if ($this->type) {
            foreach ($elements as $key => $value) {
                $this->offsetSet($key, $value);
            }
        } else {
            $this->elements = $elements;
        }
    }

    /**
     * __toString 魔术方法.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * __get 魔术方法.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->offsetGet($key);
    }

    /**
     * __set 魔术方法.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function __set($key, $value): void
    {
        $this->offsetSet($key, $value);
    }

    /**
     * 创建一个集合.
     *
     * @param mixed      $elements
     * @param null|mixed $type
     *
     * @return \Leevel\Collection\Collection
     */
    public static function make($elements = [], $type = null): self
    {
        return new static($elements, $type);
    }

    /**
     * 当前元素.
     *
     * @return mixed
     */
    public function current()
    {
        return current($this->elements);
    }

    /**
     * 当前 key.
     *
     * @return mixed
     */
    public function key()
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
     * 实现 IteratorAggregate::getIterator.
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->elements);
    }

    /**
     * 实现 ArrayAccess::offsetExists.
     *
     * @param mixed $index
     */
    public function offsetExists($index): bool
    {
        return isset($this->elements[$index]);
    }

    /**
     * 实现 ArrayAccess::offsetGet.
     *
     * @param string $index
     *
     * @return mixed
     */
    public function offsetGet($index)
    {
        return $this->elements[$index] ?? null;
    }

    /**
     * 实现 ArrayAccess::offsetSet.
     *
     * @param mixed $index
     * @param mixed $newval
     */
    public function offsetSet($index, $newval): void
    {
        $this->checkType($newval);
        $this->elements[$index] = $newval;
    }

    /**
     * 实现 ArrayAccess::offsetUnset.
     *
     * @param mixed $index
     */
    public function offsetUnset($index): void
    {
        if (isset($this->elements[$index])) {
            unset($this->elements[$index]);
        }
    }

    /**
     * 统计元素数量 count($obj).
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
     * 对象转数组.
     */
    public function toArray(): array
    {
        $args = func_get_args();

        return array_map(function ($value) use ($args) {
            return $value instanceof IArray ? $value->toArray(...$args) : $value;
        }, $this->elements);
    }

    /**
     * 实现 JsonSerializable::jsonSerialize.
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
     * 对象转 JSON.
     */
    public function toJson(?int $option = null): string
    {
        return convert_json($this->jsonSerialize(), $option);
    }

    /**
     * each.
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
     * @param mixed $value
     *
     * @throws \InvalidArgumentException
     */
    protected function checkType($value): void
    {
        if (!$this->type) {
            return;
        }

        if (these($value, $this->type)) {
            return;
        }

        $e = sprintf('Collection type %s validation failed.', implode(',', $this->type));

        throw new InvalidArgumentException($e);
    }

    /**
     * 转换数据到数组.
     *
     * @param mixed $elements
     */
    protected function elementsToArray($elements): array
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
