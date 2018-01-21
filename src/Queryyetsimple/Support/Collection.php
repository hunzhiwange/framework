<?php
/*
 * This file is part of the ************************ package.
 * ##########################################################
 * #   ____                          ______  _   _ ______   #
 * #  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
 * # |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
 * #  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
 * #       \__   | \___ |_|    \__  || |    | | | || |      #
 * #     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
 * #                          |___ /  Since 2010.10.03      #
 * ##########################################################
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Queryyetsimple\Support;

use Iterator;
use Countable;
use ArrayAccess;
use JsonSerializable;
use BadMethodCallException;
use InvalidArgumentException;

/**
 * 数组转对象集合
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2016.11.21
 * @version 1.0
 */
class Collection implements IArray, IJson, Iterator, ArrayAccess, Countable, JsonSerializable
{
    use Macro;

    /**
     * 元素合集
     *
     * @var array
     */
    protected $objects = [];

    /**
     * 验证
     *
     * @var boolean
     */
    protected $valid = true;

    /**
     * 类型
     *
     * @var string
     */
    protected $type = '';

    /**
     * 构造函数
     *
     * @param array $objects
     * @return void
     */
    public function __construct(array $objects = [], $type = '')
    {
        $this->type = $type;
        foreach ($objects as $offset => $item) {
            $this[$offset] = $item;
        }
        return $this;
    }

    /**
     * 当前元素
     *
     * @return mixed
     */
    public function current()
    {
        return current($this->objects);
    }

    /**
     * 当前 key
     *
     * @return mixed
     */
    public function key()
    {
        return key($this->objects);
    }

    /**
     * 下一个元素
     *
     * @return void
     */
    public function next()
    {
        $next = next($this->objects);
        $this->valid = $next !== false;
    }

    /**
     * 指针重置
     *
     * @return void
     */
    public function rewind()
    {
        reset($this->objects);
        $this->valid = true;
    }

    /**
     * 验证
     *
     * @return boolean
     */
    public function valid()
    {
        return $this->valid;
    }

    /**
     * 实现 isset( $obj['hello'] )
     *
     * @param string $strKey
     * @return mixed
     */
    public function offsetExists($strKey)
    {
        return isset($this->objects[$strKey]);
    }

    /**
     * 实现 $strHello = $obj['hello']
     *
     * @param string $strKey
     * @return mixed
     */
    public function offsetGet($strKey)
    {
        return $this->objects[$strKey] ?? null;
    }

    /**
     * 实现 $obj['hello'] = 'world'
     *
     * @param string $strKey
     * @param mixed $mixValue
     * @return void
     */
    public function offsetSet($strKey, $mixValue)
    {
        $this->checkType($mixValue);
        $this->objects[$strKey] = $mixValue;
    }

    /**
     * 实现 unset($obj['hello'])
     *
     * @param string $strKey
     * @return void
     */
    public function offsetUnset($strKey)
    {
        if (isset($this->objects[$strKey])) {
            unset($this->objects[$strKey]);
        }
    }

    /**
     * 统计元素数量 count($obj)
     *
     * @return int
     */
    public function count()
    {
        return count($this->objects);
    }

    /**
     * 对象转数组
     *
     * @return array
     */
    public function toArray()
    {
        return array_map(function ($mixValue) {
            return $mixValue instanceof IArray ? $mixValue->toArray() : $mixValue;
        }, $this->objects);
    }

    /**
     * 实现 JsonSerializable::jsonSerialize
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return array_map(function ($mixValue) {
            if ($mixValue instanceof JsonSerializable) {
                return $mixValue->jsonSerialize();
            } elseif ($mixValue instanceof IJson) {
                return json_decode($mixValue->toJson(), true);
            } elseif ($mixValue instanceof IArray) {
                return $mixValue->toArray();
            } else {
                return $mixValue;
            }
        }, $this->objects);
    }

    /**
     * 对象转 JSON
     *
     * @param integer $option
     * @return string
     */
    public function toJson($option = JSON_UNESCAPED_UNICODE)
    {
        return json_encode($this->jsonSerialize(), $option);
    }

    /**
     * jquery.each
     *
     * @return void
     */
    public function each()
    {
        if (is_callable($args[0])) {
            throw new InvalidArgumentException('The first args of each must be callable.');
        }

        $args = func_get_args();
        if (! empty($args[1]) && is_string($args[1])) {
            $sKeyName = $args[1];
        } else {
            $sKeyName = 'key';
        }

        $objects = $this->objects;
        foreach ($objects as $key => $val) {
            if (is_array($val)) {
                $arrData = $val;
                $arrData[$sKeyName] = $key;
            } else {
                $arrData = [
                    $sKeyName => $key,
                    'value' => $val
                ];
            }
            $args[0](new static($arrData));
        }
    }

    /**
     * jquery.prev
     *
     * @return mixed
     */
    public function prev()
    {
        $mixPrev = prev($this->objects);
        $this->valid = $mixPrev !== false;
        return $mixPrev;
    }

    /**
     * jquery.end
     *
     * @return mixed
     */
    public function end()
    {
        $mixEnd = end($this->objects);
        $this->valid = $mixEnd !== false;
        return $mixEnd;
    }

    /**
     * jquery.siblings
     *
     * @param mixed $mixCurrentKey
     * @return array
     */
    public function siblings($mixCurrentKey = null)
    {
        $arrSiblings = [];
        $mixCurrentKey === null && $mixCurrentKey = $this->key();
        if (! is_array($mixCurrentKey)) {
            $mixCurrentKey = ( array ) $mixCurrentKey;
        }
        $objects = $this->objects;
        foreach ($objects as $sKey => $mixVal) {
            if (in_array($sKey, $mixCurrentKey)) {
                continue;
            }
            $arrSiblings[$sKey] = $mixVal;
        }
        unset($objects);
        return $arrSiblings;
    }

