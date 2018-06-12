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

namespace Leevel\Mvc;

use ArrayAccess;
use JsonSerializable;
use BadMethodCallException;
use Leevel\Flow\TControl;
use Leevel\Support\Str;
use Leevel\Support\IJson;
use Leevel\Support\IArray;
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
    protected $arrData = [];

    /**
     * 值对象源数据.
     *
     * @var array
     */
    protected $arrSourceData = [];

    /**
     * 转换隐藏的属性.
     *
     * @var array
     */
    protected $arrHidden = [];

    /**
     * 转换显示的属性.
     *
     * @var array
     */
    protected $arrVisible = [];

    /**
     * 追加.
     *
     * @var array
     */
    protected $arrAppend = [];

    /**
     * 缓存驼峰法命名属性到下划线
     *
     * @var array
     */
    protected static $arrUnCamelize = [];

    /**
     * 构造函数.
     *
     * @param array $arrData
     * @param array $arrSourceData
     */
    public function __construct($arrData = [], $arrSourceData = [])
    {
        $this->arrData = $arrData;
        $this->arrSourceData = $arrSourceData;
    }

    /**
     * 设置值对象
     *
     * @param string $sName
     * @param mxied  $mixValue
     */
    public function set($sName, $mixValue)
    {
        $this->arrData[$sName] = $mixValue;
    }

    /**
     * 批量插入.
     *
     * @param string|array $mixKey
     * @param mixed        $mixValue
     */
    public function put($mixKey, $mixValue = null)
    {
        if (!is_array($mixKey)) {
            $mixKey = [
                $mixKey => $mixValue,
            ];
        }

        foreach ($mixKey as $strKey => $mixValue) {
            $this->set($strKey, $mixValue);
        }
    }

    /**
     * 数组插入数据.
     *
     * @param string $strKey
     * @param mixed  $mixValue
     */
    public function push($strKey, $mixValue)
    {
        $arr = $this->get($strKey, []);
        $arr[] = $mixValue;
        $this->set($strKey, $arr);
    }

    /**
     * 合并元素.
     *
     * @param string $strKey
     * @param array  $arrValue
     */
    public function merge($strKey, array $arrValue)
    {
        $this->set($strKey, array_unique(array_merge($this->get($strKey, []), $arrValue)));
    }

    /**
     * 弹出元素.
     *
     * @param string $strKey
     * @param mixed  $mixValue
     */
    public function pop($strKey, array $arrValue)
    {
        $this->set($strKey, array_diff($this->get($strKey, []), $arrValue));
    }

    /**
     * 数组插入键值对数据.
     *
     * @param string $strKey
     * @param mixed  $mixKey
     * @param mixed  $mixValue
     */
    public function arrays($strKey, $mixKey, $mixValue = null)
    {
        $arr = $this->get($strKey, []);
        if (is_string($mixKey)) {
            $arr[$mixKey] = $mixValue;
        } elseif (is_array($mixKey)) {
            $arr = array_merge($arr, $mixKey);
        }
        $this->set($strKey, $arr);
    }

    /**
     * 数组键值删除数据.
     *
     * @param string $strKey
     * @param mixed  $mixKey
     */
    public function arraysDelete($strKey, $mixKey)
    {
        $arr = $this->get($strKey, []);
        if (!is_array($mixKey)) {
            $mixKey = [
                $mixKey,
            ];
        }
        foreach ($mixKey as $strFoo) {
            if (isset($arr[$strFoo])) {
                unset($arr[$strFoo]);
            }
        }
        $this->set($strKey, $arr);
    }

    /**
     * 取回值对象
     *
     * @param string $sName
     * @param mixed  $mixValue
     *
     * @return mxied
     */
    public function get($sName, $mixValue = null)
    {
        return $this->arrData[$sName] ?? $mixValue;
    }

    /**
     * 删除值对象
     *
     * @param string $sName
     *
     * @return bool
     */
    public function delete($sName)
    {
        if (isset($this->arrData[$sName])) {
            unset($this->arrData[$sName]);
        }

        return true;
    }

    /**
     * 是否存在值对象
     *
     * @param string $sName
     *
     * @return bool
     */
    public function has($sName)
    {
        return isset($this->arrData[$sName]);
    }

    /**
     * 清理所有值对象数据.
     */
    public function clear()
    {
        $this->arrData = [];
    }

    /**
     * 数据是否为空.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->arrData);
    }

    /**
     * 取回值对象数据.
     *
     * @param string $sName
     * @param mixed  $mixDefault
     *
     * @return mixed
     */
    public function getData($sName, $mixDefault = null)
    {
        if (false !== strpos($sName, '\\')) {
            return $this->getPartData($sName, $mixDefault);
        } else {
            return $this->get($sName, $mixDefault);
        }
    }

    /**
     * 批量取回值对象数据.
     *
     * @param array $arrName
     * @param mixed $mixDefault
     *
     * @return array
     */
    public function getDatas(array $arrName, $mixDefault)
    {
        $arr = [];
        foreach ($arrName as $strName) {
            $arr[$strName] = $this->getData($strName, $mixDefault);
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
        return $this->arrData;
    }

    /**
     * 设置值对象源数据.
     *
     * @param string $sName
     * @param mxied  $mixValue
     */
    public function setSource($sName, $mixValue)
    {
        $this->arrSourceData[$sName] = $mixValue;
    }

    /**
     * 批量插入源数据.
     *
     * @param string|array $mixKey
     * @param mixed        $mixValue
     */
    public function putSource($mixKey, $mixValue = null)
    {
        if (!is_array($mixKey)) {
            $mixKey = [
                $mixKey => $mixValue,
            ];
        }

        foreach ($mixKey as $strKey => $mixValue) {
            $this->setSource($strKey, $mixValue);
        }
    }

    /**
     * 数组插入源数据.
     *
     * @param string $strKey
     * @param mixed  $mixValue
     */
    public function pushSource($strKey, $mixValue)
    {
        $arr = $this->getSource($strKey, []);
        $arr[] = $mixValue;
        $this->setSource($strKey, $arr);
    }

    /**
     * 合并源数据元素.
     *
     * @param string $strKey
     * @param array  $arrValue
     */
    public function mergeSource($strKey, array $arrValue)
    {
        $this->setSource($strKey, array_unique(array_merge($this->getSource($strKey, []), $arrValue)));
    }

    /**
     * 弹出源数据元素.
     *
     * @param string $strKey
     * @param mixed  $mixValue
     */
    public function popSource($strKey, array $arrValue)
    {
        $this->setSource($strKey, array_diff($this->getSource($strKey, []), $arrValue));
    }

    /**
     * 数组插入键值对源数据.
     *
     * @param string $strKey
     * @param mixed  $mixKey
     * @param mixed  $mixValue
     */
    public function arraysSource($strKey, $mixKey, $mixValue = null)
    {
        $arr = $this->getSource($strKey, []);
        if (is_string($mixKey)) {
            $arr[$mixKey] = $mixValue;
        } elseif (is_array($mixKey)) {
            $arr = array_merge($arr, $mixKey);
        }
        $this->setSource($strKey, $arr);
    }

    /**
     * 数组键值删除源数据.
     *
     * @param string $strKey
     * @param mixed  $mixKey
     */
    public function arraysDeleteSource($strKey, $mixKey)
    {
        $arr = $this->getSource($strKey, []);
        if (!is_array($mixKey)) {
            $mixKey = [
                $mixKey,
            ];
        }
        foreach ($mixKey as $strFoo) {
            if (isset($arr[$strFoo])) {
                unset($arr[$strFoo]);
            }
        }
        $this->setSource($strKey, $arr);
    }

    /**
     * 取回源数据值对象
     *
     * @param string $sName
     * @param mixed  $mixValue
     *
     * @return mxied
     */
    public function getSource($sName, $mixValue = null)
    {
        return $this->arrSourceData[$sName] ?? $mixValue;
    }

    /**
     * 删除值对象源数据.
     *
     * @param string $sName
     *
     * @return bool
     */
    public function deleteSource($sName)
    {
        if (isset($this->arrSourceData[$sName])) {
            unset($this->arrSourceData[$sName]);
        }

        return true;
    }

    /**
     * 是否存在值对象源数据.
     *
     * @param string $sName
     *
     * @return bool
     */
    public function hasSource($sName)
    {
        return isset($this->arrSourceData[$sName]);
    }

    /**
     * 清理所有值对象源数据.
     */
    public function clearSource()
    {
        $this->arrSourceData = [];
    }

    /**
     * 源数据是否为空.
     *
     * @return bool
     */
    public function isEmptySource()
    {
        return empty($this->arrSourceData);
    }

    /**
     * 取回值对象源数据.
     *
     * @param string           $sName
     * @param mixed|string|int $mixDefault
     *
     * @return mixed
     */
    public function getSourceData($sName, $mixDefault = null)
    {
        if (false !== strpos($sName, '\\')) {
            return $this->getSourcePartData($sName, $mixDefault);
        } else {
            return $this->getSource($sName, $mixDefault);
        }
    }

    /**
     * 批量取回值对象源数据.
     *
     * @param array $arrName
     * @param mixed $mixDefault
     *
     * @return array
     */
    public function getSourceDatas(array $arrName, $mixDefault)
    {
        $arr = [];
        foreach ($arrName as $strName) {
            $arr[$strName] = $this->getSourceData($strName, $mixDefault);
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
        return $this->arrSourceData;
    }

    /**
     * 比较数据是否变更.
     *
     * @param string $sName
     * @param bool   $booStrict
     *
     * @return bool
     */
    public function hasChange($sName, $booStrict = false)
    {
        $mixNewData = $this->getData($sName);
        $mixSourceData = $this->getSourceData($sName);

        return (false === $booStrict && $mixNewData != $mixSourceData) || (true === $booStrict && $mixNewData !== $mixSourceData);
    }

    /**
     * 批量比较数据是否变更.
     *
     * @param array $arrName
     * @param bool  $booStrict
     *
     * @return bool
     */
    public function hasChanges($arrName, $booStrict = false)
    {
        $arr = [];
        foreach ($arrName as $strName) {
            $arr[$strName] = $this->hasChange($strName, $booStrict);
        }

        return $arr;
    }

    /**
     * 依据新数据比较数据是否变更.
     *
     * @param bool $booStrict
     *
     * @return array
     */
    public function hasChangeNew($booStrict = false)
    {
        $arr = [];
        foreach (array_keys($this->arrData) as $strName) {
            $arr[$strName] = $this->hasChange($strName, $booStrict);
        }

        return $arr;
    }

    /**
     * 依据源数据比较数据是否变更.
     *
     * @param bool $booStrict
     *
     * @return array
     */
    public function hasChangeSource($booStrict = false)
    {
        $arr = [];
        foreach (array_keys($this->arrSourceData) as $strName) {
            $arr[$strName] = $this->hasChange($strName, $booStrict);
        }

        return $arr;
    }

    /**
     * 设置转换隐藏属性.
     *
     * @param array $arrHidden
     *
     * @return $this
     */
    public function hidden(array $arrHidden)
    {
        if ($this->checkTControl()) {
            return $this;
        }
        $this->arrHidden = $arrHidden;

        return $this;
    }

    /**
     * 获取转换隐藏属性.
     *
     * @return array
     */
    public function getHidden()
    {
        return $this->arrHidden;
    }

    /**
     * 添加转换隐藏属性.
     *
     * @param array|string $mixProp
     *
     * @return $this
     */
    public function addHidden($mixProp)
    {
        if ($this->checkTControl()) {
            return $this;
        }
        $mixProp = is_array($mixProp) ? $mixProp : func_get_args();
        $this->arrHidden = array_merge($this->arrHidden, $mixProp);

        return $this;
    }

    /**
     * 设置转换显示属性.
     *
     * @param array $arrVisible
     *
     * @return $this
     */
    public function visible(array $arrVisible)
    {
        if ($this->checkTControl()) {
            return $this;
        }
        $this->arrVisible = $arrVisible;

        return $this;
    }

    /**
     * 获取转换显示属性.
     *
     * @return array
     */
    public function getVisible()
    {
        return $this->arrVisible;
    }

    /**
     * 添加转换显示属性.
     *
     * @param array|string $mixProp
     *
     * @return $this
     */
    public function addVisible($mixProp)
    {
        if ($this->checkTControl()) {
            return $this;
        }
        $mixProp = is_array($mixProp) ? $mixProp : func_get_args();
        $this->arrVisible = array_merge($this->arrVisible, $mixProp);

        return $this;
    }

    /**
     * 设置转换追加属性.
     *
     * @param array $arrAppend
     *
     * @return $this
     */
    public function append(array $arrAppend)
    {
        if ($this->checkTControl()) {
            return $this;
        }
        $this->arrAppend = $arrAppend;

        return $this;
    }

    /**
     * 获取转换追加属性.
     *
     * @return array
     */
    public function getAppend()
    {
        return $this->arrAppend;
    }

    /**
     * 添加转换追加属性.
     *
     * @param array|string|null $mixProp
     *
     * @return $this
     */
    public function addAppend($mixProp = null)
    {
        if ($this->checkTControl()) {
            return $this;
        }
        $mixProp = is_array($mixProp) ? $mixProp : func_get_args();
        $this->arrAppend = array_merge($this->arrAppend, $mixProp);

        return $this;
    }

    /**
     * 返回部分闪存数据.
     *
     * @param string $sName
     * @param mixed  $mixDefault
     *
     * @return mixed
     */
    protected function getPartData($sName, $mixDefault = null)
    {
        return $this->getTypePartData($sName, $mixDefault);
    }

    /**
     * 返回部分闪存源数据.
     *
     * @param string $sName
     * @param mixed  $mixDefault
     *
     * @return mixed
     */
    protected function getSourcePartData($sName, $mixDefault = null)
    {
        return $this->getTypePartData($sName, $mixDefault, 'source');
    }

    /**
     * 返回部分闪存源数据.
     *
     * @param string $sName
     * @param mixed  $mixDefault
     * @param string $strType
     *
     * @return mixed
     */
    protected function getTypePartData($sName, $mixDefault = null, $strType = '')
    {
        list($sName, $strName) = explode('\\', $sName);
        $mixValue = $this->{'get'.($strType ? ucwords($strType) : '')}($sName);

        if (is_array($mixValue)) {
            $arrParts = explode('.', $strName);
            foreach ($arrParts as $sPart) {
                if (!isset($mixValue[$sPart])) {
                    return $mixDefault;
                }
                $mixValue = $mixValue[$sPart];
            }

            return $mixValue;
        } else {
            return $mixDefault;
        }
    }

    /**
     * 返回下划线式命名.
     *
     * @param string $strKey
     *
     * @return string
     */
    protected function getUnCamelizeKey($strKey)
    {
        if (isset(static::$arrUnCamelize[$strKey])) {
            return static::$arrUnCamelize[$strKey];
        }

        return static::$arrUnCamelize[$strKey] = Str::unCamelize($strKey);
    }

    /**
     * 魔术方法获取.
     *
     * @param string $sName
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
     * @param string $sName
     * @param mixed  $mixValue
     *
     * @return $this
     */
    public function __set($key, $mixValue)
    {
        return $this->set($this->getUnCamelizeKey($key), $mixValue);
    }

    /**
     * 是否存在属性.
     *
     * @param string $sName
     *
     * @return bool
     */
    public function __isset($sName)
    {
        return $this->has($this->getUnCamelizeKey($sName));
    }

    /**
     * 删除属性.
     *
     * @param string $sName
     *
     * @return bool
     */
    public function __unset($sName)
    {
        return $this->delete($this->getUnCamelizeKey($sName));
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
        $arrArgs = func_get_args();
        if ($arrArgs) {
            $arrVisible = array_unique(array_merge($this->arrVisible, is_array($arrArgs[0]) ? $arrArgs[0] : $arrArgs));
        } else {
            $arrVisible = $this->arrVisible;
        }

        if (!empty($arrVisible)) {
            $arrData = array_intersect_key($this->arrData, array_flip($arrVisible));
        } elseif (!empty($this->arrHidden)) {
            $arrData = array_diff_key($this->arrData, array_flip($this->arrHidden));
        } else {
            $arrData = $this->arrData;
        }

        $arrData = array_merge($arrData, $this->arrAppend ? array_flip($this->arrAppend) : []);
        foreach ($arrData as $strKey => &$mixValue) {
            $mixValue = $this->getData($strKey);
        }

        return $arrData;
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
     * call.
     *
     * @param string $method
     * @param array  $arrArgs
     *
     * @return mixed
     */
    public function __call(string $method, array $arrArgs)
    {
        if ($this->placeholderTControl($method)) {
            return $this;
        }

        switch (true) {
            case 'get' == substr($method, 0, 3):
                return $this->__get(substr($method, 3), $arrArgs[0] ?? null);
            case 'set' == substr($method, 0, 3):
                return $this->__set(substr($method, 3), $arrArgs[0] ?? null);
            case 'delete' == substr($method, 0, 5):
                return $this->__unset(substr($method, 5));
            case 'has' == substr($method, 0, 3):
                return $this->__isset(substr($method, 3));
        }

        throw new BadMethodCallException(sprintf('Method %s is not exits.', $method));
    }
}
