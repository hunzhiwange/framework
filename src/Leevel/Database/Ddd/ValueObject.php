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

namespace Leevel\Database\Ddd;

use ArrayAccess;
use BadMethodCallException;
use JsonSerializable;
use Leevel\Flow\TControl;
use Leevel\Support\IArray;
use Leevel\Support\IJson;
use Leevel\Support\Str;
use Leevel\Support\TSerialize;

/**
 * 值对象
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.09.17
 *
 * @version 1.0
 */
class ValueObject implements IArray, IJson, JsonSerializable, ArrayAccess
{
    use TSerialize;
    use TControl;

    /**
     * 值对象数据.
     *
     * @var array
     */
    protected $datas = [];

    /**
     * 值对象源数据.
     *
     * @var array
     */
    protected $sourceDatas = [];

    /**
     * 转换隐藏的属性.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * 转换显示的属性.
     *
     * @var array
     */
    protected $visible = [];

    /**
     * 追加.
     *
     * @var array
     */
    protected $append = [];

    /**
     * 缓存驼峰法命名属性到下划线
     *
     * @var array
     */
    protected static $unCamelize = [];

    /**
     * 构造函数.
     *
     * @param array $datas
     * @param array $sourceDatas
     */
    public function __construct($datas = [], $sourceDatas = [])
    {
        $this->datas = $datas;
        $this->sourceDatas = $sourceDatas;
    }

    /**
     * 魔术方法获取.
     *
     * @param string $name
     * @param mixed  $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getData($this->getUnCamelizeKey($key));
    }

    /**
     * 强制更新属性值
     *
     * @param string $name
     * @param mixed  $value
     * @param mixed  $key
     *
     * @return $this
     */
    public function __set($key, $value)
    {
        return $this->set($this->getUnCamelizeKey($key), $value);
    }

    /**
     * 是否存在属性.
     *
     * @param string $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return $this->has($this->getUnCamelizeKey($name));
    }

    /**
     * 删除属性.
     *
     * @param string $name
     *
     * @return bool
     */
    public function __unset($name)
    {
        return $this->delete($this->getUnCamelizeKey($name));
    }

    /**
     * call.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public function __call(string $method, array $args)
    {
        if ($this->placeholderTControl($method)) {
            return $this;
        }

        switch (true) {
            case 'get' === substr($method, 0, 3):
                return $this->__get(substr($method, 3), $args[0] ?? null);
            case 'set' === substr($method, 0, 3):
                return $this->__set(substr($method, 3), $args[0] ?? null);
            case 'delete' === substr($method, 0, 5):
                return $this->__unset(substr($method, 5));
            case 'has' === substr($method, 0, 3):
                return $this->__isset(substr($method, 3));
        }

        throw new BadMethodCallException(
            sprintf('Method %s is not exits.', $method)
        );
    }

    /**
     * 设置值对象
     *
     * @param string $name
     * @param mxied  $value
     */
    public function set($name, $value)
    {
        $this->datas[$name] = $value;
    }

