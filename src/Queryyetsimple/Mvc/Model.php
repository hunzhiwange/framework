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
use BadMethodCallException;
use Carbon\Carbon;
use DateTime;
use DateTimeInterface;
use Exception;
use JsonSerializable;
use Leevel\Collection\Collection;
use Leevel\Event\IDispatch;
use Leevel\Flow\TControl;
use Leevel\Mvc\Relation\BelongsTo;
use Leevel\Mvc\Relation\HasMany;
use Leevel\Mvc\Relation\ManyMany;
use Leevel\Mvc\Relation\Relation;
use Leevel\Support\Arr;
use Leevel\Support\IArray;
use Leevel\Support\IJson;
use Leevel\Support\Str;
use Leevel\Support\TMacro;
use Leevel\Support\TSerialize;

/**
 * 模型 Object Relational Mapping.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.04.27
 *
 * @version 1.0
 */
abstract class Model implements IModel, IArray, IJson, JsonSerializable, ArrayAccess
{
    use TSerialize;
    use TMacro {
        __callStatic as macroCallStatic;
        __call as macroCall;
    }
    use TControl;

    /**
     * 与模型关联的数据表.
     *
     * @var string
     */
    protected $strTable;

    /**
     * 此模型的连接名称.
     *
     * @var mixed
     */
    protected $mixConnect;

    /**
     * 模型属性.
     *
     * @var array
     */
    protected $arrProp = [];

    /**
     * 改变的模型属性.
     *
     * @var array
     */
    protected $arrChangedProp = [];

    /**
     * 构造器初始化数据黑名单.
     *
     * @var array
     */
    protected $arrConstructBlack = [];

    /**
     * 构造器初始化数据白名单.
     *
     * @var array
     */
    protected $arrConstructWhite = [];

    /**
     * 数据赋值黑名单.
     *
     * @var array
     */
    protected $arrFillBlack = [];

    /**
     * 数据赋值白名单.
     *
     * @var array
     */
    protected $arrFillWhite = [];

    /**
     * 数据赋值写入黑名单.
     *
     * @var array
     */
    protected $arrCreateFillBlack = [];

    /**
     * 数据赋值写入白名单.
     *
     * @var array
     */
    protected $arrCreateFillWhite = [];

    /**
     * 数据赋值更新黑名单.
     *
     * @var array
     */
    protected $arrUpdateFillBlack = [];

    /**
     * 数据赋值更新白名单.
     *
     * @var array
     */
    protected $arrUpdateFillWhite = [];

    /**
     * 只读属性.
     *
     * @var array
     */
    protected $arrReadonly = [];

    /**
     * 写入是否自动提交 POST 数据.
     *
     * @var bool
     */
    protected $booCreateAutoPost = false;

    /**
     * 更新是否自动提交 POST 数据.
     *
     * @var bool
     */
    protected $booUpdateAutoPost = false;

    /**
     * 自动提交 POST 数据白名单.
     *
     * @var array
     */
    protected $arrPostWhite = [];

    /**
     * 自动提交 POST 数据黑名单.
     *
     * @var array
     */
    protected $arrPostBlack = [];

    /**
     * 自动提交 POST 数据写入白名单.
     *
     * @var array
     */
    protected $arrCreatePostWhite = [];

    /**
     * 自动提交 POST 数据写入黑名单.
     *
     * @var array
     */
    protected $arrCreatePostBlack = [];

    /**
     * 自动提交 POST 数据更新白名单.
     *
     * @var array
     */
    protected $arrUpdatePostWhite = [];

    /**
     * 自动提交 POST 数据更新黑名单.
     *
     * @var array
     */
    protected $arrUpdatePostBlack = [];

    /**
     * 写入是否自动填充.
     *
     * @var bool
     */
    protected $booCreateAutoFill = true;

    /**
     * 更新是否自动填充.
     *
     * @var bool
     */
    protected $booUpdateAutoFill = true;

    /**
     * 自动填充.
     *
     * @var array
     */
    protected $arrAutoFill = [];

    /**
     * 创建自动填充.
     *
     * @var array
     */
    protected $arrCreateFill = [];

    /**
     * 更新自动填充.
     *
     * @var array
     */
    protected $arrUpdateFill = [];

    /**
     * 数据类型.
     *
     * @var array
     */
    protected $arrConversion = [];

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
     * 模型的日期字段保存格式.
     *
     * @var string
     */
    protected $strDateFormat = 'Y-m-d H:i:s';

    /**
     * 应被转换为日期的属性.
     *
     * @var array
     */
    protected $arrDate = [];

    /**
     * 开启默认时间属性转换.
     *
     * @var array
     */
    protected $booTimestamp = true;

    /**
     * 查询 select.
     *
     * @var \Leevel\Database\Select
     */
    protected $objSelectForQuery;

    /**
     * 模型事件处理器.
     *
     * @var \Leevel\Event\IDispatch
     */
    protected static $objDispatch;

    /**
     * 缓存下划线到驼峰法命名属性.
     *
     * @var array
     */
    protected static $arrCamelizeProp = [];

    /**
     * 关联数据缓存.
     *
     * @var array
     */
    protected $arrRelationProp = [];

    /**
     * 模型字段.
     *
     * @var array
     */
    protected $arrField;

    /**
     * 模型主键字段.
     *
     * @var array
     */
    protected $arrPrimaryKey;

    /**
     * 最后插入记录.
     *
     * @var mixed
     */
    protected $mixLastInsertId;

    /**
     * 响应记录.
     *
     * @var int
     */
    protected $intRowCount;

    /**
     * 最后插入记录或者响应记录.
     *
     * @var mixed
     */
    protected $mixLastInsertIdOrRowCount;

    /**
     * 是否处于强制改变属性中.
     *
     * @var bool
     */
    protected $booForceProp = false;

    /**
     * 构造函数.
     *
     * @param null|array $arrData
     * @param mixed      $mixConnect
     * @param string     $strTable
     */
    public function __construct($arrData = null, $mixConnect = null, $strTable = null)
    {
        if (null !== $mixConnect) {
            $this->mixConnect = $mixConnect;
        }

        if (null !== $strTable) {
            $this->strTable = $strTable;
        } else {
            $this->parseTableByClass();
        }

        $this->initField();

        if (is_array($arrData) && $arrData) {
            foreach ($this->whiteAndBlack(array_keys($arrData), $this->arrConstructWhite, $this->arrConstructBlack) as $strProp) {
                if (array_key_exists($strProp, $arrData)) {
                    $this->prop($strProp, $arrData[$strProp]);
                }
            }
        }
    }

    /**
     * 魔术方法获取.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getProp($key);
    }

    /**
     * 强制更新属性值
     *
     * @param string $key
     * @param mixed  $mixValue
     *
     * @return $this
     */
    public function __set($key, $mixValue)
    {
        return $this->forceProp($key, $mixValue);
    }

    /**
     * 是否存在属性.
     *
     * @param string $sPropName
     *
     * @return bool
     */
    public function __isset($sPropName)
    {
        return $this->hasProp($sPropName);
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

        // 作用域
        if (method_exists($this, 'scope'.ucwords($method))) {
            array_unshift($arrArgs, $method);

            return $this->{'scope'}(...$arrArgs);
        }

        try {
            // 调用 trait __call 实现扩展方法
            return $this->macroCall($method, $arrArgs);
        } catch (BadMethodCallException $oE) {
            $this->runEvent(static::BEFORE_FIND_EVENT);
            $this->runEvent(static::BEFORE_SELECT_EVENT);

            $mixData = $this->getClassCollectionQuery()->{$method}(...$arrArgs);

            if ($mixData instanceof Collection) {
                $this->runEvent(static::AFTER_SELECT_EVENT, $mixData);
            } else {
                $this->runEvent(static::AFTER_FIND_EVENT, $mixData);
            }

            return $mixData;
        }
    }