    /**
     * jquery.nextAll
     *
     * @param mixed $mixCurrentKey
     * @return array
     */
    public function nextAll($mixCurrentKey = null)
    {
        $arrNexts = [];
        $mixCurrentKey === null && $mixCurrentKey = $this->key();
        $objects = $this->objects;
        $booCurrent = false;
        foreach ($objects as $sKey => $mixVal) {
            if ($booCurrent === false) {
                if ($mixCurrentKey === $sKey) {
                    $booCurrent = true;
                }
                continue;
            }
            $arrNexts[$sKey] = $mixVal;
        }
        unset($objects);
        return $arrNexts;
    }

    /**
     * jquery.prevAll
     *
     * @param mixed $mixCurrentKey
     * @return array
     */
    public function prevAll($mixCurrentKey = null)
    {
        $arrPrevs = [];
        $mixCurrentKey === null && $mixCurrentKey = $this->key();
        $objects = $this->objects;
        $booCurrent = false;
        foreach ($objects as $sKey => $mixVal) {
            if ($mixCurrentKey === $sKey) {
                $booCurrent = true;
                break;
            }
            $arrPrevs[$sKey] = $mixVal;
        }
        unset($objects);
        return $arrPrevs;
    }

    /**
     * jquery.attr
     *
     * @param string $sKey
     * @param mixed $mixValue
     * @return void|mixed
     */
    public function attr($sKey, $mixValue = null)
    {
        if ($mixValue === null) {
            return $this->__get($sKey);
        } else {
            $this->__set($sKey, $mixValue);
        }
    }

    /**
     * jquery.eq
     *
     * @param string $sKey
     * @return mixed
     */
    public function eq($sKey)
    {
        return $this->offsetGet($sKey);
    }

    /**
     * jquery.get
     *
     * @param string $sKey
     * @return mixed
     */
    public function get($sKey)
    {
        return $this->offsetGet($sKey);
    }

    /**
     * jquery.index
     *
     * @param mixed $mixValue
     * @return mixed
     */
    public function index($mixValue = null)
    {
        if ($mixValue === null) {
            return $this->key();
        } else {
            $sKey = array_search($mixValue, $this->objects);
            if ($sKey === false) {
                return null;
            }
            return $sKey;
        }
    }

    /**
     * jquery.find
     *
     * @param string $sKey
     * @return mixed
     */
    public function find($sKey)
    {
        return $this->offsetGet($sKey);
    }

    /**
     * jquery.first
     *
     * @return mixed
     */
    public function first()
    {
        return $this->rewind();
    }

    /**
     * jquery.last
     *
     * @return mixed
     */
    public function last()
    {
        return $this->end();
    }
    /**
     * jquery.is
     *
     * @param string $sKey
     * @return boolean
     */
    public function is($sKey)
    {
        return $this->offsetExists($sKey);
    }

    /**
     * jquery.slice
     *
     * @param int $nSelector
     * @param string $nEnd
     * @return array
     */
    public function slice($nSelector, $nEnd = null)
    {
        if ($nEnd === null) {
            return array_slice($this->objects, $nSelector);
        } else {
            return array_slice($this->objects, $nSelector, $nEnd);
        }
    }

    /**
     * jquery.not
     *
     * @param string $sKey
     * @return array
     */
    public function not($sKey)
    {
        return $this->siblings($sKey);
    }

    /**
     * jquery.filter
     *
     * @param string $sKey
     * @return array
     */
    public function filter($sKey)
    {
        return $this->siblings($sKey);
    }

    /**
     * jquer.size
     *
     * @return int
     */
    public function size()
    {
        return $this->count();
    }

    /**
     * 是否为空
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return empty($this->objects);
    }

    /**
     * 数据 map
     *
     * @param string $sKeyName
     * @param mixed $mixValueName
     * @return array
     */
    public function map($sKeyName, $mixValueName = null)
    {
        if ($mixValueName === null) {
            return array_column($this->objects, null, $sKeyName);
        } elseif ($mixValueName === true) {
            return array_column($this->objects, $sKeyName);
        } else {
            return array_column($this->objects, $mixValueName, $sKeyName);
        }
    }

    /**
     * 验证类型
     *
     * @param mixed $mixObject
     * @return void
     */
    protected function checkType($mixObject)
    {
        if (! $this->type) {
            return;
        }

        if (is_object($mixObject)) {
            if ($mixObject instanceof $this->type) {
                return;
            }
            $type = get_class($mixObject);
        } else {
            $type = gettype($mixObject);
        }

        if ($type == $this->type) {
            return;
        }

        throw new InvalidArgumentException(sprintf('Collection type %s validation failed', $type));
    }

    /**
     * call 
     *
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function __call(string $method, array $args)
    {
        throw new BadMethodCallException(sprintf('Collection method %s is not defined.', $method));
    }

    /**
     * __get 魔术方法
     *
     * @param string $strKey
     * @return mixed
     */
    public function __get($strKey)
    {
        if (array_key_exists($strKey, $this->objects)) {
            return $this->objects[$strKey];
        } else {
            return null;
        }
    }

    /**
     * __set 魔术方法
     *
     * @param string $sKey
     * @param mixed $mixVal
     * @return mixed
     */
    public function __set($sKey, $mixVal)
    {
        $this->checkType($mixVal);
        $mixOld = $this->__get($sKey);
        $this->objects[$sKey] = $mixVal;
        return $mixOld;
    }
}
