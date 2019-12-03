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

namespace Leevel\Http;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use Leevel\Support\IArray;
use Leevel\Support\IJson;

/**
 * HTTP Bag.
 *
 * - This class borrows heavily from the Symfony4 Framework and is part of the symfony package.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.02.19
 *
 * @version 1.0
 *
 * @see Symfony\Component\HttpFoundation (https://github.com/symfony/symfony)
 */
class Bag implements IArray, IJson, Countable, IteratorAggregate, JsonSerializable
{
    /**
     * 元素合集.
     *
     * @var array
     */
    protected array $elements = [];

    /**
     * 构造函数.
     */
    public function __construct(array $elements = [])
    {
        $this->elements = $elements;
    }

    /**
     * 魔术方法 __toString.
     */
    public function __toString(): string
    {
        return $this->toJson();
    }

    /**
     * 取回元素.
     */
    public function all(): array
    {
        return $this->elements;
    }

    /**
     * 返回元素键值.
     */
    public function keys(): array
    {
        return array_keys($this->elements);
    }

    /**
     * 替换当前所有元素.
     */
    public function replace(array $elements = []): void
    {
        $this->elements = $elements;
    }

    /**
     * 新增元素.
     */
    public function add(array $elements = []): void
    {
        $this->elements = array_replace($this->elements, $elements);
    }

    /**
     * 取回元素值.
     *
     * @param null|mixed $defaults
     *
     * @return mixed
     */
    public function get(string $key, $defaults = null)
    {
        $key = $this->normalize($key);

        return $this->filter($key, $defaults);
    }

    /**
     * 设置元素值.
     *
     * @param mixed $value
     */
    public function set(string $key, $value): void
    {
        $key = $this->normalize($key);

        $this->elements[$key] = $value;
    }

    /**
     * 判断是否存在元素值.
     */
    public function has(string $key): bool
    {
        $key = $this->normalize($key);

        return array_key_exists($key, $this->elements);
    }

    /**
     * 删除元素值.
     */
    public function remove(string $key): void
    {
        $key = $this->normalize($key);
        if ($this->has($key)) {
            unset($this->elements[$key]);
        }
    }

    /**
     * 获取过滤变量.
     *
     * @param null|mixed            $defaults
     * @param null|array|int|string $filter
     *
     * @return mixed
     */
    public function filter(string $key, $defaults = null, $filter = null, array $options = [])
    {
        $key = $this->normalize($key);
        $filter = $this->parseFilter($filter);
        list($key, $filter) = $this->parseKeyFilter($key, $filter);
        $part = '';
        if (false !== strpos($key, '\\')) {
            list($key, $part) = explode('\\', $key);
        }

        $result = array_key_exists($key, $this->elements) ? $this->elements[$key] : $defaults;

        if ($part) {
            $result = $this->getPartData($part, $result, $defaults);
        }

        if ($filter) {
            $result = $this->filterValue($result, $defaults, $filter, $options);
        }

        return $result;
    }

    /**
     * 实现 Countable::count.
     */
    public function count(): int
    {
        return count($this->elements);
    }

    /**
     * 对象转数组.
     */
    public function toArray(): array
    {
        return $this->elements;
    }

    /**
     * 实现 JsonSerializable::jsonSerialize.
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * 对象转 JSON.
     */
    public function toJson(?int $option = null): string
    {
        if (null === $option) {
            $option = JSON_UNESCAPED_UNICODE;
        }

        return json_encode($this->jsonSerialize(), $option);
    }

    /**
     * 实现 IteratorAggregate::getIterator.
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->elements);
    }

    /**
     * 分析键值和过滤器.
     */
    protected function parseKeyFilter(string $key, array $filter): array
    {
        if (false !== strpos($key, '|')) {
            $temp = explode('|', $key);
            $key = array_shift($temp);
            $filter = array_merge($temp, $filter);
        }

        return [$key, $filter];
    }

    /**
     * 分析过滤器.
     *
     * @param null|array|string $filter
     */
    protected function parseFilter($filter = null): array
    {
        if (null === $filter) {
            return [];
        }

        return is_array($filter) ? $filter : func_get_args();
    }

    /**
     * 过滤值.
     *
     * @param mixed $value
     * @param mixed $defaults
     *
     * @return mixed
     */
    protected function filterValue($value, $defaults, array $filters, array $options = [])
    {
        foreach ($filters as $item) {
            if (is_string($item) && false !== strpos($item, '=')) {
                $value = $this->filterValueWithFunc($value, $item);
            } elseif (is_callable($item)) {
                $value = $this->filterValueWithCallable($value, $item);
            } elseif (is_scalar($value) && !empty($item)) {
                $value = $this->filterValueWithFilterVar($value, $item, $options);
                if (false === $value) {
                    $value = $defaults;

                    break;
                }
            }
        }

        return $value;
    }

    /**
     * 使用函数过滤值.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    protected function filterValueWithFunc($value, string $filter)
    {
        list($filter, $extend) = explode('=', $filter);
        $evals = null;

        if ('default' === $filter) {
            if (!is_numeric($extend) && !preg_match('/^[A-Z\_]+$/', $extend)) {
                $extend = "'".$extend."'";
            }
            $evals = "\$value = '".($value ? '1' : '')."' ?: ".$extend.';';
        } elseif ($extend) {
            if (false !== strpos($extend, ',')) {
                $tmp = explode(',', $extend);
                $result = [];
                foreach ($tmp as $v) {
                    $v = trim($v);
                    if ('**' === $v || is_numeric($v) || preg_match('/^[A-Z\_]+$/', $v)) {
                        $result[] = $v;
                    } else {
                        $result[] = "'".$v."'";
                    }
                }
                $extend = implode(',', $result);
            }

            if (strstr($extend, '**')) {
                $extend = str_replace('**', '$value', $extend);
                $evals = "\$value = {$filter}({$extend});";
            } else {
                $evals = "\$value = {$filter}(\$value, {$extend});";
            }
        }

        eval($evals);

        return $value;
    }

    /**
     * 使用回调过滤值.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    protected function filterValueWithCallable($value, string $filter)
    {
        $value = call_user_func($filter, $value);

        return $value;
    }

    /**
     * 使用 filter_var 过滤值.
     *
     * @param mixed $value
     * @param mixed $filter
     *
     * @see http://php.net/manual/en/function.filter-var.php
     *
     * @return mixed
     */
    protected function filterValueWithFilterVar($value, $filter, array $options)
    {
        $value = filter_var($value, $this->parseFilterId($filter), $options);

        return $value;
    }

    /**
     * 分析转换 filter_var 参数.
     *
     * @param mixed $filter
     */
    protected function parseFilterId($filter): int
    {
        $filter = $this->isInt($filter) ? $filter : filter_id($filter);

        return $filter;
    }

    /**
     * 判断字符串是否为数字.
     *
     * @param mixed $value
     *
     * @since bool
     */
    protected function isInt($value): bool
    {
        if (is_int($value)) {
            return true;
        }

        return ctype_digit((string) $value);
    }

    /**
     * 返回部分数组数据.
     *
     * @param mixed      $value
     * @param null|mixed $defaults
     *
     * @return mixed
     */
    protected function getPartData(string $key, $value, $defaults = null)
    {
        if (!is_array($value)) {
            return $value;
        }

        $parts = explode('.', $key);
        foreach ($parts as $item) {
            if (!is_array($value) || !isset($value[$item])) {
                return $defaults;
            }
            $value = $value[$item];
        }

        return $value;
    }

    /**
     * 格式化键值.
     */
    protected function normalize(string $key): string
    {
        return $key;
    }
}