    /**
     * 批量插入.
     *
     * @param array|string $keys
     * @param mixed        $value
     */
    public function put($keys, $value = null)
    {
        if (!is_array($keys)) {
            $keys = [
                $keys => $value,
            ];
        }

        foreach ($keys as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * 数组插入数据.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function push($key, $value)
    {
        $arr = $this->get($key, []);

        $arr[] = $value;

        $this->set($key, $arr);
    }

    /**
     * 合并元素.
     *
     * @param string $key
     * @param array  $value
     */
    public function merge($key, array $value)
    {
        $this->set(
            $key,
            array_unique(array_merge($this->get($key, []), $value))
        );
    }

    /**
     * 弹出元素.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function pop($key, array $value)
    {
        $this->set(
            $key,
            array_diff($this->get($key, []), $value)
        );
    }

    /**
     * 数组插入键值对数据.
     *
     * @param string $key
     * @param mixed  $keys
     * @param mixed  $value
     */
    public function arrays($key, $keys, $value = null)
    {
        $arr = $this->get($key, []);

        if (is_string($keys)) {
            $arr[$keys] = $value;
        } elseif (is_array($keys)) {
            $arr = array_merge($arr, $keys);
        }

        $this->set($key, $arr);
    }

    /**
     * 数组键值删除数据.
     *
     * @param string $key
     * @param mixed  $keys
     */
    public function arraysDelete($key, $keys)
    {
        $arr = $this->get($key, []);

        if (!is_array($keys)) {
            $keys = [
                $keys,
            ];
        }

        foreach ($keys as $value) {
            if (isset($arr[$value])) {
                unset($arr[$value]);
            }
        }

        $this->set($key, $arr);
    }

    /**
     * 取回值对象
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return mxied
     */
    public function get($name, $value = null)
    {
        return $this->datas[$name] ?? $value;
    }

    /**
     * 删除值对象
     *
     * @param string $name
     *
     * @return bool
     */
    public function delete($name)
    {
        if (isset($this->datas[$name])) {
            unset($this->datas[$name]);
        }

        return true;
    }

    /**
     * 是否存在值对象
     *
     * @param string $name
     *
     * @return bool
     */
    public function has($name)
    {
        return isset($this->datas[$name]);
    }

    /**
     * 清理所有值对象数据.
     */
    public function clear()
    {
        $this->datas = [];
    }

    /**
     * 数据是否为空.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->datas);
    }

    /**
     * 取回值对象数据.
     *
     * @param string $name
     * @param mixed  $defaults
     *
     * @return mixed
     */
    public function getData($name, $defaults = null)
    {
        if (false !== strpos($name, '\\')) {
            return $this->getPartData($name, $defaults);
        }

        return $this->get($name, $defaults);
    }

    /**
     * 批量取回值对象数据.
     *
     * @param array $names
     * @param mixed $defaults
     *
     * @return array
     */
    public function getDatas(array $names, $defaults)
    {
        $arr = [];

        foreach ($names as $name) {
            $arr[$name] = $this->getData($name, $defaults);
        }

        return $arr;
    }

    /**
     * 取回值对象所有数据.
     *
     * @return array
     */
    public function getDataAll()
    {
        return $this->datas;
    }

    /**
     * 设置值对象源数据.
     *
     * @param string $name
     * @param mxied  $value
     */
    public function setSource($name, $value)
    {
        $this->sourceDatas[$name] = $value;
    }

    /**
     * 批量插入源数据.
     *
     * @param array|string $keys
     * @param mixed        $value
     */
    public function putSource($keys, $value = null)
    {
        if (!is_array($keys)) {
            $keys = [
                $keys => $value,
            ];
        }

        foreach ($keys as $key => $value) {
            $this->setSource($key, $value);
        }
    }

    /**
     * 数组插入源数据.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function pushSource($key, $value)
    {
        $arr = $this->getSource($key, []);

        $arr[] = $value;

        $this->setSource($key, $arr);
    }

    /**
     * 合并源数据元素.
     *
     * @param string $key
     * @param array  $value
     */
    public function mergeSource($key, array $value)
    {
        $this->setSource(
            $key,
            array_unique(array_merge($this->getSource($key, []), $value))
        );
    }

    /**
     * 弹出源数据元素.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function popSource($key, array $value)
    {
        $this->setSource(
            $key,
            array_diff($this->getSource($key, []), $value)
        );
    }

    /**
     * 数组插入键值对源数据.
     *
     * @param string $key
     * @param mixed  $keys
     * @param mixed  $value
     */
    public function arraysSource($key, $keys, $value = null)
    {
        $arr = $this->getSource($key, []);

        if (is_string($keys)) {
            $arr[$keys] = $value;
        } elseif (is_array($keys)) {
            $arr = array_merge($arr, $keys);
        }

        $this->setSource($key, $arr);
    }

    /**
     * 数组键值删除源数据.
     *
     * @param string $key
     * @param mixed  $keys
     */
    public function arraysDeleteSource($key, $keys)
    {
        $arr = $this->getSource($key, []);

        if (!is_array($keys)) {
            $keys = [
                $keys,
            ];
        }

        foreach ($keys as $value) {
            if (isset($arr[$value])) {
                unset($arr[$value]);
            }
        }

        $this->setSource($key, $arr);
    }

    /**
     * 取回源数据值对象
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return mxied
     */
    public function getSource($name, $value = null)
    {
        return $this->sourceDatas[$name] ?? $value;
    }

    /**
     * 删除值对象源数据.
     *
     * @param string $name
     *
     * @return bool
     */
    public function deleteSource($name)
    {
        if (isset($this->sourceDatas[$name])) {
            unset($this->sourceDatas[$name]);
        }

        return true;
    }

    /**
     * 是否存在值对象源数据.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasSource($name)
    {
        return isset($this->sourceDatas[$name]);
    }

    /**
     * 清理所有值对象源数据.
     */
    public function clearSource()
    {
        $this->sourceDatas = [];
    }

    /**
     * 源数据是否为空.
     *
     * @return bool
     */
    public function isEmptySource()
    {
        return empty($this->sourceDatas);
    }

    /**
     * 取回值对象源数据.
     *
     * @param string           $name
     * @param int|mixed|string $defaults
     *
     * @return mixed
     */
    public function getSourceData($name, $defaults = null)
    {
        if (false !== strpos($name, '\\')) {
            return $this->getSourcePartData($name, $defaults);
        }

        return $this->getSource($name, $defaults);
    }

    /**
     * 批量取回值对象源数据.
     *
     * @param array $names
     * @param mixed $defaults
     *
     * @return array
     */
    public function getSourceDatas(array $names, $defaults)
    {
        $arr = [];

        foreach ($names as $name) {
            $arr[$name] = $this->getSourceData($name, $defaults);
        }

        return $arr;
    }

    /**
     * 取回值对象所有源数据.
     *
     * @return array
     */
    public function getSourceDataAll()
    {
        return $this->sourceDatas;
    }

    /**
     * 比较数据是否变更.
     *
     * @param string $name
     * @param bool   $strict
     *
     * @return bool
     */
    public function hasChange($name, $strict = false)
    {
        $newData = $this->getData($name);
        $sourceData = $this->getSourceData($name);

        return (false === $strict && $newData !== $sourceData) ||
            (true === $strict && $newData !== $sourceData);
    }

    /**
     * 批量比较数据是否变更.
     *
     * @param array $names
     * @param bool  $strict
     *
     * @return bool
     */
    public function hasChanges($names, $strict = false)
    {
        $arr = [];

        foreach ($names as $name) {
            $arr[$name] = $this->hasChange($name, $strict);
        }

        return $arr;
    }

    /**
     * 依据新数据比较数据是否变更.
     *
     * @param bool $strict
     *
     * @return array
     */
    public function hasChangeNew($strict = false)
    {
        $arr = [];

        foreach (array_keys($this->datas) as $name) {
            $arr[$name] = $this->hasChange($name, $strict);
        }

        return $arr;
    }

    /**
     * 依据源数据比较数据是否变更.
     *
     * @param bool $strict
     *
     * @return array
     */
    public function hasChangeSource($strict = false)
    {
        $arr = [];

        foreach (array_keys($this->sourceDatas) as $name) {
            $arr[$name] = $this->hasChange($name, $strict);
        }

        return $arr;
    }

    /**
     * 设置转换隐藏属性.
     *
     * @param array $hidden
     *
     * @return $this
     */
    public function hidden(array $hidden)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->hidden = $hidden;

        return $this;
    }

    /**
     * 获取转换隐藏属性.
     *
     * @return array
     */
    public function getHidden()
    {
        return $this->hidden;
    }

    /**
     * 添加转换隐藏属性.
     *
     * @param array|string $props
     *
     * @return $this
     */
    public function addHidden($props)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $props = is_array($props) ? $props : func_get_args();

        $this->hidden = array_merge($this->hidden, $props);

        return $this;
    }

