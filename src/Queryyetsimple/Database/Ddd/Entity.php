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
use Carbon\Carbon;
use Closure;
use DateTime;
use DateTimeInterface;
use Exception;
use InvalidArgumentException;
use JsonSerializable;
use Leevel\Collection\Collection;
use Leevel\Database\Ddd\Relation\BelongsTo;
use Leevel\Database\Ddd\Relation\HasMany;
use Leevel\Database\Ddd\Relation\ManyMany;
use Leevel\Database\Ddd\Relation\Relation;
use Leevel\Event\IDispatch;
use Leevel\Flow\TControl;
use Leevel\Support\Arr;
use Leevel\Support\IArray;
use Leevel\Support\IJson;
use Leevel\Support\Str;
use Leevel\Support\TMacro;
use Leevel\Support\TSerialize;

/**
 * 模型实体 Object Relational Mapping.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.04.27
 *
 * @version 1.0
 */
abstract class Entity implements IEntity, IArray, IJson, JsonSerializable, ArrayAccess
{
    use TSerialize;
    use TMacro {
        __callStatic as macroCallStatic;
        __call as macroCall;
    }
    use TControl;

    /**
     * 此模型实体的连接名称.
     *
     * @var mixed
     */
    protected $mixConnect;

    /**
     * 模型实体属性.
     *
     * @var array
     */
    protected $arrProp = [];

    /**
     * 需要保存的模型实体属性.
     *
     * @var array
     */
    protected $arrCreatedProp = [];

    /**
     * 改变的模型实体属性.
     *
     * @var array
     */
    protected $arrChangedProp = [];

    /**
     * 黑白名单.
     *
     * @var array
     */
    protected $blackWhites = [
        'construct' => [
            'white' => [],
            'black' => [],
        ],
        'fill' => [
            'white' => [],
            'black' => [],
        ],
        'create_fill' => [
            'white' => [],
            'black' => [],
        ],
        'update_fill' => [
            'white' => [],
            'black' => [],
        ],
    ];

    /**
     * 写入是否自动填充.
     *
     * @var bool
     */
    protected $createFill = true;

    /**
     * 更新是否自动填充.
     *
     * @var bool
     */
    protected $updateFill = true;

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
     * 模型实体的日期字段保存格式.
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
     * 模型实体事件处理器.
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
     * 最后插入记录或者响应记录.
     *
     * @var mixed
     */
    protected $lastResult;

    /**
     * 是否处于强制改变属性中.
     *
     * @var bool
     */
    protected $booForceProp = false;

    /**
     * 持久化基础层
     *
     * @var \Closure
     */
    protected $flush;

    /**
     * 即将持久化数据.
     *
     * @var array
     */
    protected $flushData;

