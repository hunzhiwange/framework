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

namespace Leevel\Collection;

use ArrayAccess;
use Countable;
use InvalidArgumentException;
use Iterator;
use JsonSerializable;
use Leevel\Support\IArray;
use Leevel\Support\IJson;
use Leevel\Support\TMacro;
use Leevel\Support\Type;

/**
 * 集合.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2016.11.21
 *
 * @version 1.0
 */
class Collection implements IArray, IJson, Iterator, ArrayAccess, Countable, JsonSerializable
{
    use TMacro;

    /**
     * 元素合集.
     *
     * @var array
     */
    protected $elements = [];

    /**
     * 验证
     *
     * @var bool
     */
    protected $valid = true;

    /**
     * 类型.
     *
     * @var mixed
     */
    protected $type = [];

    /**
     * 构造函数.
     *
     * @param mixed $elements
     * @param array $type
     */
    public function __construct($elements = [], array $type = null)
    {
        if ($type) {
            $this->type = $type;
        }

        $elements = $this->getArrayElements($elements);

        if ($this->type) {
            foreach ($elements as $key => $value) {
                $this->offsetSet($key, $value);
            }
        } else {
            $this->elements = $elements;
        }

        unset($elements);
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
    public function __set($key, $value)
    {
        $this->offsetSet($key, $value);
    }

    /**
     * 创建一个集合.
     *
     * @param mixed $elements
     * @param mixed $type
     */
    public static function make($elements = [], $type = null)
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
    public function next()
    {
        $next = next($this->elements);
        $this->valid = false !== $next;
    }

    /**
     * 指针重置.
     */
    public function rewind()
    {
        reset($this->elements);
        $this->valid = true;
    }

    /**
     * 验证
     *
     * @return bool
     */
    public function valid()
    {
        return $this->valid;
    }

    /**
     * 实现 ArrayAccess::offsetExists.
     *
     * @param string $offset
     *
     * @return mixed
     */
    public function offsetExists($offset)
    {
        return isset($this->elements[$offset]);
    }

    /**
     * 实现 ArrayAccess::offsetGet.
     *
     * @param string $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->elements[$offset] ?? null;
    }

    /**
     * 实现 ArrayAccess::offsetSet.
     *
     * @param string $offset
     * @param mixed  $value
     */
    public function offsetSet($offset, $value)
    {
        $this->checkType($value);
        $this->elements[$offset] = $value;
    }

    /**
     * 实现 ArrayAccess::offsetUnset.
     *
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
        if (isset($this->elements[$offset])) {
            unset($this->elements[$offset]);
        }
    }

    /**
     * 统计元素数量 count($obj).
     *
     * @return int
     */
    public function count()
    {
        return count($this->elements);
    }

    /**
     * 返回所有元素.
     *
     * @return array
     */
    public function all()
    {
        return $this->elements;
    }

    /**
     * 对象转数组.
     *
     * @return array
     */
    public function toArray()
    {
        return array_map(function ($value) {
            return $value instanceof IArray ? $value->toArray() : $value;
        }, $this->elements);
    }

    /**
     * 实现 JsonSerializable::jsonSerialize.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return array_map(function ($value) {
            if ($value instanceof JsonSerializable) {
                return $value->jsonSerialize();
            }
            if ($value instanceof IJson) {
                return json_decode($value->toJson(), true);
            }
            if ($value instanceof IArray) {
                return $value->toArray();
            }

            return $value;
        }, $this->elements);
    }

    /**
     * 对象转 JSON.
     *
     * @param int $option
     *
     * @return string
     */
    public function toJson($option = JSON_UNESCAPED_UNICODE)
    {
        return json_encode($this->jsonSerialize(), $option);
    }

    /**
     * JQuery.each.
     *
     * @param callable $callback
     *
     * @return $this
     */
    public function each(callable $callback)
    {
        foreach ($this->elements as $key => $item) {
            if (false === $callback($item, $key)) {
                break;
            }
        }

        return $this;
    }

    /**
     * JQuery.prev.
     *
     * @return mixed
     */
    public function prev()
    {
        $prev = prev($this->elements);

        $this->valid = true;

        return $prev;
    }

    /**
     * JQuery.end.
     *
     * @return mixed
     */
    public function end()
    {
        $end = end($this->elements);

        $this->valid = false;

        return $end;
    }

    /**
     * JQuery.siblings.
     *
     * @param mixed $key
     *
     * @return array
     */
    public function siblings($key = null)
    {
        $result = [];

        $key = $this->parseKey($key);

        foreach ($this->elements as $k => $value) {
            if ($k === $key) {
                continue;
            }

            $result[$k] = $value;
        }

        return $result;
    }

    /**
     * JQuery.nextAll.
     *
     * @param mixed $key
     *
     * @return array
     */
    public function nextAll($key = null)
    {
        $result = [];

        $key = $this->parseKey($key);
        $current = false;

        foreach ($this->elements as $k => $value) {
            if (false === $current) {
                if ($k === $key) {
                    $current = true;
                }

                continue;
            }
            $result[$k] = $value;
        }

        return $result;
    }

    /**
     * JQuery.prevAll.
     *
     * @param mixed $key
     *
     * @return array
     */
    public function prevAll($key = null)
    {
        $result = [];

        $key = $this->parseKey($key);
        $current = false;

        foreach ($this->elements as $k => $value) {
            if ($k === $key) {
                $current = true;

                break;
            }
            $result[$k] = $value;
        }

        return $result;
    }

    /**
     * JQuery.attr.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return mixed|void
     */
    public function attr($key, $value = null)
    {
        if (null === $value) {
            return $this->offsetGet($key);
        }
        $this->offsetSet($key, $value);
    }

    /**
     * JQuery.eq.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function eq($key)
    {
        return $this->offsetGet($key);
    }

    /**
     * JQuery.get.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get($key)
    {
        return $this->offsetGet($key);
    }

    /**
     * JQuery.index.
     *
     * @param mixed $value
     * @param bool  $strict
     *
     * @return mixed
     */
    public function index($value = null, bool $strict = true)
    {
        if (null === $value) {
            return $this->key();
        }
        $key = array_search($value, $this->elements, $strict);

        if (false === $key) {
            return;
        }

        return $key;
    }

    /**
     * JQuery.find.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function find($key)
    {
        return $this->offsetGet($key);
    }

    /**
     * JQuery.first.
     *
     * @return mixed
     */
    public function first()
    {
        $this->rewind();

        return $this->current();
    }

    /**
     * JQuery.last.
     *
     * @return mixed
     */
    public function last()
    {
        return $this->end();
    }

    /**
     * JQuery.is.
     *
     * @param string $key
     *
     * @return bool
     */
    public function is($key)
    {
        return $this->offsetExists($key);
    }

    /**
     * JQuery.slice.
     *
     * @param int $selector
     * @param int $length
     *
     * @return array
     */
    public function slice($selector, $length = null)
    {
        return array_slice($this->elements, $selector, $length);
    }

    /**
     * JQuery.not.
     *
     * @param string $key
     *
     * @return array
     */
    public function not($key)
    {
        return $this->siblings($key);
    }

    /**
     * JQuery.filter.
     *
     * @param string $key
     *
     * @return array
     */
    public function filter($key)
    {
        return $this->siblings($key);
    }

    /**
     * jquer.size.
     *
     * @return int
     */
    public function size()
    {
        return $this->count();
    }

    /**
     * 是否为空.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->elements);
    }

    /**
     * 数据 map.
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return array
     */
    public function map($key, $value = null)
    {
        return array_column($this->elements, $key, $value);
    }

    /**
     * 验证类型.
     *
     * @param mixed $value
     */
    protected function checkType($value)
    {
        if (!$this->type) {
            return;
        }

        if (Type::these($value, $this->type)) {
            return;
        }

        throw new InvalidArgumentException(sprintf('Collection type %s validation failed', implode(',', $this->type)));
    }

    /**
     * 转换数据到数组.
     *
     * @param mixed $elements
     *
     * @return array
     */
    protected function getArrayElements($elements)
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
            return json_decode($elements->toJson(), true);
        }
        if ($elements instanceof JsonSerializable) {
            return $elements->jsonSerialize();
        }

        return (array) $elements;
    }

    /**
     * 分析 key.
     *
     * @param mixed $key
     *
     * @return mixed
     */
    protected function parseKey($key = null)
    {
        if (null === $key) {
            $key = $this->key();
        }

        return $key;
    }
}