    /**
     * 设置转换显示属性.
     *
     * @param array $visible
     *
     * @return $this
     */
    public function visible(array $visible)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->visible = $visible;

        return $this;
    }

    /**
     * 获取转换显示属性.
     *
     * @return array
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * 添加转换显示属性.
     *
     * @param array|string $props
     *
     * @return $this
     */
    public function addVisible($props)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $props = is_array($props) ? $props : func_get_args();

        $this->visible = array_merge($this->visible, $props);

        return $this;
    }

    /**
     * 设置转换追加属性.
     *
     * @param array $append
     *
     * @return $this
     */
    public function append(array $append)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->append = $append;

        return $this;
    }

    /**
     * 获取转换追加属性.
     *
     * @return array
     */
    public function getAppend()
    {
        return $this->append;
    }

    /**
     * 添加转换追加属性.
     *
     * @param null|array|string $props
     *
     * @return $this
     */
    public function addAppend($props = null)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $props = is_array($props) ? $props : func_get_args();

        $this->append = array_merge($this->append, $props);

        return $this;
    }

    /**
     * 实现 ArrayAccess::offsetExists.
     *
     * @param string $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * 实现 ArrayAccess::offsetSet.
     *
     * @param string $offset
     * @param mixed  $value
     *
     * @return $this
     */
    public function offsetSet($offset, $value)
    {
        return $this->set($offset, $value);
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
        return $this->get($offset);
    }

    /**
     * 实现 ArrayAccess::offsetUnset.
     *
     * @param string $offset
     *
     * @return $this
     */
    public function offsetUnset($offset)
    {
        return $this->delete($offset);
    }

    /**
     * 对象转数组.
     *
     * @return array
     */
    public function toArray()
    {
        $args = func_get_args();

        if ($args) {
            $visible = array_unique(
                array_merge(
                    $this->visible,
                    is_array($args[0]) ? $args[0] : $args
                )
            );
        } else {
            $visible = $this->visible;
        }

        if (!empty($visible)) {
            $datas = array_intersect_key(
                $this->datas,
                array_flip($visible)
            );
        } elseif (!empty($this->hidden)) {
            $datas = array_diff_key(
                $this->datas,
                array_flip($this->hidden)
            );
        } else {
            $datas = $this->datas;
        }

        $datas = array_merge(
            $datas,
            $this->append ? array_flip($this->append) : []
        );

        foreach ($datas as $key => &$value) {
            $value = $this->getData($key);
        }

        return $datas;
    }

    /**
     * 实现 JsonSerializable::jsonSerialize.
     *
     * @return bool
     */
    public function jsonSerialize()
    {
        return $this->toArray();
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
        return json_encode($this->toArray(), $option);
    }

    /**
     * 返回部分闪存数据.
     *
     * @param string $name
     * @param mixed  $defaults
     *
     * @return mixed
     */
    protected function getPartData($name, $defaults = null)
    {
        return $this->getTypePartData($name, $defaults);
    }

    /**
     * 返回部分闪存源数据.
     *
     * @param string $name
     * @param mixed  $defaults
     *
     * @return mixed
     */
    protected function getSourcePartData($name, $defaults = null)
    {
        return $this->getTypePartData($name, $defaults, 'source');
    }

    /**
     * 返回部分闪存源数据.
     *
     * @param string $name
     * @param mixed  $defaults
     * @param string $type
     *
     * @return mixed
     */
    protected function getTypePartData($name, $defaults = null, $type = '')
    {
        list($name, $tmp) = explode('\\', $name);

        $value = $this->{'get'.($type ? ucwords($type) : '')}($name);

        if (is_array($value)) {
            $parts = explode('.', $tmp);

            foreach ($parts as $part) {
                if (!isset($value[$part])) {
                    return $defaults;
                }

                $value = $value[$part];
            }

            return $value;
        }

        return $defaults;
    }

    /**
     * 返回下划线式命名.
     *
     * @param string $key
     *
     * @return string
     */
    protected function getUnCamelizeKey($key)
    {
        if (isset(static::$unCamelize[$key])) {
            return static::$unCamelize[$key];
        }

        return static::$unCamelize[$key] = Str::unCamelize($key);
    }
}