    /**
     * call static.
     *
     * @param string $method
     * @param array  $arrArgs
     *
     * @return mixed
     */
    public static function __callStatic(string $method, array $arrArgs)
    {
        return (new static())->{$method}(...$arrArgs);
    }

    /**
     * 将模型转化为 JSON.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * 自动判断快捷方式.
     *
     * @param null|array $arrData
     *
     * @return $this
     */
    public function save($arrData = null)
    {
        $this->saveEntry('save', $arrData);

        return $this;
    }

    /**
     * 新增快捷方式.
     *
     * @param null|array $arrData
     *
     * @return $this
     */
    public function create($arrData = null)
    {
        $this->saveEntry('create', $arrData);

        return $this;
    }

    /**
     * 更新快捷方式.
     *
     * @param null|array $arrData
     *
     * @return $this
     */
    public function update($arrData = null)
    {
        $this->saveEntry('update', $arrData);

        return $this;
    }

    /**
     * replace 快捷方式.
     *
     * @param null|array $arrData
     *
     * @return $this
     */
    public function replace($arrData = null)
    {
        $this->saveEntry('replace', $arrData);

        return $this;
    }

    /**
     * 自动判断快捷方式返回主键或者响应记录.
     *
     * @param null|array $arrData
     *
     * @return mixed
     */
    public function saveResult($arrData = null)
    {
        return $this->saveEntry('save', $arrData);
    }

    /**
     * 新增快捷方式返回主键.
     *
     * @param null|array $arrData
     *
     * @return mixed
     */
    public function createResult($arrData = null)
    {
        return $this->saveEntry('create', $arrData);
    }

    /**
     * 更新快捷方式返回响应记录.
     *
     * @param null|array $arrData
     *
     * @return int
     */
    public function updateResult($arrData = null)
    {
        return $this->saveEntry('update', $arrData);
    }

    /**
     * replace 快捷方式返回主键或者响应记录.
     *
     * @param null|array $arrData
     *
     * @return mixed
     */
    public function replaceResult($arrData = null)
    {
        return $this->saveEntry('replace', $arrData);
    }

    /**
     * 自动判断快捷方式生成模型.
     *
     * @param null|array $arrData
     *
     * @return $this
     */
    public static function saveNew($arrData = null)
    {
        $objModel = new static($arrData);
        $objModel->save();

        return $objModel;
    }

    /**
     * 新增快捷方式生成模型.
     *
     * @param null|array $arrData
     *
     * @return $this
     */
    public static function createNew($arrData = null)
    {
        $objModel = new static($arrData);
        $objModel->create();

        return $objModel;
    }

    /**
     * 更新快捷方式生成模型.
     *
     * @param null|array $arrData
     *
     * @return $this
     */
    public static function updateNew($arrData = null)
    {
        $objModel = new static($arrData);
        $objModel->update();

        return $objModel;
    }

    /**
     * replace 快捷方式生成模型.
     *
     * @param null|array $arrData
     *
     * @return $this
     */
    public static function replaceNew($arrData = null)
    {
        $objModel = new static($arrData);
        $objModel->replace();

        return $objModel;
    }

    /**
     * 返回最后插入记录.
     *
     * @return mixed
     */
    public function lastInsertId()
    {
        return $this->mixLastInsertId;
    }

    /**
     * 返回响应记录.
     *
     * @return int
     */
    public function rowCount()
    {
        return $this->intRowCount;
    }

    /**
     * 返回最后插入记录或者响应记录.
     *
     * @return mixed
     */
    public function lastInsertIdOrRowCount()
    {
        return $this->mixLastInsertIdOrRowCount;
    }

    /**
     * 根据主键 ID 删除模型.
     *
     * @param array|int $ids
     * @param mixed     $mixId
     *
     * @return int
     */
    public function destroy($mixId)
    {
        $intCount = 0;
        $mixId = (array) $mixId;
        $objInstance = new static();
        foreach ($objInstance->whereIn($objInstance->getPrimaryKeyNameForQuery(), $mixId)->getAll() as $objModel) {
            if ($objModel->delete()) {
                $intCount++;
            }
        }

        return $intCount;
    }

    /**
     * 删除模型.
     *
     * @return int
     */
    public function delete()
    {
        if (null === $this->getPrimaryKeyName()) {
            throw new Exception(sprintf('Model %s has no primary key', $this->getCalledClass()));
        }

        $this->runEvent(static::BEFORE_DELETE_EVENT);

        $intNum = $this->deleteModelByKey();

        $this->runEvent(static::AFTER_DELETE_EVENT);

        return $intNum;
    }

    /**
     * 唯一标识符.
     *
     * @return mixed
     */
    public function id()
    {
        return $this->primaryKey();
    }

    /**
     * 获取主键.
     *
     * @param bool $booUpdateChange
     *
     * @return mixed
     */
    public function primaryKey($booUpdateChange = false)
    {
        $arrPrimaryData = [];

        $arrPrimaryKey = $this->getPrimaryKeyNameSource();
        foreach ($arrPrimaryKey as $sPrimaryKey) {
            if (!isset($this->arrProp[$sPrimaryKey])) {
                continue;
            }
            if (true === $booUpdateChange) {
                if (!in_array($sPrimaryKey, $this->arrChangedProp, true)) {
                    $arrPrimaryData[$sPrimaryKey] = $this->arrProp[$sPrimaryKey];
                }
            } else {
                $arrPrimaryData[$sPrimaryKey] = $this->arrProp[$sPrimaryKey];
            }
        }

        // 复合主键，但是数据不完整则忽略
        if (count($arrPrimaryKey) > 1 && count($arrPrimaryKey) !== count($arrPrimaryData)) {
            return;
        }

        if (1 === count($arrPrimaryData)) {
            $arrPrimaryData = reset($arrPrimaryData);
        }

        if (!empty($arrPrimaryData)) {
            return $arrPrimaryData;
        }
    }

    /**
     * 改变属性.
     *
     * < update 调用无效，请换用 forceProp >
     *
     * @param mixed $mixProp
     * @param mixed $mixValue
     * @param mixed $strProp
     *
     * @return $this
     */
    public function prop($strProp, $mixValue)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $mixValue = $this->meta()->fieldsProp($strProp, $mixValue);

        if (null === $mixValue && ($strCamelize = 'set'.$this->getCamelizeProp($strProp).'Prop') && method_exists($this, $strCamelize)) {
            if (null === (($mixValue = $this->{$strCamelize}($this->getProp($strProp))))) {
                $mixValue = $this->getProp($strProp);
            }
        } elseif ($mixValue && (in_array($strProp, $this->getDate(), true) || $this->isDateConversion($strProp))) {
            $mixValue = $this->fromDateTime($mixValue);
        } elseif ($this->isJsonConversion($strProp) && null !== $mixValue) {
            $mixValue = $this->asJson($mixValue);
        }

        $this->arrProp[$strProp] = $mixValue;
        if ($this->getForceProp() && !in_array($strProp, $this->arrReadonly, true) && !in_array($strProp, $this->arrChangedProp, true)) {
            $this->arrChangedProp[] = $strProp;
        }