    /**
     * 构造函数.
     *
     * @param null|array $arrData
     * @param mixed      $mixConnect
     */
    public function __construct($arrData = null, $mixConnect = null)
    {
        $className = get_class($this);

        foreach ([
            'TABLE', 'PRIMARY_KEY',
            'AUTO_INCREMENT', 'STRUCT',
        ] as $item) {
            if (!defined($className.'::'.$item)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'The entity const %s is not defined.',
                        $item
                    )
                );
            }
        }

        foreach (static::STRUCT as $field => $v) {
            foreach ([
                'construct', 'fill',
                'create_fill', 'update_fill',
            ] as $type) {
                foreach (['black', 'white'] as $bw) {
                    if (!empty($v[$type.'_'.$bw])) {
                        $this->blackWhites[$type][$bw][] = $field;
                    }
                }
            }
        }

        if (null !== $mixConnect) {
            $this->mixConnect = $mixConnect;
        }

        if (is_array($arrData) && $arrData) {
            foreach (
                $this->normalizeBlackAndWhite(
                    $arrData,
                    'construct'
                ) as $strProp => $value) {
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
     * 将模型实体转化为 JSON.
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
     * 返回最后插入记录或者响应记录.
     *
     * @return mixed
     */
    public function lastResult()
    {
        return $this->lastResult;
    }

    /**
     * 根据主键 ID 删除模型实体.
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

        foreach ($objInstance->whereIn($objInstance->getPrimaryKeyNameForQuery(), $mixId)->getAll() as $objEntity) {
            if ($objEntity->delete()) {
                $intCount++;
            }
        }

        return $intCount;
    }

    /**
     * 删除模型实体.
     *
     * @return int
     */
    public function delete()
    {
        if (null === $this->getPrimaryKeyName()) {
            throw new Exception(
                sprintf(
                    'Entity %s has no primary key',
                    $this->getCalledClass()
                )
            );
        }

        $this->runEvent(static::BEFORE_DELETE_EVENT);

        $intNum = $this->deleteEntityByKey();

        $this->runEvent(static::AFTER_DELETE_EVENT);

        return $intNum;
    }

    /**
     * 数据持久化数据.
     *
     * @return mixed
     */
    public function flush()
    {
        if (!$this->flush) {
            return 0;
        }

        $result = call_user_func_array($this->flush, $this->flushData);

        $this->setFlush(null);
        $this->setFlushData(null);

        $this->runEvent(static::AFTER_SAVE_EVENT);

        return $result;
    }

    /**
     * 设置持久化基础层
     *
     * @param null|\Closure $flush
     */
    public function setFlush(?Closure $flush)
    {
        $this->flush = $flush;
    }

    /**
     * 设置数据持久化数据.
     *
     * @param null|array $data
     */
    public function setFlushData(?array $data)
    {
        $this->flushData = $data;
    }

    /**
     * 数据持久化数据.
     *
     * @return null|\Closure
     */
    public function getFlush()
    {
        return $this->flush;
    }

    /**
     * 获取数据持久化数据.
     *
     * @return null|array
     */
    public function getFlushData()
    {
        return $this->flushData;
    }

    /**
     * 获取主键
     * 唯一标识符.
     *
     * @param bool $update
     *
     * @return mixed
     */
    public function id(bool $update = false)
    {
        $arrPrimaryData = [];

        $arrPrimaryKey = $this->getPrimaryKeyNameSource();

        foreach ($arrPrimaryKey as $sPrimaryKey) {
            if (!$this->{$this->normalizeCamelizeProp($sPrimaryKey)}) {
                continue;
            }

            if (true === $update) {
                if (!in_array($sPrimaryKey, $this->arrChangedProp, true)) {
                    $arrPrimaryData[$sPrimaryKey] = $this->{$this->normalizeCamelizeProp($sPrimaryKey)};
                }
            } else {
                $arrPrimaryData[$sPrimaryKey] = $this->{$this->normalizeCamelizeProp($sPrimaryKey)};
            }
        }

        // 复合主键，但是数据不完整则忽略
        if (count($arrPrimaryKey) > 1 &&
            count($arrPrimaryKey) !== count($arrPrimaryData)) {
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

        if (!$this->hasField($strProp)) {
            throw new BadMethodCallException(
                sprintf(
                    'Entity %s field %s do not exist.',
                    get_class($this),
                    $strProp
                )
            );
        }

        //$mixValue = $this->meta()->fieldsProp($strProp, $mixValue);

        if (null === $mixValue &&
            ($strCamelize = 'set'.ucfirst($this->normalizeCamelizeProp($strProp)).'Prop') &&
                method_exists($this, $strCamelize)) {
            if (null === (($mixValue = $this->{$strCamelize}($this->getProp($strProp))))) {
                $mixValue = $this->getProp($strProp);
            }
        } elseif ($mixValue &&
            (in_array($strProp, $this->getDate(), true) ||
                $this->isDateConversion($strProp))) {
            $mixValue = $this->fromDateTime($mixValue);
        } elseif (null !== $mixValue &&
            $this->isJsonConversion($strProp)) {
            $needJson = true;
            $isObject = $this->isObjectConversion($strProp);

            if (is_string($mixValue)) {
                // 将类似于 ["hello","world"] 字符串先转数组
                // 便于通过 JSON_FORCE_OBJECT 强制编码为对象 JSON
                if ($isObject) {
                    $mixValue = $this->fromJson($mixValue, true);
                } else {
                    $needJson = false;
                }
            }

            if ($needJson) {
                $mixValue = $this->asJson(
                    $mixValue,
                    $isObject ? JSON_FORCE_OBJECT : 0
                );
            }
        }

        $this->{$this->normalizeCamelizeProp($strProp)} = $mixValue;

        if (!in_array($strProp, $this->arrCreatedProp, true)) {
            $this->arrCreatedProp[] = $strProp;
        }

        if ($this->getForceProp() &&
            !in_array($strProp, $this->arrChangedProp, true) &&
            empty(static::STRUCT[$strProp]['readonly'])) {
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
        if (!$this->hasField($strPropName)) {
            // throw new Exception(
            //     sprintf(
            //         'Entity %s database table %s has no field %s',
            //         $this->getCalledClass(),
            //         $this->getTable(),
            //         $strPropName
            //     )
            // );
        }

        if (in_array($strPropName, $this->arrCreatedProp, true)) {
            $mixValue = $this->{$this->normalizeCamelizeProp($strPropName)};
        } elseif (method_exists($this, $strPropName.'pp')) {
            return $this->loadRelationProp($strPropName);
        } else {
            $mixValue = null;
        }

        if (($strCamelize = 'get'.ucfirst($this->normalizeCamelizeProp($strPropName)).'Prop') &&
            method_exists($this, $strCamelize)) {
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
     * 取得模型实体数据.
     *
     * @param string $sPropName
     *
     * @return mixed
     */
    public function getRelationProp($sPropName)
    {
        return $this->hasRelationProp($sPropName) ?
        $this->arrRelationProp[$sPropName] :

        null;
    }

    /**
     * 关联模型实体数据是否载入.
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
     * @return \Leevel\Database\Ddd\select
     */
    public static function with($mixRelation)
    {
        if (is_string($mixRelation)) {
            $mixRelation = func_get_args();
        }

        return (new static())->
        getClassCollectionQuery()->

        with($mixRelation);
    }

    /**
     * 一对一关联.
     *
     * @param string $strRelatedEntity
     * @param string $strTargetKey
     * @param string $strSourceKey
     *
     * @return \Leevel\Database\Ddd\Relation\HasOne|void
     */
    public function hasOne($strRelatedEntity, $strTargetKey = null, $strSourceKey = null)
    {
        $objEntity = new $strRelatedEntity();
        $strTargetKey = $strTargetKey ?: $this->getTargetKey();
        $strSourceKey = $strSourceKey ?: $this->getPrimaryKeyNameForQuery();

        if (!$objEntity->hasField($strTargetKey)) {
            throw new Exception(
                sprintf(
                    'Entity %s database table %s has no field %s',
                    $strRelatedEntity,
                    $objEntity->getTable(),
                    $strTargetKey
                )
            );
        }

        if (!$this->hasField($strSourceKey)) {
            throw new Exception(
                sprintf(
                    'Entity %s database table %s has no field %s',
                    $this->getCalledClass(),
                    $this->getTable(),
                    $strSourceKey
                )
            );
        }

        return new HasOne(
            $objEntity,
            $this,
            $strTargetKey,
            $strSourceKey
        );
    }

    /**
     * 定义从属关系.
     *
     * @param string $strRelatedEntity
     * @param string $strTargetKey
     * @param string $strSourceKey
     *
     * @return \Leevel\Database\Ddd\Relation\BelongsTo|void
     */
    public function belongsTo($strRelatedEntity, $strTargetKey = null, $strSourceKey = null)
    {
        $objEntity = new $strRelatedEntity();

        $strTargetKey = $strTargetKey ?: $objEntity->getPrimaryKeyNameForQuery();
        $strSourceKey = $strSourceKey ?: $objEntity->getTargetKey();

        if (!$objEntity->hasField($strTargetKey)) {
            throw new Exception(
                sprintf(
                    'Entity %s has no field %sEntity %s database table %s has no field %s',
                    $strRelatedEntity,
                    $objEntity->getTable(),
                    $strTargetKey
                )
            );
        }

        if (!$this->hasField($strSourceKey)) {
            throw new Exception(
                sprintf(
                    'Entity %s database table %s has no field %s',
                    $this->getCalledClass(),
                    $this->getTable(),
                    $strSourceKey
                )
            );
        }

        return new BelongsTo(
            $objEntity,
            $this,
            $strTargetKey,
            $strSourceKey
        );
    }

    /**
     * 一对多关联.
     *
     * @param string $strRelatedEntity
     * @param string $strTargetKey
     * @param string $strSourceKey
     *
     * @return \Leevel\Database\Ddd\Relation\HasMany|void
     */
    public function hasMany($strRelatedEntity, $strTargetKey = null, $strSourceKey = null)
    {
        $objEntity = new $strRelatedEntity();
        $strTargetKey = $strTargetKey ?: $this->getTargetKey();
        $strSourceKey = $strSourceKey ?: $this->getPrimaryKeyNameForQuery();

        if (!$objEntity->hasField($strTargetKey)) {
            throw new Exception(
                sprintf(
                    'Entity %s database table %s has no field %s',
                    $strRelatedEntity,
                    $objEntity->getTable(),
                    $strTargetKey
                )
            );
        }

        if (!$this->hasField($strSourceKey)) {
            throw new Exception(
                sprintf(
                    'Entity %s database table %s has no field %s',
                    $this->getCalledClass(),
                    $this->getTable(),
                    $strSourceKey
                )
            );
        }

        return new HasMany(
            $objEntity,
            $this,
            $strTargetKey,
            $strSourceKey
        );
    }

    /**
     * 多对多关联.
     *
     * @param string $strRelatedEntity
     * @param string $strMiddleEntity
     * @param string $strTargetKey
     * @param string $strSourceKey
     * @param string $strMiddleTargetKey
     * @param string $strMiddleSourceKey
     *
     * @return \Leevel\Database\Ddd\Relation\HasMany|void
     */
    public function manyMany($strRelatedEntity, $strMiddleEntity = null, $strTargetKey = null, $strSourceKey = null, $strMiddleTargetKey = null, $strMiddleSourceKey = null)
    {
        $objEntity = new $strRelatedEntity();

        $strMiddleEntity = $strMiddleEntity ?: $this->getMiddleEntity($objEntity);
        $objMiddleEntity = new $strMiddleEntity();

        $strTargetKey = $strTargetKey ?: $objEntity->getPrimaryKeyNameForQuery();
        $strMiddleTargetKey = $strMiddleTargetKey ?: $objEntity->getTargetKey();

        $strSourceKey = $strSourceKey ?: $this->getPrimaryKeyNameForQuery();
        $strMiddleSourceKey = $strMiddleSourceKey ?: $this->getTargetKey();

        if (!$objEntity->hasField($strTargetKey)) {
            throw new Exception(
                sprintf(
                    'Entity %s database table %s has no field %s',
                    $strRelatedEntity,
                    $objEntity->getTable(),
                    $strTargetKey
                )
            );
        }

        if (!$objMiddleEntity->hasField($strMiddleTargetKey)) {
            throw new Exception(
                sprintf(
                    'Entity %s database table %s has no field %s',
                    $strMiddleEntity,
                    $objMiddleEntity->getTable(),
                    $strMiddleTargetKey
                )
            );
        }

        if (!$this->hasField($strSourceKey)) {
            throw new Exception(
                sprintf(
                    'Entity %s database table %s has no field %s',
                    $this->getCalledClass(),
                    $this->getTable(),
                    $strSourceKey
                )
            );
        }

        if (!$objMiddleEntity->hasField($strMiddleSourceKey)) {
            throw new Exception(
                sprintf(
                    'Entity %s database table %s has no field %s',
                    $strMiddleEntity,
                    $objMiddleEntity->getTable(),
                    $strMiddleSourceKey
                )
            );
        }

        return new ManyMany(
            $objEntity,
            $this,
            $objMiddleEntity,
            $strTargetKey,
            $strSourceKey,
            $strMiddleTargetKey,
            $strMiddleSourceKey
        );
    }

    /**
     * 中间表带命名空间完整名字.
     *
     * @param \Leevel\Database\Ddd\IEntity $objRelatedEntity
     *
     * @return string
     */
    public function getMiddleEntity($objRelatedEntity)
    {
        $arrClass = explode('\\', $this->getCalledClass());
        array_pop($arrClass);
        $arrClass[] = $this->getMiddleTable($objRelatedEntity);

        return implode('\\', $arrClass);
    }

    /**
     * 取得中间表名字.
     *
     * @param \Leevel\Database\Ddd\IEntity $objRelatedEntity
     *
     * @return string
     */
    public function getMiddleTable($objRelatedEntity)
    {
        return $this->getTable().'_'.$objRelatedEntity->getTable();
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
     * 注册模型实体事件 selecting.
     *
     * @param \leevel\event\observer|string $mixListener
     */
    public static function selecting($mixListener)
    {
        static::registerEvent(
            static::BEFORE_SELECT_EVENT,
            $mixListener
        );
    }

    /**
     * 注册模型实体事件 selected.
     *
     * @param \leevel\event\observer|string $mixListener
     */
    public static function selected($mixListener)
    {
        static::registerEvent(
            static::AFTER_SELECT_EVENT,
            $mixListener
        );
    }

    /**
     * 注册模型实体事件 finding.
     *
     * @param \leevel\event\observer|string $mixListener
     */
    public static function finding($mixListener)
    {
        static::registerEvent(
            static::BEFORE_FIND_EVENT,
            $mixListener
        );
    }

    /**
     * 注册模型实体事件 finded.
     *
     * @param \leevel\event\observer|string $mixListener
     */
    public static function finded($mixListener)
    {
        static::registerEvent(
            static::AFTER_FIND_EVENT,
            $mixListener
        );
    }

    /**
     * 注册模型实体事件 saveing.
     *
     * @param \leevel\event\observer|string $mixListener
     */
    public static function saveing($mixListener)
    {
        static::registerEvent(
            static::BEFORE_SAVE_EVENT,
            $mixListener
        );
    }

    /**
     * 注册模型实体事件 saved.
     *
     * @param \leevel\event\observer|string $mixListener
     */
    public static function saved($mixListener)
    {
        static::registerEvent(
            static::AFTER_SAVE_EVENT,
            $mixListener
        );
    }

    /**
     * 注册模型实体事件 creating.
     *
     * @param \leevel\event\observer|string $mixListener
     */
    public static function creating($mixListener)
    {
        static::registerEvent(
            static::BEFORE_CREATE_EVENT,
            $mixListener
        );
    }

    /**
     * 注册模型实体事件 created.
     *
     * @param \leevel\event\observer|string $mixListener
     */
    public static function created($mixListener)
    {
        static::registerEvent(
            static::AFTER_CREATE_EVENT,
            $mixListener
        );
    }

    /**
     * 注册模型实体事件 updating.
     *
     * @param \leevel\event\observer|string $mixListener
     */
    public static function updating($mixListener)
    {
        static::registerEvent(
            static::BEFORE_UPDATE_EVENT,
            $mixListener
        );
    }

    /**
     * 注册模型实体事件 updated.
     *
     * @param \leevel\event\observer|string $mixListener
     */
    public static function updated($mixListener)
    {
        static::registerEvent(
            static::AFTER_UPDATE_EVENT,
            $mixListener
        );
    }

    /**
     * 注册模型实体事件 deleting.
     *
     * @param \leevel\event\observer|string $mixListener
     */
    public static function deleting($mixListener)
    {
        static::registerEvent(
            static::BEFORE_DELETE_EVENT,
            $mixListener
        );
    }

    /**
     * 注册模型实体事件 deleted.
     *
     * @param \leevel\event\observer|string $mixListener
     */
    public static function deleted($mixListener)
    {
        static::registerEvent(
            static::AFTER_DELETE_EVENT,
            $mixListener
        );
    }

    /**
     * 注册模型实体事件 softDeleting.
     *
     * @param \leevel\event\observer|string $mixListener
     */
    public static function softDeleting($mixListener)
    {
        static::registerEvent(
            static::BEFORE_SOFT_DELETE_EVENT,
            $mixListener
        );
    }

    /**
     * 注册模型实体事件 softDeleted.
     *
     * @param \leevel\event\observer|string $mixListener
     */
    public static function softDeleted($mixListener)
    {
        static::registerEvent(
            static::AFTER_SOFT_DELETE_EVENT,
            $mixListener
        );
    }

    /**
     * 注册模型实体事件 softRestoring.
     *
     * @param \leevel\event\observer|string $mixListener
     */
    public static function softRestoring($mixListener)
    {
        static::registerEvent(
            static::BEFORE_SOFT_RESTORE_EVENT,
            $mixListener
        );
    }

    /**
     * 注册模型实体事件 softRestored.
     *
     * @param \leevel\event\observer|string $mixListener
     */
    public static function softRestored($mixListener)
    {
        static::registerEvent(
            static::AFTER_SOFT_RESTORE_EVENT,
            $mixListener
        );
    }

    /**
     * 返回模型实体事件处理器.
     *
     * @return \Leevel\Event\IDispatch
     */
    public static function getEventDispatch()
    {
        return static::$objDispatch;
    }

    /**
     * 设置模型实体事件处理器.
     *
     * @param \Leevel\Event\IDispatch $objDispatch
     */
    public static function setEventDispatch(IDispatch $objDispatch)
    {
        static::$objDispatch = $objDispatch;
    }

    /**
     * 注销模型实体事件.
     */
    public static function unsetEventDispatch()
    {
        static::$objDispatch = null;
    }

    /**
     * 注册模型实体事件.
     *
     * @param string                        $strEvent
     * @param \leevel\event\observer|string $mixListener
     */
    public static function registerEvent($strEvent, $mixListener)
    {
        if (isset(static::$objDispatch)) {
            static::isSupportEvent($strEvent);

            static::$objDispatch->listener(
                "entity.{$strEvent}:".static::class,
                $mixListener
            );
        }
    }

    /**
     * 执行模型实体事件.
     *
     * @param string $strEvent
     */
    public function runEvent($strEvent)
    {
        $this->isSupportEvent($strEvent);

        $arrArgs = func_get_args();
        array_shift($arrArgs);
        array_unshift($arrArgs, "entity.{$strEvent}:".get_class($this));
        array_unshift($arrArgs, $this);

        if (method_exists($this, $eventInner = 'runEvent'.ucwords($strEvent))) {
            call_user_func_array([
                $this,
                $eventInner,
            ], $arrArgs);
        }

        if (!isset(static::$objDispatch)) {
            return;
        }

        call_user_func_array([
            static::$objDispatch,
            'run',
        ], $arrArgs);
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
            throw new Exception(
                sprintf('Event %s do not support.')
            );
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
     * 返回已经创建.
     *
     * @return array
     */
    public function getCreated(): array
    {
        return $this->arrCreatedProp;
    }

    /**
     * 返回已经改变.
     *
     * @return array
     */
    public function getChanged(): array
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
        return static::PRIMARY_KEY;
    }

    /**
     * 返回自动增长字段.
     *
     * @return string
     */
    public function getAutoIncrement()
    {
        return static::AUTO_INCREMENT;
    }

    /**
     * 返回字段名字.
     *
     * @return array
     */
    public function getField(): array
    {
        return static::STRUCT;
    }

    /**
     * 是否存在字段.
     *
     * @param string $strFiled
     * @param mixed  $strField
     *
     * @return bool
     */
    public function hasField($strField): bool
    {
        return array_key_exists($strField, $this->getField());
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
            throw new Exception(
                sprintf(
                    'Entity %s do not have primary key or composite id not supported',
                    $this->getCalledClass()
                )
            );
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

        return $this->id();
    }

    /**
     * 返回设置表.
     *
     * @return string
     */
    public function getTable()
    {
        return static::TABLE;
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
     * 是否自动填充数据.
     *
     * @param bool $autoFill
     *
     * @return $this
     */
    public function autoFill($autoFill = true)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->createFill = $autoFill;
        $this->updateFill = $autoFill;

        return $this;
    }

    /**
     * 写入是否自动填充数据.
     *
     * @param bool $autoFill
     *
     * @return $this
     */
    public function createFill($autoFill = true)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->createFill = $autoFill;

        return $this;
    }

    /**
     * 更新是否自动填充数据.
     *
     * @param bool $autoFill
     *
     * @return $this
     */
    public function updateFill($autoFill = true)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->updateFill = $autoFill;

        return $this;
    }

    /**
     * 设置转换隐藏属性.
     *
     * @param array|string $hidden
     *
     * @return $this
     */
    public function hidden($hidden)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->hidden = $this->normalizeData(
            ...func_get_args()
        );

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
     * @param array|string $hidden
     *
     * @return $this
     */
    public function addHidden($hidden)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $hidden = $this->normalizeData(
            ...func_get_args()
        );

        $this->hidden = array_merge(
            $this->hidden,
            $hidden
        );

        return $this;
    }

    /**
     * 删除 hidden.
     *
     * @param array|string $hidden
     *
     * @return $this
     */
    public function removeHidden($hidden)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $hidden = $this->normalizeData(
            ...func_get_args()
        );

        $this->hidden = array_diff(
            $this->hidden,
            $hidden
        );

        return $this;
    }

    /**
     * 设置转换显示属性.
     *
     * @param array|string $visible
     *
     * @return $this
     */
    public function visible($visible)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->visible = $this->normalizeData(
            ...func_get_args()
        );

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
     * @param array|string $string
     * @param mixed        $visible
     *
     * @return $this
     */
    public function addVisible($visible)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $visible = $this->normalizeData(
            ...func_get_args()
        );

        $this->visible = array_merge(
            $this->visible,
            $visible
        );

        return $this;
    }

    /**
     * 删除 visible.
     *
     * @param array|string $visible
     *
     * @return $this
     */
    public function removeVisible($visible)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $visible = $this->normalizeData(
            ...func_get_args()
        );

        $this->visible = array_diff(
            $this->visible,
            $visible
        );

        return $this;
    }

    /**
     * 设置转换追加属性.
     *
     * @param array|string $append
     *
     * @return $this
     */
    public function append($append)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->append = $this->normalizeData(
            ...func_get_args()
        );

        return $this;
    }

    /**
     * 获取转换追加属性.
     *
     * @return array
     */
    public function getAppend(): array
    {
        return $this->append;
    }

    /**
     * 添加转换追加属性.
     *
     * @param array|string $append
     *
     * @return $this
     */
    public function addAppend($append)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $append = $this->normalizeData(
            ...func_get_args()
        );

        $this->append = array_merge(
            $this->append,
            $append
        );

        return $this;
    }

    /**
     * 删除 append.
     *
     * @param array|string $append
     *
     * @return $this
     */
    public function removeAppend($append)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $append = $this->normalizeData(
            ...func_get_args()
        );

        $this->append = array_diff(
            $this->append,
            $append
        );

        return $this;
    }

    /**
     * 设置模型实体时间格式化.
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
        $arrProp = $this->blackAndWhite(
            array_flip($this->arrCreatedProp),
            $this->visible,
            $this->hidden
        );

        if ($this->append) {
            $arrProp = array_merge(
                $arrProp,
                array_flip($this->append)
            );
        }

        foreach ($arrProp as $strProp => &$mixValue) {
            $mixValue = $this->getProp($strProp);
        }

        foreach ($this->getDate() as $strProp) {
            if (!isset($arrProp[$strProp])) {
                continue;
            }

            $arrProp[$strProp] = $this->serializeDate(
                $this->asDateTime($arrProp[$strProp])
            );
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
    public function blackAndWhite(array $arrKey, array $arrWhite, array $arrBlack)
    {
        if (!empty($arrWhite)) {
            $arrKey = array_intersect_key(
                $arrKey,
                array_flip($arrWhite)
            );
        } elseif (!empty($arrBlack)) {
            $arrKey = array_diff_key(
                $arrKey,
                array_flip($arrBlack)
            );
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
        if (!isset(static::STRUCT[$strKey])) {
            return false;
        }

        if (array_key_exists('conversion', static::STRUCT[$strKey])) {
            return $mixType ?
                in_array($this->getConversionType($strKey), (array) $mixType, true) :
                true;
        }

        return false;
    }

    /**
     * 创建一个模型实体集合.
     *
     * @param array $arrEntity
     *
     * @return \Leevel\Collection\Collection
     */
    public function collection(array $arrEntity = [])
    {
        return new Collection($arrEntity);
    }

    /**
     * 创建新的实例.
     *
     * @param array $arrProp
     * @param mixed $mixConnect
     *
     * @return static
     */
    public function newInstance($arrProp = [], $mixConnect = null)
    {
        return new static((array) $arrProp, $mixConnect);
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
        return $this->asDateTime($mixValue)->
        format($this->getDateFormat());
    }

    /**
     * 获取查询键值
     *
     * @return array|void
     */
    public function getKeyConditionForQuery()
    {
        if (null === (($arrPrimaryData = $this->id()))) {
            throw new Exception(
                sprintf(
                    'Entity %s has no primary key data',
                    $this->getCalledClass()
                )
            );
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
        return $this->getQuery()->
        asClass($this->getCalledClass())->

        asCollection()->

        registerCallSelect(
            new Select($this)
        );
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
     * 返回模型实体类的 meta 对象
     *
     * @return \Leevel\Database\Ddd\IMeta
     */
    public function meta(): IMeta
    {
        return Meta::instance(static::TABLE, $this->mixConnect);
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
     * 保存统一入口.
     *
     * @param string     $sSaveMethod
     * @param null|array $arrData
     *
     * @return $this
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
                $this->createReal();

                break;
            case 'update':
                $this->updateReal();

                break;
            case 'replace':
                $this->replaceReal();

                break;
            case 'save':
            default:
                $arrPrimaryData = $this->id(/*true*/);

                // 复合主键的情况下，则使用 replace 方式
                if (is_array($arrPrimaryData)) {
                    $this->replaceReal();
                }

                // 单一主键
                else {
                    if (empty($arrPrimaryData)) {
                        $this->createReal();
                    } else {
                        $this->updateReal();
                    }
                }

                break;
        }

        return $this;
    }

    /**
     * 添加数据.
     *
     * @return $this
     */
    protected function createReal()
    {
        $this->parseAutoFill('create');

        $arrPropKey = $this->normalizeBlackAndWhite(
            array_flip($this->arrCreatedProp),
            'fill'
        );

        if ($arrPropKey) {
            $arrPropKey = $this->normalizeBlackAndWhite(
                $arrPropKey,
                'create_fill'
            );
        }

        $arrSaveData = [];

        foreach ($this->arrCreatedProp as $prop) {
            if (!array_key_exists($prop, $arrPropKey)) {
                continue;
            }

            $arrSaveData[$prop] = $this->{$this->normalizeCamelizeProp($prop)};
        }

        if (!$arrSaveData) {
            if (null === (($arrPrimaryKey = $this->getPrimaryKeyNameSource()))) {
                throw new Exception(
                    sprintf(
                        'Entity %s has no primary key', $this->getCalledClass()
                    )
                );
            }

            foreach ($arrPrimaryKey as $strPrimaryKey) {
                $arrSaveData[$strPrimaryKey] = null;
            }
        }

        $this->prepareFlush(
            function ($arrSaveData) {
                $this->runEvent(
                    static::BEFORE_CREATE_EVENT,
                    $arrSaveData
                );

                $intLastInsertId = $this->meta()->insert($arrSaveData);

                $this->{$this->getAutoIncrement()} = $intLastInsertId;

                $this->clearChanged();

                $this->runEvent(
                    static::AFTER_CREATE_EVENT,
                    $arrSaveData
                );

                return $this->lastResult = $intLastInsertId;
            }, [$arrSaveData]);

        return $this;
    }

    /**
     * 更新数据.
     *
     * @return $this
     */
    protected function updateReal()
    {
        $this->parseAutoFill('update');

        $arrPropKey = $this->normalizeBlackAndWhite(
            array_flip($this->arrChangedProp),
            'fill'
        );

        if ($arrPropKey) {
            $arrPropKey = $this->normalizeBlackAndWhite(
                $arrPropKey,
                'update_fill'
            );
        }

        $arrSaveData = [];

        foreach ($this->arrChangedProp as $prop) {
            if (!array_key_exists($prop, $arrPropKey)) {
                continue;
            }

            $arrSaveData[$prop] = $this->{$this->normalizeCamelizeProp($prop)};
        }

        if (!$arrSaveData) {
            return $this;
        }

        $arrCondition = [];

        foreach ($this->getPrimaryKeyNameSource() as $sFieldName) {
            if (isset($arrSaveData[$sFieldName])) {
                unset($arrSaveData[$sFieldName]);
            }

            if ($value = $this->{$this->normalizeCamelizeProp($sFieldName)}) {
                $arrCondition[$sFieldName] = $value;
            }
        }

        if (empty($arrCondition) || empty($arrSaveData)) {
            return $this;
        }

        $this->prepareFlush(
            function ($arrCondition, $arrSaveData) {
                $this->runEvent(
                    static::BEFORE_UPDATE_EVENT,
                    $arrSaveData,
                    $arrCondition
                );

                $intNum = $this->meta()->update(
                    $arrCondition,
                    $arrSaveData
                );

                $this->runEvent(
                    static::BEFORE_UPDATE_EVENT,
                    null,
                    null
                );

                $this->clearChanged();

                $this->runEvent(static::AFTER_UPDATE_EVENT);

                return $this->lastResult = isset($intNum) ? $intNum : 0;
            }, [$arrCondition, $arrSaveData]);

        return $this;
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
        } catch (Exception $e) {
            return $this->updateReal();
        }
    }

    /**
     * 准备即将进行持久化的数据.
     *
     * @param \Closure $strType
     * @param array    $data
     */
    protected function prepareFlush(Closure $flush, array $data)
    {
        $this->setFlush($flush);

        $this->setFlushData($data);
    }

    /**
     * 自动填充.
     *
     * @param string $strType
     */
    protected function parseAutoFill($strType = 'create')
    {
        if (('create' === $strType && !$this->createFill) ||
            ('update' === $strType && !$this->updateFill)) {
            return;
        }

        foreach (static::STRUCT as $prop => $value) {
            if (array_key_exists($strType.'_fill', $value)) {
                $this->forceProp($prop, $value[$strType.'_fill']);

                continue;
            }

            if (array_key_exists('auto_fill', $value)) {
                $this->forceProp($prop, $value['auto_fill']);
            }
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
            throw new Exception(
                sprintf(
                    'Relation prop must return a type of %s',
                    'Leevel\Database\Ddd\Relation\Relation'
                )
            );
        }

        return $this->arrRelationProp[$strPropName] = $oRelation->sourceQuery();
    }

    /**
     * 格式化黑白名单数据.
     *
     * @param array  $arrKey
     * @param string $type
     *
     * @return array
     */
    protected function normalizeBlackAndWhite(array $arrKey, string $type): array
    {
        return $this->blackAndWhite(
            $arrKey,
            $this->blackWhites[$type]['white'],
            $this->blackWhites[$type]['black']
        );
    }

    /**
     * 格式化数据.
     *
     * @param array|string $data
     *
     * @return array
     */
    protected function normalizeData($data): array
    {
        return is_array($data) ?
            $data :
            func_get_args();
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
        return strtolower(
            static::STRUCT[$strKey]['conversion']
        );
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
            'time',
            'timestamp',
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
            'arr',
            'array',
            'json',
            'obj',
            'object',
            'coll',
            'collection',
        ]);
    }

    /**
     * 属性是否可以转换为对象.
     *
     * @param string $strProp
     *
     * @return bool
     */
    protected function isObjectConversion($strProp)
    {
        return $this->hasConversion($strProp, [
            'obj',
            'object',
        ]);
    }

    /**
     * 将变量转为 JSON.
     *
     * @param mixed $mixValue
     * @param int   $option
     *
     * @return string
     */
    protected function asJson($mixValue, int $option = 0)
    {
        return json_encode($mixValue, $option);
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
            case 'str':
            case 'string':
                return (string) $mixValue;
            case 'bool':
            case 'boolean':
                return (bool) $mixValue;
            case 'obj':
            case 'object':
                return $this->fromJson($mixValue, true);
            case 'arr':
            case 'array':
            case 'json':
                return $this->fromJson($mixValue);
            case 'coll':
            case 'collection':
                return new Collection($this->fromJson($mixValue));
            case 'date':
            case 'datetime':
                return $this->asDateTime($mixValue);
            case 'time':
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
    protected function asDateTime($mixValue): Carbon
    {
        if ($mixValue instanceof Carbon) {
            return $mixValue;
        }

        if ($mixValue instanceof DateTimeInterface) {
            return new Carbon(
                $mixValue->format('Y-m-d H:i:s.u'),
                $mixValue->getTimeZone()
            );
        }

        if (is_numeric($mixValue)) {
            return Carbon::createFromTimestamp($mixValue);
        }

        if (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $mixValue)) {
            return Carbon::createFromFormat('Y-m-d', $mixValue)->
            startOfDay();
        }

        return Carbon::createFromFormat(
            $this->getDateFormat(),
            $mixValue
        );
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
        return $this->asDateTime($mixValue)->
        getTimestamp();
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
     * 删除模型实体.
     *
     * @return int
     */
    protected function deleteEntityByKey()
    {
        return $this->getQuery()->
        where($this->getKeyConditionForQuery())->

        delete();
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
    protected function normalizeCamelizeProp($strProp)
    {
        if (isset(static::$arrCamelizeProp[$strProp])) {
            return static::$arrCamelizeProp[$strProp];
        }

        return static::$arrCamelizeProp[$strProp] = Str::camelize($strProp);
    }
}