        return $this;
    }

    /**
     * 批量强制改变属性.
     *
     * < update 调用无效，请换用 propForces >
     *
     * @param array $arrProp
     *
     * @return $this
     */
    public function props(array $arrProp)
    {
        if ($this->checkTControl()) {
            return $this;
        }
        foreach ($arrProp as $strProp => $mixValue) {
            $this->prop($strProp, $mixValue);
        }

        return $this;
    }

    /**
     * 强制改变属性.
     *
     * @param mixed $strPropName
     * @param mixed $mixValue
     *
     * @return $this
     */
    public function forceProp($strPropName, $mixValue)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->setForceProp(true);
        call_user_func_array([
            $this,
            'prop',
        ], [
            $strPropName,
            $mixValue,
        ]);
        $this->setForceProp(false);

        return $this;
    }

    /**
     * 批量强制改变属性.
     *
     * @param array $arrProp
     *
     * @return $this
     */
    public function forceProps(array $arrProp)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->setForceProp(true);
        call_user_func_array([
            $this,
            'props',
        ], [
            $arrProp,
        ]);
        $this->setForceProp(false);

        return $this;
    }

    /**
     * 返回属性.
     *
     * @param string $strPropName
     *
     * @return mixed
     */
    public function getProp($strPropName)
    {
        if (!isset($this->arrProp[$strPropName])) {
            if (method_exists($this, $strPropName)) {
                return $this->loadRelationProp($strPropName);
            }
            if (!$this->hasField($strPropName)) {
                throw new Exception(sprintf('Model %s database table %s has no field %s', $this->getCalledClass(), $this->getTable(), $strPropName));
            }
            $mixValue = null;
        } else {
            $mixValue = $this->arrProp[$strPropName];
        }

        if (($strCamelize = 'get'.$this->getCamelizeProp($strPropName).'Prop') && method_exists($this, $strCamelize)) {
            $mixValue = $this->{$strCamelize}($mixValue);
        }

        if ($this->hasConversion($strPropName)) {
            $mixValue = $this->conversionProp($strPropName, $mixValue);
        }

        return $mixValue;
    }

    /**
     * 返回关联数据.
     *
     * @param string $strPropName
     *
     * @return mixed
     */
    public function loadRelationProp($strPropName)
    {
        if ($this->hasRelationProp($strPropName)) {
            return $this->getRelationProp($strPropName);
        }

        return $this->parseDataFromRelation($strPropName);
    }

    /**
     * 是否存在属性.
     *
     * @param string $sPropName
     *
     * @return bool
     */
    public function hasProp($sPropName)
    {
        return array_key_exists($sPropName, $this->arrProp);
    }

    /**
     * 删除属性.
     *
     * @param string $sPropName
     *
     * @return $this
     */
    public function deleteProp($sPropName)
    {
        if ($this->checkTControl()) {
            return $this;
        }
        if (!isset($this->arrProp[$sPropName])) {
            unset($this->arrProp[$sPropName]);
        }

        return $this;
    }

    /**
     * 取得所有关联缓存数据.
     *
     * @return array
     */
    public function getRelationProps()
    {
        return $this->arrRelationProp;
    }

    /**
     * 取得模型数据.
     *
     * @param string $sPropName
     *
     * @return mixed
     */
    public function getRelationProp($sPropName)
    {
        return $this->hasRelationProp($sPropName) ? $this->arrRelationProp[$sPropName] : null;
    }

    /**
     * 关联模型数据是否载入.
     *
     * @param string $sPropName
     *
     * @return bool
     */
    public function hasRelationProp($sPropName)
    {
        return array_key_exists($sPropName, $this->arrRelationProp);
    }

    /**
     * 设置关联数据.
     *
     * @param string $sPropName
     * @param mixed  $mixValue
     *
     * @return $this
     */
    public function setRelationProp($sPropName, $mixValue)
    {
        if ($this->checkTControl()) {
            return $this;
        }
        $this->arrRelationProp[$sPropName] = $mixValue;

        return $this;
    }

    /**
     * 批量设置关联数据.
     *
     * @param array $arrRelationProp
     *
     * @return $this
     */
    public function setRelationProps(array $arrRelationProp)
    {
        if ($this->checkTControl()) {
            return $this;
        }
        $this->arrRelationProp = $arrRelationProp;

        return $this;
    }

    /**
     * 预加载关联.
     *
     * @param array|string $mixRelation
     *
     * @return \Leevel\Mvc\select
     */
    public static function with($mixRelation)
    {
        if (is_string($mixRelation)) {
            $mixRelation = func_get_args();
        }

        return (new static())->getClassCollectionQuery()->with($mixRelation);
    }

    /**
     * 一对一关联.
     *
     * @param string $strRelatedModel
     * @param string $strTargetKey
     * @param string $strSourceKey
     *
     * @return \Leevel\Mvc\Relation\HasOne|void
     */
    public function hasOne($strRelatedModel, $strTargetKey = null, $strSourceKey = null)
    {
        $objModel = new $strRelatedModel();
        $strTargetKey = $strTargetKey ?: $this->getTargetKey();
        $strSourceKey = $strSourceKey ?: $this->getPrimaryKeyNameForQuery();

        if (!$objModel->hasField($strTargetKey)) {
            throw new Exception(sprintf('Model %s database table %s has no field %s', $strRelatedModel, $objModel->getTable(), $strTargetKey));
        }

        if (!$this->hasField($strSourceKey)) {
            throw new Exception(sprintf('Model %s database table %s has no field %s', $this->getCalledClass(), $this->getTable(), $strSourceKey));
        }

        return new HasOne($objModel, $this, $strTargetKey, $strSourceKey);
    }

    /**
     * 定义从属关系.
     *
     * @param string $strRelatedModel
     * @param string $strTargetKey
     * @param string $strSourceKey
     *
     * @return \Leevel\Mvc\Relation\BelongsTo|void
     */
    public function belongsTo($strRelatedModel, $strTargetKey = null, $strSourceKey = null)
    {
        $objModel = new $strRelatedModel();

        $strTargetKey = $strTargetKey ?: $objModel->getPrimaryKeyNameForQuery();
        $strSourceKey = $strSourceKey ?: $objModel->getTargetKey();

        if (!$objModel->hasField($strTargetKey)) {
            throw new Exception(sprintf('Model %s has no field %sModel %s database table %s has no field %s', $strRelatedModel, $objModel->getTable(), $strTargetKey));
        }

        if (!$this->hasField($strSourceKey)) {
            throw new Exception(sprintf('Model %s database table %s has no field %s', $this->getCalledClass(), $this->getTable(), $strSourceKey));
        }

        return new BelongsTo($objModel, $this, $strTargetKey, $strSourceKey);
    }

    /**
     * 一对多关联.
     *
     * @param string $strRelatedModel
     * @param string $strTargetKey
     * @param string $strSourceKey
     *
     * @return \Leevel\Mvc\Relation\HasMany|void
     */
    public function hasMany($strRelatedModel, $strTargetKey = null, $strSourceKey = null)
    {
        $objModel = new $strRelatedModel();
        $strTargetKey = $strTargetKey ?: $this->getTargetKey();
        $strSourceKey = $strSourceKey ?: $this->getPrimaryKeyNameForQuery();

        if (!$objModel->hasField($strTargetKey)) {
            throw new Exception(sprintf('Model %s database table %s has no field %s', $strRelatedModel, $objModel->getTable(), $strTargetKey));
        }

        if (!$this->hasField($strSourceKey)) {
            throw new Exception(sprintf('Model %s database table %s has no field %s', $this->getCalledClass(), $this->getTable(), $strSourceKey));
        }

        return new HasMany($objModel, $this, $strTargetKey, $strSourceKey);
    }

    /**
     * 多对多关联.
     *
     * @param string $strRelatedModel
     * @param string $strMiddleModel
     * @param string $strTargetKey
     * @param string $strSourceKey
     * @param string $strMiddleTargetKey
     * @param string $strMiddleSourceKey
     *
     * @return \Leevel\Mvc\Relation\HasMany|void
     */
    public function manyMany($strRelatedModel, $strMiddleModel = null, $strTargetKey = null, $strSourceKey = null, $strMiddleTargetKey = null, $strMiddleSourceKey = null)
    {
        $objModel = new $strRelatedModel();

        $strMiddleModel = $strMiddleModel ?: $this->getMiddleModel($objModel);
        $objMiddleModel = new $strMiddleModel();

        $strTargetKey = $strTargetKey ?: $objModel->getPrimaryKeyNameForQuery();
        $strMiddleTargetKey = $strMiddleTargetKey ?: $objModel->getTargetKey();

        $strSourceKey = $strSourceKey ?: $this->getPrimaryKeyNameForQuery();
        $strMiddleSourceKey = $strMiddleSourceKey ?: $this->getTargetKey();

        if (!$objModel->hasField($strTargetKey)) {
            throw new Exception(sprintf('Model %s database table %s has no field %s', $strRelatedModel, $objModel->getTable(), $strTargetKey));
        }

        if (!$objMiddleModel->hasField($strMiddleTargetKey)) {
            throw new Exception(sprintf('Model %s database table %s has no field %s', $strMiddleModel, $objMiddleModel->getTable(), $strMiddleTargetKey));
        }

        if (!$this->hasField($strSourceKey)) {
            throw new Exception(sprintf('Model %s database table %s has no field %s', $this->getCalledClass(), $this->getTable(), $strSourceKey));
        }

        if (!$objMiddleModel->hasField($strMiddleSourceKey)) {
            throw new Exception(sprintf('Model %s database table %s has no field %s', $strMiddleModel, $objMiddleModel->getTable(), $strMiddleSourceKey));
        }

        return new ManyMany($objModel, $this, $objMiddleModel, $strTargetKey, $strSourceKey, $strMiddleTargetKey, $strMiddleSourceKey);
    }

    /**
     * 中间表带命名空间完整名字.
     *
     * @param \Leevel\Mvc\IModel $objRelatedModel
     *
     * @return string
     */
    public function getMiddleModel($objRelatedModel)
    {
        $arrClass = explode('\\', $this->getCalledClass());
        array_pop($arrClass);
        $arrClass[] = $this->getMiddleTable($objRelatedModel);

        return implode('\\', $arrClass);
    }

    /**
     * 取得中间表名字.
     *
     * @param \Leevel\Mvc\IModel $objRelatedModel
     *
     * @return string
     */
    public function getMiddleTable($objRelatedModel)
    {
        return $this->getTable().'_'.$objRelatedModel->getTable();
    }

    /**
     * 返回惯性关联 ID.
     *
     * @return string
     */
    public function getTargetKey()
    {
        return $this->getTable().'_'.$this->getPrimaryKeyNameForQuery();
    }

    /**
     * 注册模型事件 selecting.
     *
     * @param \leevel\event\observer|string $mixListener
     */
    public static function selecting($mixListener)
    {
        static::registerEvent(static::BEFORE_SELECT_EVENT, $mixListener);
    }

    /**
     * 注册模型事件 selected.
     *
     * @param \leevel\event\observer|string $mixListener
     */
    public static function selected($mixListener)
    {
        static::registerEvent(static::AFTER_SELECT_EVENT, $mixListener);
    }

    /**
     * 注册模型事件 finding.
     *
     * @param \leevel\event\observer|string $mixListener
     */
    public static function finding($mixListener)
    {
        static::registerEvent(static::BEFORE_FIND_EVENT, $mixListener);
    }

    /**
     * 注册模型事件 finded.
     *
     * @param \leevel\event\observer|string $mixListener
     */
    public static function finded($mixListener)
    {
        static::registerEvent(static::AFTER_FIND_EVENT, $mixListener);
    }

    /**
     * 注册模型事件 saveing.
     *
     * @param \leevel\event\observer|string $mixListener
     */
    public static function saveing($mixListener)
    {
        static::registerEvent(static::BEFORE_SAVE_EVENT, $mixListener);
    }

    /**
     * 注册模型事件 saved.
     *
     * @param \leevel\event\observer|string $mixListener
     */
    public static function saved($mixListener)
    {
        static::registerEvent(static::AFTER_SAVE_EVENT, $mixListener);
    }

    /**
     * 注册模型事件 creating.
     *
     * @param \leevel\event\observer|string $mixListener
     */
    public static function creating($mixListener)
    {
        static::registerEvent(static::BEFORE_CREATE_EVENT, $mixListener);
    }

    /**
     * 注册模型事件 created.
     *
     * @param \leevel\event\observer|string $mixListener
     */
    public static function created($mixListener)
    {
        static::registerEvent(static::AFTER_CREATE_EVENT, $mixListener);
    }

    /**
     * 注册模型事件 updating.
     *
     * @param \leevel\event\observer|string $mixListener
     */
    public static function updating($mixListener)
    {
        static::registerEvent(static::BEFORE_UPDATE_EVENT, $mixListener);
    }

    /**
     * 注册模型事件 updated.
     *
     * @param \leevel\event\observer|string $mixListener
     */
    public static function updated($mixListener)
    {
        static::registerEvent(static::AFTER_UPDATE_EVENT, $mixListener);
    }

    /**
     * 注册模型事件 deleting.
     *
     * @param \leevel\event\observer|string $mixListener
     */
    public static function deleting($mixListener)
    {
        static::registerEvent(static::BEFORE_DELETE_EVENT, $mixListener);
    }

    /**
     * 注册模型事件 deleted.
     *
     * @param \leevel\event\observer|string $mixListener
     */
    public static function deleted($mixListener)
    {
        static::registerEvent(static::AFTER_DELETE_EVENT, $mixListener);
    }

    /**
     * 注册模型事件 softDeleting.
     *
     * @param \leevel\event\observer|string $mixListener
     */
    public static function softDeleting($mixListener)
    {
        static::registerEvent(static::BEFORE_SOFT_DELETE_EVENT, $mixListener);
    }

    /**
     * 注册模型事件 softDeleted.
     *
     * @param \leevel\event\observer|string $mixListener
     */
    public static function softDeleted($mixListener)
    {
        static::registerEvent(static::AFTER_SOFT_DELETE_EVENT, $mixListener);
    }

    /**
     * 注册模型事件 softRestoring.
     *
     * @param \leevel\event\observer|string $mixListener
     */
    public static function softRestoring($mixListener)
    {
        static::registerEvent(static::BEFORE_SOFT_RESTORE_EVENT, $mixListener);
    }

    /**
     * 注册模型事件 softRestored.
     *
     * @param \leevel\event\observer|string $mixListener
     */
    public static function softRestored($mixListener)
    {
        static::registerEvent(static::AFTER_SOFT_RESTORE_EVENT, $mixListener);
    }

    /**
     * 返回模型事件处理器.
     *
     * @return \Leevel\Event\IDispatch
     */
    public static function getEventDispatch()
    {
        return static::$objDispatch;
    }

    /**
     * 设置模型事件处理器.
     *
     * @param \Leevel\Event\IDispatch $objDispatch
     */
    public static function setEventDispatch(IDispatch $objDispatch)
    {
        static::$objDispatch = $objDispatch;
    }

    /**
     * 注销模型事件.
     */
    public static function unsetEventDispatch()
    {
        static::$objDispatch = null;
    }

    /**
     * 注册模型事件.
     *
     * @param string                        $strEvent
     * @param \leevel\event\observer|string $mixListener
     */
    public static function registerEvent($strEvent, $mixListener)
    {
        if (isset(static::$objDispatch)) {
            static::isSupportEvent($strEvent);
            static::$objDispatch->listener("model.{$strEvent}:".static::class, $mixListener);
        }
    }

    /**
     * 执行模型事件.
     *
     * @param string $strEvent
     *
     * @return mixed
     */
    public function runEvent($strEvent)
    {
        if (!isset(static::$objDispatch)) {
            return true;
        }

        $this->isSupportEvent($strEvent);

        $arrArgs = func_get_args();
        array_shift($arrArgs);
        array_unshift($arrArgs, "model.{$strEvent}:".get_class($this));
        array_unshift($arrArgs, $this);

        call_user_func_array([
            $this,
            'runEvent'.ucwords($strEvent),
        ], $arrArgs);

        call_user_func_array([
            static::$objDispatch,
            'run',
        ], $arrArgs);
        unset($arrArgs);
    }

    /**
     * 验证事件是否受支持
     *
     * @param string $event
     * @param mixed  $strEvent
     *
     * @return bool
     */
    public static function isSupportEvent($strEvent)
    {
        if (!in_array($strEvent, static::getSupportEvent(), true)) {
            throw new Exception(sprintf('Event %s do not support'));
        }
    }

    /**
     * 返回受支持的事件.
     *
     * @return array
     */
    public static function getSupportEvent()
    {
        return [
            static::BEFORE_SELECT_EVENT,
            static::AFTER_SELECT_EVENT,
            static::BEFORE_FIND_EVENT,
            static::AFTER_FIND_EVENT,
            static::BEFORE_SAVE_EVENT,
            static::AFTER_SAVE_EVENT,
            static::BEFORE_CREATE_EVENT,
            static::AFTER_CREATE_EVENT,
            static::BEFORE_UPDATE_EVENT,
            static::AFTER_UPDATE_EVENT,
            static::BEFORE_DELETE_EVENT,
            static::AFTER_DELETE_EVENT,
            static::BEFORE_SOFT_DELETE_EVENT,
            static::AFTER_SOFT_DELETE_EVENT,
            static::BEFORE_SOFT_RESTORE_EVENT,
            static::AFTER_SOFT_RESTORE_EVENT,
        ];
    }

    /**
     * 返回改变.
     *
     * @return array
     */
    public function getChanged()
    {
        return $this->arrChangedProp;
    }

    /**
     * 检测是否已经改变.
     *
     * @param string $sPropsName
     *
     * @return bool
     */
    public function hasChanged($sPropsName = null)
    {
        // null 判读是否存在属性
        if (null === $sPropsName) {
            return !empty($this->arrChangedProp);
        }

        $arrPropsName = Arr::normalize($sPropsName);
        foreach ($arrPropsName as $sPropName) {
            if (isset($this->arrChangedProp[$sPropName])) {
                return true;
            }
        }

        return false;
    }

    /**
     * 清除改变属性.
     *
     * @param mixed $mixProp
     *
     * @return $this
     */
    public function clearChanged($mixProp = null)
    {
        if ($this->checkTControl()) {
            return $this;
        }
        if (null === $mixProp) {
            $this->arrChangedProp = [];
        } else {
            $mixProp = Arr::normalize($mixProp);
            foreach ($mixProp as $sProp) {
                if (isset($this->arrChangedProp[$sProp])) {
                    unset($this->arrChangedProp[$sProp]);
                }
            }
        }

        return $this;
    }

    /**
     * 返回主键字段.
     *
     * @return null|array|string
     */
    public function getPrimaryKeyName()
    {
        $arrKey = $this->getPrimaryKeyNameSource();

        return 1 === count($arrKey) ? reset($arrKey) : $arrKey;
    }

    /**
     * 返回主键字段.
     *
     * @return array
     */
    public function getPrimaryKeyNameSource()
    {
        if (null !== $this->arrPrimaryKey) {
            return $this->arrPrimaryKey;
        }

        return $this->arrPrimaryKey = $this->meta()->getPrimaryKey() ?: [];
    }

    /**
     * 返回字段名字.
     *
     * @return array
     */
    public function getField()
    {
        return $this->arrField;
    }

    /**
     * 是否存在字段.
     *
     * @param string $strFiled
     * @param mixed  $strField
     *
     * @return array
     */
    public function hasField($strField)
    {
        return in_array($strField, $this->getField(), true);
    }

    /**
     * 返回供查询的主键字段
     * 复合主键或者没有主键直接抛出异常.
     *
     * @return string|void
     */
    public function getPrimaryKeyNameForQuery()
    {
        $mixKey = $this->getPrimaryKeyName();
        if (!is_string($mixKey)) {
            throw new Exception(sprintf('Model %s do not have primary key or composite id not supported', $this->getCalledClass()));
        }

        return $mixKey;
    }

    /**
     * 返回供查询的主键字段值
     * 复合主键或者没有主键直接抛出异常.
     *
     * @return mixed
     */
    public function getPrimaryKeyForQuery()
    {
        $this->getPrimaryKeyNameForQuery();

        return $this->primaryKey();
    }

    /**
     * 设置表.
     *
     * @param string $strTable
     *
     * @return $this
     */
    public function table($strTable)
    {
        if ($this->checkTControl()) {
            return $this;
        }
        $this->strTable = $strTable;

        return $this;
    }

    /**
     * 返回设置表.
     *
     * @return string
     */
    public function getTable()
    {
        return $this->strTable;
    }

    /**
     * 设置连接.
     *
     * @param mixed $mixConnect
     *
     * @return $this
     */
    public function connect($mixConnect)
    {
        if ($this->checkTControl()) {
            return $this;
        }
        $this->mixConnect = $mixConnect;

        return $this;
    }

    /**
     * 返回设置连接.
     *
     * @return mixed
     */
    public function getConnect()
    {
        return $this->mixConnect;
    }

    /**
     * 是否自动提交表单数据.
     *
     * @param bool $booAutoPost
     *
     * @return $this
     */
    public function autoPost($booAutoPost = true)
    {
        if ($this->checkTControl()) {
            return $this;
        }
        $this->booCreateAutoPost = $booAutoPost;
        $this->booUpdateAutoPost = $booAutoPost;

        return $this;
    }

    /**
     * 写入是否自动提交表单数据.
     *
     * @param bool $booAutoPost
     *
     * @return $this
     */
    public function createAutoPost($booAutoPost = true)
    {
        if ($this->checkTControl()) {
            return $this;
        }
        $this->booCreateAutoPost = $booAutoPost;

        return $this;
    }

    /**
     * 更新是否自动提交表单数据.
     *
     * @param bool $booAutoPost
     *
     * @return $this
     */
    public function updateAutoPost($booAutoPost = true)
    {
        if ($this->checkTControl()) {
            return $this;
        }
        $this->booUpdateAutoPost = $booAutoPost;

        return $this;
    }

    /**
     * 返回是否自动提交表单数据.
     *
     * @param string $strType
     *
     * @return bool
     */
    public function getAutoPost($strType = 'create')
    {
        return 'create' === $strType ? $this->booCreateAutoPost : $this->booUpdateAutoPost;
    }

    /**
     * 是否自动填充数据.
     *
     * @param bool $booAutoFill
     *
     * @return $this
     */
    public function autoFill($booAutoFill = true)
    {
        if ($this->checkTControl()) {
            return $this;
        }
        $this->booCreateAutoFill = $booAutoFill;
        $this->booUpdateAutoFill = $booAutoFill;

        return $this;
    }

    /**
     * 写入是否自动填充数据.
     *
     * @param bool $booAutoFill
     *
     * @return $this
     */
    public function createAutoFill($booAutoFill = true)
    {
        if ($this->checkTControl()) {
            return $this;
        }
        $this->booCreateAutoFill = $booAutoFill;

        return $this;
    }

    /**
     * 更新是否自动填充数据.
     *
     * @param bool $booAutoFill
     *
     * @return $this
     */
    public function updateAutoFill($booAutoFill = true)
    {
        if ($this->checkTControl()) {
            return $this;
        }
        $this->booUpdateAutoFill = $booAutoFill;

        return $this;
    }

    /**
     * 返回是否自动填充数据.
     *
     * @param string $strType
     *
     * @return bool
     */
    public function getAutoFill($strType = 'create')
    {
        return 'create' === $strType ? $this->booCreateAutoFill : $this->booUpdateAutoFill;
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
     * 删除 hidden.
     *
     * @param array|string $mixProp
     *
     * @return $this
     */
    public function removeHidden($mixProp)
    {
        if ($this->checkTControl()) {
            return $this;
        }
        $this->arrHidden = array_diff($this->arrHidden, (array) $mixProp);

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
     * 删除 visible.
     *
     * @param array|string $mixProp
     *
     * @return $this
     */
    public function removeVisible($mixProp)
    {
        if ($this->checkTControl()) {
            return $this;
        }
        $this->arrVisible = array_diff($this->arrVisible, (array) $mixProp);

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
     * @param null|array|string $mixProp
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
     * 删除 append.
     *
     * @param array|string $mixProp
     *
     * @return $this
     */
    public function removeAppend($mixProp)
    {
        if ($this->checkTControl()) {
            return $this;
        }
        $this->arrAppend = array_diff($this->arrAppend, (array) $mixProp);

        return $this;
    }

    /**
     * 设置模型时间格式化.
     *
     * @param string $strDateFormat
     *
     * @return $this
     */
    public function setDateFormat($strDateFormat)
    {
        if ($this->checkTControl()) {
            return $this;
        }
        $this->strDateFormat = $strDateFormat;

        return $this;
    }

    /**
     * 对象转数组.
     *
     * @return array
     */
    public function toArray()
    {
        $arrProp = $this->whiteAndBlack($this->arrProp, $this->arrVisible, $this->arrHidden);

        $arrProp = array_merge($arrProp, $this->arrAppend ? array_flip($this->arrAppend) : []);
        foreach ($arrProp as $strProp => &$mixValue) {
            $mixValue = $this->getProp($strProp);
        }

        foreach ($this->getDate() as $strProp) {
            if (!isset($arrProp[$strProp])) {
                continue;
            }
            $arrProp[$strProp] = $this->serializeDate($this->asDateTime($arrProp[$strProp]));
        }

        return $arrProp;
    }

    /**
     * 黑白名单数据解析.
     *
     * @param array $arrKey
     * @param array $arrWhite
     * @param array $arrBlack
     *
     * @return array
     */
    public function whiteAndBlack(array $arrKey, array $arrWhite, array $arrBlack)
    {
        if (!empty($arrWhite)) {
            $arrKey = array_intersect_key($arrKey, array_flip($arrWhite));
        } elseif (!empty($arrBlack)) {
            $arrKey = array_diff_key($arrKey, array_flip($arrBlack));
        }

        return $arrKey;
    }

    /**
     * 创建一个 Carbon 时间对象
     *
     * @return \Carbon\Carbon
     */
    public function carbon()
    {
        return new Carbon();
    }

    /**
     * 取得新建时间字段.
     *
     * @return string
     */
    public function getCreatedAtColumn()
    {
        return static::CREATED_AT;
    }

    /**
     * 取得更新时间字段.
     *
     * @return string
     */
    public function getUpdatedAtColumn()
    {
        return static::UPDATED_AT;
    }

    /**
     * 获取需要转换为时间的属性.
     *
     * @return array
     */
    public function getDate()
    {
        return $this->booTimestamp ? array_merge($this->arrDate, [
            static::CREATED_AT,
            static::UPDATED_AT,
        ]) : $this->arrDate;
    }

    /**
     * 设置需要转换时间的属性.
     *
     * @param array $arrDate
     *
     * @return $this
     */
    public function date(array $arrDate)
    {
        if ($this->checkTControl()) {
            return $this;
        }
        $this->arrDate = $arrDate;

        return $this;
    }

    /**
     * 添加需要转换时间的属性.
     *
     * @param array|string $mixProp
     *
     * @return $this
     */
    public function addDate($mixProp)
    {
        if ($this->checkTControl()) {
            return $this;
        }
        $mixProp = is_array($mixProp) ? $mixProp : func_get_args();
        $this->arrDate = array_merge($this->arrDate, $mixProp);

        return $this;
    }

    /**
     * 是否使用默认时间.
     *
     * @return bool
     */
    public function getTimestamp()
    {
        return $this->booTimestamp;
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
     * 转换 JSON.
     *
     * @param string $strValue
     * @param bool   $booObject
     *
     * @return mixed
     */
    public function fromJson($strValue, $booObject = false)
    {
        return json_decode($strValue, !$booObject);
    }

    /**
     * 转换 Serialize.
     *
     * @param string $strValue
     *
     * @return mixed
     */
    public function fromSerialize($strValue)
    {
        return unserialize($strValue);
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
     * 是否存在转换类型.
     *
     * @param string            $strKey
     * @param null|array|string $mixType
     *
     * @return bool
     */
    public function hasConversion($strKey, $mixType = null)
    {
        if (array_key_exists($strKey, $this->getConversion())) {
            return $mixType ? in_array($this->getConversionType($strKey), (array) $mixType, true) : true;
        }

        return false;
    }

    /**
     * 获取转换类型.
     *
     * @return array
     */
    public function getConversion()
    {
        return $this->arrConversion;
    }

    /**
     * 创建一个模型集合.
     *
     * @param array $arrModel
     *
     * @return \Leevel\Collection\Collection
     */
    public function collection(array $arrModel = [])
    {
        return new Collection($arrModel);
    }

    /**
     * 创建新的实例.
     *
     * @param array  $arrProp
     * @param mixed  $mixConnect
     * @param string $strTable
     *
     * @return static
     */
    public function newInstance($arrProp = [], $mixConnect = null, $strTable = null)
    {
        return new static((array) $arrProp, $mixConnect, $strTable);
    }

    /**
     * 将时间转化为数据库存储的值
     *
     * @param \DateTime|int $mixValue
     *
     * @return string
     */
    public function fromDateTime($mixValue)
    {
        return $this->asDateTime($mixValue)->format($this->getDateFormat());
    }

    /**
     * 获取查询键值
     *
     * @return array|void
     */
    public function getKeyConditionForQuery()
    {
        if (null === (($arrPrimaryData = $this->primaryKey()))) {
            throw new Exception(sprintf('Model %s has no primary key data', $this->getCalledClass()));
        }

        if (!is_array($arrPrimaryData)) {
            $arrPrimaryData = [
                $this->getPrimaryKeyNameForQuery() => $arrPrimaryData,
            ];
        }

        return $arrPrimaryData;
    }

    /**
     * 设置查询 select.
     *
     * @param mixed $objSelectForQuery
     *
     * @return $this
     */
    public function setSelectForQuery($objSelectForQuery)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->objSelectForQuery = $objSelectForQuery;

        return $this;
    }

    /**
     * 查询 select.
     *
     * @return \Leevel\Database\Select
     */
    public function getSelectForQuery()
    {
        return $this->objSelectForQuery;
    }

    /**
     * 返回数据库查询集合对象
     *
     * @return \Leevel\Database\IConnect
     */
    public function getClassCollectionQuerySource()
    {
        return $this->getQuery()->asClass($this->getCalledClass())->asCollection()->registerCallSelect(new select($this));
    }

    /**
     * 返回数据库查询集合对象
     *
     * @return \Leevel\Database\IConnect
     */
    public function getClassCollectionQuery()
    {
        return $this->getSelectForQuery() ?: $this->getClassCollectionQuerySource();
    }

    /**
     * 返回数据库查询对象
     *
     * @return \Leevel\Database\IConnect
     */
    public function getQuery()
    {
        return $this->meta()->getSelect();
    }

    /**
     * 返回模型类的 meta 对象
     *
     * @return Meta
     */
    public function meta()
    {
        return meta::instance($this->strTable, $this->mixConnect);
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
        return $this->hasProp($offset);
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
        return $this->forceProp($offset, $value);
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
        return $this->getProp($offset);
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
        return $this->deleteProp($offset);
    }

    /**
     * 初始化字段名字.
     */
    protected function initField()
    {
        $this->arrField = $this->meta()->getField();
    }

    /**
     * 分析默认模型表.
     *
     * @return string
     */
    protected function parseTableByClass()
    {
        if (!$this->strTable) {
            $strTable = $this->getCalledClass();
            $strTable = explode('\\', $strTable);
            $this->strTable = array_pop($strTable);
        }
    }

    /**
     * 保存统一入口.
     *
     * @param strint     $sSaveMethod
     * @param null|array $arrData
     *
     * @return mixed
     */
    protected function saveEntry($sSaveMethod = 'save', $arrData = null)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        if (is_array($arrData) && $arrData) {
            $this->forceProps($arrData);
        }

        $this->runEvent(static::BEFORE_SAVE_EVENT);

        // 程序通过内置方法统一实现
        switch (strtolower($sSaveMethod)) {
            case 'create':
                $mixResult = $this->createReal();

                break;
            case 'update':
                $mixResult = $this->updateReal();

                break;
            case 'replace':
                $mixResult = $this->replaceReal();

                break;
            case 'save':
            default:
                $arrPrimaryData = $this->primaryKey(true);

                // 复合主键的情况下，则使用 replace 方式
                if (is_array($arrPrimaryData)) {
                    $mixResult = $this->replaceReal();
                }

                // 单一主键
                else {
                    if (empty($arrPrimaryData)) {
                        $mixResult = $this->createReal();
                    } else {
                        $mixResult = $this->updateReal();
                    }
                }

                break;
        }

        $this->runEvent(static::AFTER_SAVE_EVENT);

        return $mixResult;
    }

    /**
     * 添加数据.
     *
     * @return mixed
     */
    protected function createReal()
    {
        $this->parseAutoPost('create');
        $this->parseAutoFill('create');

        $arrPropKey = $this->whiteAndBlack(array_keys($this->arrProp), $this->arrFillWhite, $this->arrFillBlack);
        if ($arrPropKey) {
            $arrPropKey = $this->whiteAndBlack($arrPropKey, $this->arrCreateFillWhite, $this->arrCreateFillBlack);
        }

        $arrSaveData = [];
        foreach ($this->arrProp as $sPropName => $mixValue) {
            if (null === $mixValue || !in_array($sPropName, $arrPropKey, true)) {
                continue;
            }
            $arrSaveData[$sPropName] = $mixValue;
        }

        if (!$arrSaveData) {
            if (null === (($arrPrimaryKey = $this->getPrimaryKeyNameSource()))) {
                throw new Exception(sprintf('Model %s has no primary key', $this->getCalledClass()));
            }

            foreach ($arrPrimaryKey as $strPrimaryKey) {
                $arrSaveData[$strPrimaryKey] = null;
            }
        }

        $this->runEvent(static::BEFORE_CREATE_EVENT, $arrSaveData);

        $arrLastInsertId = $this->meta()->insert($arrSaveData);
        $this->arrProp = array_merge($this->arrProp, $arrLastInsertId);
        $this->clearChanged();

        $this->runEvent(static::AFTER_CREATE_EVENT, $arrSaveData);

        return $this->mixLastInsertIdOrRowCount = $this->mixLastInsertId = reset($arrLastInsertId);
    }

    /**
     * 更新数据.
     *
     * @return int
     */
    protected function updateReal()
    {
        $this->parseAutoPost('update');
        $this->parseAutoFill('update');

        $arrPropKey = $this->whiteAndBlack(array_keys($this->arrProp), $this->arrFillWhite, $this->arrFillBlack);
        if ($arrPropKey) {
            $arrPropKey = $this->whiteAndBlack($arrPropKey, $this->arrUpdateFillWhite, $this->arrUpdateFillBlack);
        }

        $arrSaveData = [];
        foreach ($this->arrProp as $sPropName => $mixValue) {
            if (!in_array($sPropName, $this->arrChangedProp, true) || !in_array($sPropName, $arrPropKey, true)) {
                continue;
            }
            $arrSaveData[$sPropName] = $mixValue;
        }

        $booChange = false;

        if ($arrSaveData) {
            $arrCondition = [];
            foreach ($this->getPrimaryKeyNameSource() as $sFieldName) {
                if (isset($arrSaveData[$sFieldName])) {
                    unset($arrSaveData[$sFieldName]);
                }
                if (!empty($this->arrProp[$sFieldName])) {
                    $arrCondition[$sFieldName] = $this->arrProp[$sFieldName];
                }
            }

            if (!empty($arrSaveData) && !empty($arrCondition)) {
                $this->runEvent(static::BEFORE_UPDATE_EVENT, $arrSaveData, $arrCondition);
                $intNum = $this->meta()->update($arrCondition, $arrSaveData);
                $booChange = true;
            }
        }

        if (!$booChange) {
            $this->runEvent(static::BEFORE_UPDATE_EVENT, null, null);
        }

        $this->clearChanged();

        $this->runEvent(static::AFTER_UPDATE_EVENT);

        return $this->mixLastInsertIdOrRowCount = $this->intRowCount = isset($intNum) ? $intNum : 0;
    }

    /**
     * 模拟 replace 数据.
     *
     * @return mixed
     */
    protected function replaceReal()
    {
        try {
            return $this->createReal();
        } catch (Exception $oE) {
            return $this->updateReal();
        }
    }

    /**
     * 自动提交表单数据.
     *
     * @param string $strType
     */
    protected function parseAutoPost($strType = 'create')
    {
        if (false === $this->getAutoPost($strType) || empty($_POST)) {
            return;
        }

        $arrPost = $this->whiteAndBlack(array_keys($_POST), $this->arrPostWhite, $this->arrPostBlack);
        if ($arrPost) {
            if ('create' === $strType) {
                $arrPost = $this->whiteAndBlack($arrPost, $this->arrCreatePostWhite, $this->arrCreatePostBlack);
            } else {
                $arrPost = $this->whiteAndBlack($arrPost, $this->arrUpdatePostWhite, $this->arrUpdatePostBlack);
            }
        }

        if ($arrPost) {
            $arrValue = [];
            foreach ($_POST as $strKey => $mixValue) {
                if (in_array($strKey, $arrPost, true)) {
                    $arrTemp[$strKey] = $mixValue;
                }
            }

            if ($arrValue) {
                foreach ($this->meta()->fieldsProps($arrValue) as $strField => $mixValue) {
                    if (!in_array($strField, $this->arrChangedProp, true)) {
                        $this->arrProp[$strField] = trim($mixValue);
                        $this->arrChangedProp[] = $strField;
                    }
                }
            }
        }
    }

    /**
     * 自动填充.
     *
     * @param string $strType
     */
    protected function parseAutoFill($strType = 'create')
    {
        if (false === $this->getAutoFill($strType)) {
            return;
        }

        if ('create' === $strType) {
            $arrFill = array_merge($this->arrAutoFill, $this->arrCreateFill);
        } else {
            $arrFill = array_merge($this->arrAutoFill, $this->arrUpdateFill);
        }

        if (!$arrFill) {
            return;
        }

        foreach ($arrFill as $mixKey => $mixValue) {
            if (is_int($mixKey)) {
                $mixKey = $mixValue;
                $mixValue = null;
            }
            $this->forceProp($mixKey, $mixValue);
        }
    }

    /**
     * 从关联中读取数据.
     *
     * @param string $strPropName
     *
     * @return mixed
     */
    protected function parseDataFromRelation($strPropName)
    {
        $oRelation = $this->{$strPropName}();
        if (!($oRelation instanceof Relation)) {
            throw new Exception(sprintf('Relation prop must return a type of %s', 'Leevel\Mvc\Relation\Relation'));
        }

        return $this->arrRelationProp[$strPropName] = $oRelation->sourceQuery();
    }

    /**
     * 模型快捷事件 selecting.
     */
    protected function runEventSelecting()
    {
    }

    /**
     * 模型快捷事件 selected.
     */
    protected function runEventSelected()
    {
    }

    /**
     * 模型快捷事件 finding.
     */
    protected function runEventFinding()
    {
    }

    /**
     * 模型快捷事件 finded.
     */
    protected function runEventFinded()
    {
    }

    /**
     * 模型快捷事件 saveing.
     */
    protected function runEventSaveing()
    {
    }

    /**
     * 模型快捷事件 saved.
     */
    protected function runEventSaved()
    {
    }

    /**
     * 模型快捷事件 creating.
     */
    protected function runEventCreating()
    {
    }

    /**
     * 模型快捷事件 created.
     */
    protected function runEventCreated()
    {
    }

    /**
     * 模型快捷事件 updating.
     */
    protected function runEventUpdating()
    {
    }

    /**
     * 模型快捷事件 updated.
     */
    protected function runEventUpdated()
    {
    }

    /**
     * 模型快捷事件 deleting.
     */
    protected function runEventDeleting()
    {
    }

    /**
     * 模型快捷事件 deleted.
     */
    protected function runEventDeleted()
    {
    }

    /**
     * 模型快捷事件 softDeleting.
     */
    protected function runEventSoftDeleting()
    {
    }

    /**
     * 模型快捷事件 softDeleted.
     */
    protected function runEventSoftDeleted()
    {
    }

    /**
     * 模型快捷事件 softRestoring.
     */
    protected function runEventSoftRestoring()
    {
    }

    /**
     * 模型快捷事件 softRestored.
     */
    protected function runEventSoftRestored()
    {
    }

    /**
     * 获取转换类型.
     *
     * @param string $strKey
     *
     * @return string
     */
    protected function getConversionType($strKey)
    {
        return trim(strtolower($this->getConversion()[$strKey]));
    }

    /**
     * 属性是否可以被转换为属性.
     *
     * @param string $strProp
     *
     * @return bool
     */
    protected function isDateConversion($strProp)
    {
        return $this->hasConversion($strProp, [
            'date',
            'datetime',
        ]);
    }

    /**
     * 属性是否可以转换为 JSON.
     *
     * @param string $strProp
     *
     * @return bool
     */
    protected function isJsonConversion($strProp)
    {
        return $this->hasConversion($strProp, [
            'array',
            'json',
            'object',
            'collection',
        ]);
    }

    /**
     * 将变量转为 JSON.
     *
     * @param mixed $mixValue
     *
     * @return string
     */
    protected function asJson($mixValue)
    {
        return json_encode($mixValue);
    }

    /**
     * 转换属性.
     *
     * @param string $strKey
     * @param mixed  $mixValue
     *
     * @return mixed
     */
    protected function conversionProp($strKey, $mixValue)
    {
        if (null === $mixValue) {
            return $mixValue;
        }

        switch ($this->getConversionType($strKey)) {
            case 'int':
            case 'integer':
                return (int) $mixValue;
            case 'real':
            case 'float':
            case 'double':
                return (float) $mixValue;
            case 'string':
                return (string) $mixValue;
            case 'bool':
            case 'boolean':
                return (bool) $mixValue;
            case 'object':
                return $this->fromJson($mixValue, true);
            case 'array':
            case 'json':
                return $this->fromJson($mixValue);
            case 'collection':
                return new Collection($this->fromJson($mixValue));
            case 'date':
            case 'datetime':
                return $this->asDateTime($mixValue);
            case 'timestamp':
                return $this->asTimeStamp($mixValue);
            default:
                return $mixValue;
        }
    }

    /**
     * 设置是否处于强制更新属性的.
     *
     * @param bool $booForceProp
     *
     * @return bool
     */
    protected function setForceProp($booForceProp = true)
    {
        $this->booForceProp = $booForceProp;
    }

    /**
     * 返回是否处于强制更新属性的.
     *
     * @return bool
     */
    protected function getForceProp()
    {
        return $this->booForceProp;
    }

    /**
     * 转换为时间对象
     *
     * @param mixed $mixValue
     *
     * @return \Carbon\Carbon
     */
    protected function asDateTime($mixValue)
    {
        if ($mixValue instanceof Carbon) {
            return $mixValue;
        }

        if ($mixValue instanceof DateTimeInterface) {
            return new Carbon($mixValue->format('Y-m-d H:i:s.u'), $mixValue->getTimeZone());
        }

        if (is_numeric($mixValue)) {
            return Carbon::createFromTimestamp($mixValue);
        }

        if (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $mixValue)) {
            return Carbon::createFromFormat('Y-m-d', $mixValue)->startOfDay();
        }

        return Carbon::createFromFormat($this->getDateFormat(), $mixValue);
    }

    /**
     * 转为 unix 时间风格
     *
     * @param mixed $mixValue
     *
     * @return int
     */
    protected function asTimeStamp($mixValue)
    {
        return $this->asDateTime($mixValue)->getTimestamp();
    }

    /**
     * 序列化时间.
     *
     * @param \DateTime $objDate
     *
     * @return string
     */
    protected function serializeDate(DateTime $objDate)
    {
        return $objDate->format($this->getDateFormat());
    }

    /**
     * 返回属性时间格式化.
     *
     * @return string
     */
    protected function getDateFormat()
    {
        return $this->strDateFormat ?: 'Y-m-d H:i:s';
    }

    /**
     * 删除模型.
     *
     * @return int
     */
    protected function deleteModelByKey()
    {
        return $this->getQuery()->where($this->getKeyConditionForQuery())->delete();
    }

    /**
     * 获取调用 class.
     *
     * @return string
     */
    protected function getCalledClass()
    {
        return static::class;
    }

    /**
     * 返回下划线式命名.
     *
     * @param string $strProp
     *
     * @return string
     */
    protected function getCamelizeProp($strProp)
    {
        if (isset(static::$arrCamelizeProp[$strProp])) {
            return static::$arrCamelizeProp[$strProp];
        }

        return static::$arrCamelizeProp[$strProp] = ucwords(Str::camelize($strProp));
    }
}
