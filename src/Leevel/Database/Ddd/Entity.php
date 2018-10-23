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
use InvalidArgumentException;
use JsonSerializable;
use Leevel\Collection\Collection;
use Leevel\Database\Ddd\Relation\BelongsTo;
use Leevel\Database\Ddd\Relation\HasMany;
use Leevel\Database\Ddd\Relation\HasOne;
use Leevel\Database\Ddd\Relation\ManyMany;
use Leevel\Database\Ddd\Relation\Relation;
use Leevel\Database\Select as DatabaseSelect;
use Leevel\Event\IDispatch;
use Leevel\Support\IArray;
use Leevel\Support\IJson;
use Leevel\Support\Str;

/**
 * 模型实体 Object Relational Mapping.
 * 为最大化避免 getter setter 属性与系统冲突
 * 系统自身的属性均加前缀 leevel，设置以 with 开头, 获取数据不带 get.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.04.27
 *
 * @version 1.0
 */
abstract class Entity implements IEntity, IArray, IJson, JsonSerializable, ArrayAccess
{
    /**
     * 此模型实体的连接名称.
     *
     * @var mixed
     */
    protected $leevelConnect;

    /**
     * 待保存的模型实体属性.
     *
     * @var array
     */
    protected $leevelCreatedProp = [];

    /**
     * 待更新的模型实体属性.
     *
     * @var array
     */
    protected $leevelChangedProp = [];

    /**
     * 黑白名单.
     *
     * @var array
     */
    protected $leevelBlackWhites = [
        'construct_prop' => [
            'white' => [],
            'black' => [],
        ],
        'create_prop' => [
            'white' => [],
            'black' => [],
        ],
        'update_prop' => [
            'white' => [],
            'black' => [],
        ],
        'show_prop' => [
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
     * 持久化基础层
     *
     * @var \Closure
     */
    protected $leevelFlush;

    /**
     * 即将持久化数据.
     *
     * @var array
     */
    protected $leevelFlushData;

    /**
     * 多对多关联中间实体.
     *
     * @var \Leevel\Database\Ddd\Entity
     */
    protected $leevelRelationMiddle;

    /**
     * 模型实体事件处理器.
     *
     * @var \Leevel\Event\IDispatch
     */
    protected static $leevelDispatch;

    /**
     * 缓存驼峰法命名属性.
     *
     * @var array
     */
    protected static $leevelCamelizeProp = [];

    /**
     * 缓存下划线命名属性.
     *
     * @var array
     */
    protected static $leevelUnCamelizeProp = [];

    /**
     * 构造函数.
     *
     * @param array $data
     * @param mixed $connect
     */
    public function __construct(array $data = [], $connect = null)
    {
        $className = static::class;

        foreach ([
            'TABLE', 'ID',
            'AUTO', 'STRUCT',
        ] as $item) {
            if (!defined($className.'::'.$item)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'The entity const %s was not defined.',
                        $item
                    )
                );
            }
        }

        foreach (static::STRUCT as $field => $v) {
            foreach ([
                'construct_prop', 'show_prop',
                'create_prop', 'update_prop',
                'create_fill', 'update_fill',
            ] as $type) {
                foreach (['black', 'white'] as $bw) {
                    if (!empty($v[$type.'_'.$bw])) {
                        $this->leevelBlackWhites[$type][$bw][] = $field;
                    }
                }
            }
        }

        if (null !== $connect) {
            $this->leevelConnect = $connect;
        }

        if ($data) {
            foreach (
                $this->normalizeWhiteAndBlack($data, 'construct_prop') as $prop => $value) {
                if (array_key_exists($prop, $data)) {
                    $this->withPropValue($prop, $data[$prop], false);
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
    public function __get(string $prop)
    {
        return $this->offsetGet($prop);
    }

    /**
     * 更新属性值
     *
     * @param string $key
     * @param mixed  $value
     */
    public function __set(string $prop, $value)
    {
        $this->offsetSet($prop, $value);
    }

    /**
     * 是否存在属性.
     *
     * @param string $prop
     *
     * @return bool
     */
    public function __isset(string $prop): bool
    {
        return $this->offsetExists($prop);
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
        // getter
        if (0 === strpos($method, 'get')) {
            if (!method_exists($this, 'getter')) {
                throw new InvalidArgumentException(
                    sprintf('The entity `%s` `%s` method was not defined.', static::class, 'getter')
                );
            }

            return $this->getter(lcfirst(substr($method, 3)));
        }

        // setter
        if (0 === strpos($method, 'set')) {
            if (!method_exists($this, 'setter')) {
                throw new InvalidArgumentException(
                    sprintf('The entity `%s` `%s` method was not defined.', static::class, 'setter')
                );
            }

            return $this->setter(lcfirst(substr($method, 3)), $args[0] ?? null);
        }

        // relation
        try {
            $unCamelize = $this->normalizeUnCamelizeProp($method);

            if ($this->isRelation($unCamelize)) {
                return $this->loadRelation($unCamelize, true);
            }
        } catch (InvalidArgumentException $e) {
        }

        // 作用域
        if (method_exists($this, 'scope'.ucwords($method))) {
            array_unshift($args, $method);

            return $this->scope(...$args);
        }

        $this->runEvent(static::BEFORE_FIND_EVENT);
        $this->runEvent(static::BEFORE_SELECT_EVENT);

        $data = $this->select()->{$method}(...$args);

        if ($data instanceof Collection) {
            $this->runEvent(static::AFTER_SELECT_EVENT, $data);
        } else {
            $this->runEvent(static::AFTER_FIND_EVENT, $data);
        }

        return $data;
    }

    /**
     * call static.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public static function __callStatic(string $method, array $args)
    {
        return (new static())->{$method}(...$args);
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
     * @param array      $data
     * @param null|array $fill
     *
     * @return $this
     */
    public function save(array $data = [], array $fill = null)
    {
        $this->saveEntry('save', $data, $fill);

        return $this;
    }

    /**
     * 新增快捷方式.
     *
     * @param array      $data
     * @param null|array $fill
     *
     * @return $this
     */
    public function create(array $data = [], array $fill = null)
    {
        $this->saveEntry('create', $data, $fill);

        return $this;
    }

    /**
     * 更新快捷方式.
     *
     * @param array      $data
     * @param null|array $fill
     *
     * @return $this
     */
    public function update(array $data = [], array $fill = null)
    {
        $this->saveEntry('update', $data, $fill);

        return $this;
    }

    /**
     * replace 快捷方式.
     *
     * @param array      $data
     * @param null|array $fill
     *
     * @return $this
     */
    public function replace(array $data = [], array $fill = null)
    {
        $this->saveEntry('replace', $data, $fill);

        return $this;
    }

    /**
     * 根据主键 ID 删除模型实体.
     *
     * @param array $ids
     *
     * @return int
     */
    public function destroy(array $ids): int
    {
        $count = 0;

        $instance = new static();

        foreach ($instance->whereIn($instance->singlePrimaryKey(), $ids)->findAll() as $entity) {
            if ($entity->delete()) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * 删除模型实体.
     *
     * @return int
     */
    public function delete(): int
    {
        if (null === $this->primaryKey()) {
            throw new InvalidArgumentException(
                sprintf(
                    'Entity %s has no primary key.',
                    static::class
                )
            );
        }

        $this->runEvent(static::BEFORE_DELETE_EVENT);

        $num = $this->deleteEntityByKey();

        $this->runEvent(static::AFTER_DELETE_EVENT);

        return $num;
    }

    /**
     * 数据持久化数据.
     *
     * @return mixed
     */
    public function flush()
    {
        if (!$this->leevelFlush) {
            return 0;
        }

        $result = call_user_func_array($this->leevelFlush, $this->leevelFlushData);

        $this->leevelFlush = null;
        $this->leevelFlushData = null;

        $this->runEvent(static::AFTER_SAVE_EVENT);

        return $result;
    }

    /**
     * 获取数据持久化数据.
     *
     * @return null|array
     */
    public function flushData(): ?array
    {
        return $this->leevelFlushData;
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
        $primaryData = [];

        $primaryKey = $this->primaryKeys();

        foreach ($primaryKey as $value) {
            if (!$this->{$this->normalizeCamelizeProp($value)}) {
                continue;
            }

            if (true === $update) {
                if (!in_array($value, $this->leevelChangedProp, true)) {
                    $primaryData[$value] = $this->{$this->normalizeCamelizeProp($value)};
                }
            } else {
                $primaryData[$value] = $this->{$this->normalizeCamelizeProp($value)};
            }
        }

        // 复合主键，但是数据不完整则忽略
        if (count($primaryKey) > 1 &&
            count($primaryKey) !== count($primaryData)) {
            return;
        }

        if (1 === count($primaryData)) {
            $primaryData = reset($primaryData);
        }

        if (!empty($primaryData)) {
            return $primaryData;
        }
    }

    /**
     * 返回关联数据.
     *
     * @param string $prop
     *
     * @return mixed
     */
    public function loadRelationProp($prop)
    {
        if ($result = $this->relationProp($prop)) {
            return $result;
        }

        return $this->parseDataFromRelation($prop);
    }

    /**
     * 是否为关联属性.
     *
     * @param string $prop
     *
     * @return bool
     */
    public function isRelation(string $prop): bool
    {
        $this->validateProp($prop);

        $struct = static::STRUCT[$prop];

        if (isset($struct[self::BELONGS_TO]) ||
           isset($struct[self::HAS_MANY]) ||
           isset($struct[self::HAS_ONE]) ||
           isset($struct[self::MANY_MANY])) {
            return true;
        }

        return false;
    }

    /**
     * 读取关联.
     *
     * @param string $prop
     *
     * @return \Leevel\Database\Ddd\Relation\Relation
     */
    public function loadRelation(string $prop): Relation
    {
        $defined = static::STRUCT[$prop];

        if (isset($defined[self::BELONGS_TO])) {
            $this->validateRelationDefined($defined, ['source_key', 'target_key']);

            $relation = $this->belongsTo(
               $defined[self::BELONGS_TO],
               $defined['target_key'],
               $defined['source_key']
           );
        } elseif (isset($defined[self::HAS_MANY])) {
            $this->validateRelationDefined($defined, ['source_key', 'target_key']);

            $relation = $this->hasMany(
               $defined[self::HAS_MANY],
               $defined['target_key'],
               $defined['source_key']
           );
        } elseif (isset($defined[self::HAS_ONE])) {
            $this->validateRelationDefined($defined, ['source_key', 'target_key']);

            $relation = $this->hasOne(
               $defined[self::HAS_ONE],
               $defined['target_key'],
               $defined['source_key']
           );
        } elseif (isset($defined[self::MANY_MANY])) {
            $this->validateRelationDefined($defined, [
                'middle_entity', 'source_key', 'target_key',
                'middle_target_key', 'middle_source_key',
            ]);

            $relation = $this->ManyMany(
               $defined[self::MANY_MANY],
               $defined['middle_entity'],
               $defined['target_key'],
               $defined['source_key'],
               $defined['middle_target_key'],
               $defined['middle_source_key']
           );
        }

        if (isset($defined[self::SCOPE])) {
            call_user_func([$this, 'scope'.ucfirst($defined[self::SCOPE])], $relation);
        }

        return $relation;
    }

    /**
     * 取得模型实体数据.
     *
     * @param string $prop
     *
     * @return mixed
     */
    public function relationProp(string $prop)
    {
        return $this->{'get'.ucfirst($this->normalizeCamelizeProp($prop))}();
    }

    /**
     * 设置关联数据.
     *
     * @param string $prop
     * @param mixed  $value
     */
    public function withRelationProp(string $prop, $value)
    {
        $this->{'set'.ucfirst($this->normalizeCamelizeProp($prop))}($value);
    }

    /**
     * 预加载关联.
     *
     * @param array $relation
     *
     * @return \Leevel\Database\Ddd\Select
     */
    public static function eager(array $relation): Select
    {
        return (new static())->select()->eager($relation);
    }

    /**
     * 设置多对多中间实体.
     *
     * @param \Leevel\Database\Ddd\IEntity $middle
     */
    public function withMiddle(IEntity $middle): void
    {
        $this->leevelRelationMiddle = $middle;
    }

    /**
     * 获取多对多中间实体.
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function middle(): ?IEntity
    {
        return $this->leevelRelationMiddle;
    }

    /**
     * 一对一关联.
     *
     * @param string $relatedEntityClass
     * @param string $targetKey
     * @param string $sourceKey
     *
     * @return \Leevel\Database\Ddd\Relation\HasOne
     */
    public function hasOne(string $relatedEntityClass, string $targetKey, string $sourceKey): HasOne
    {
        $entity = new $relatedEntityClass();

        $this->validateRelationField($entity, $targetKey);
        $this->validateRelationField($this, $sourceKey);

        return new HasOne($entity, $this, $targetKey, $sourceKey);
    }

    /**
     * 定义从属关系.
     *
     * @param string $relatedEntityClass
     * @param string $targetKey
     * @param string $sourceKey
     *
     * @return \Leevel\Database\Ddd\Relation\BelongsTo
     */
    public function belongsTo($relatedEntityClass, string $targetKey, string $sourceKey): BelongsTo
    {
        $entity = new $relatedEntityClass();

        $this->validateRelationField($entity, $targetKey);
        $this->validateRelationField($this, $sourceKey);

        return new BelongsTo($entity, $this, $targetKey, $sourceKey);
    }

    /**
     * 一对多关联.
     *
     * @param string $relatedEntityClass
     * @param string $targetKey
     * @param string $sourceKey
     *
     * @return \Leevel\Database\Ddd\Relation\HasMany
     */
    public function hasMany(string $relatedEntityClass, string $targetKey, string $sourceKey): HasMany
    {
        $entity = new $relatedEntityClass();

        $this->validateRelationField($entity, $targetKey);
        $this->validateRelationField($this, $sourceKey);

        return new HasMany($entity, $this, $targetKey, $sourceKey);
    }

    /**
     * 多对多关联.
     *
     * @param string $relatedEntityClass
     * @param string $middleEntityClass
     * @param string $targetKey
     * @param string $sourceKey
     * @param string $middleTargetKey
     * @param string $middleSourceKey
     *
     * @return \Leevel\Database\Ddd\Relation\ManyMany
     */
    public function manyMany(string $relatedEntityClass, string $middleEntityClass, string $targetKey, string $sourceKey, string $middleTargetKey, string $middleSourceKey): ManyMany
    {
        $entity = new $relatedEntityClass();
        $middleEntity = new $middleEntityClass();

        $this->validateRelationField($entity, $targetKey);
        $this->validateRelationField($middleEntity, $middleTargetKey);
        $this->validateRelationField($this, $sourceKey);
        $this->validateRelationField($middleEntity, $middleSourceKey);

        return new ManyMany(
            $entity, $this, $middleEntity, $targetKey,
            $sourceKey, $middleTargetKey, $middleSourceKey
        );
    }

    /**
     * 返回模型实体事件处理器.
     *
     * @return \Leevel\Event\IDispatch
     */
    public static function eventDispatch(): IDispatch
    {
        return static::$leevelDispatch;
    }

    /**
     * 设置模型实体事件处理器.
     *
     * @param null|\Leevel\Event\IDispatch $dispatch
     */
    public static function withEventDispatch(IDispatch $dispatch = null)
    {
        static::$leevelDispatch = $dispatch;
    }

    /**
     * 注册模型实体事件.
     *
     * @param string                        $event
     * @param \leevel\event\observer|string $listener
     */
    public static function registerEvent(string $event, $listener)
    {
        if (isset(static::$leevelDispatch)) {
            static::isSupportEvent($event);

            static::$leevelDispatch->listener(
                "entity.{$event}:".static::class,
                $listener
            );
        }
    }

    /**
     * 执行模型实体事件.
     *
     * @param string $event
     */
    public function runEvent(string $event)
    {
        if (!isset(static::$leevelDispatch)) {
            return;
        }

        $this->isSupportEvent($event);

        $args = func_get_args();
        array_shift($args);
        array_unshift($args, "entity.{$event}:".get_class($this));
        array_unshift($args, $this);

        call_user_func_array([
            static::$leevelDispatch,
            'handle',
        ], $args);
    }

    /**
     * 验证事件是否受支持
     *
     * @param string $event
     *
     * @return bool
     */
    public static function isSupportEvent(string $event)
    {
        if (!in_array($event, static::supportEvent(), true)) {
            throw new InvalidArgumentException(sprintf('Event `%s` do not support.'));
        }
    }

    /**
     * 返回受支持的事件.
     *
     * @return array
     */
    public static function supportEvent(): array
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
    public function created(): array
    {
        return $this->leevelCreatedProp;
    }

    /**
     * 返回已经改变.
     *
     * @return array
     */
    public function changed(): array
    {
        return $this->leevelChangedProp;
    }

    /**
     * 检测是否已经改变.
     *
     * @param string $prop
     *
     * @return bool
     */
    public function hasChanged(string $prop)
    {
        return isset($this->leevelChangedProp[$prop]);
    }

    /**
     * 清除改变属性.
     *
     * @param null|array $props
     *
     * @return $this
     */
    public function clearChanged(?array $props = null)
    {
        if (null === $props) {
            $this->leevelChangedProp = [];
        } else {
            foreach ($props as $value) {
                if (isset($this->leevelChangedProp[$value])) {
                    unset($this->leevelChangedProp[$value]);
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
    public function primaryKey()
    {
        $keys = $this->primaryKeys();

        return 1 === count($keys) ? reset($keys) : $keys;
    }

    /**
     * 返回主键字段.
     *
     * @return array
     */
    public function primaryKeys(): array
    {
        return (array) static::ID;
    }

    /**
     * 返回自动增长字段.
     *
     * @return string
     */
    public function autoIncrement(): ?string
    {
        return static::AUTO;
    }

    /**
     * 返回字段名字.
     *
     * @return array
     */
    public function fields(): array
    {
        return static::STRUCT;
    }

    /**
     * 是否存在字段.
     *
     * @param string $strFiled
     * @param mixed  $field
     *
     * @return bool
     */
    public function hasField(string $field): bool
    {
        return array_key_exists($field, $this->fields());
    }

    /**
     * 返回供查询的主键字段
     * 复合主键或者没有主键直接抛出异常.
     *
     * @return string|void
     */
    public function singlePrimaryKey()
    {
        $key = $this->primaryKey();

        if (!is_string($key)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Entity %s do not have primary key or composite id not supported.',
                    static::class
                )
            );
        }

        return $key;
    }

    /**
     * 返回供查询的主键字段值
     * 复合主键或者没有主键直接抛出异常.
     *
     * @return mixed
     */
    public function getPrimaryKeyForQuery()
    {
        $this->singlePrimaryKey();

        return $this->id();
    }

    /**
     * 返回设置表.
     *
     * @return string
     */
    public function table(): string
    {
        return static::TABLE;
    }

    /**
     * 设置连接.
     *
     * @param mixed $connect
     *
     * @return $this
     */
    public function withConnect($connect)
    {
        $this->leevelConnect = $connect;

        return $this;
    }

    /**
     * 对象转数组.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->toArraySource(...func_get_args());
    }

    /**
     * 对象转 JSON.
     *
     * @param int $option
     *
     * @return string
     */
    public function toJson(int $option = JSON_UNESCAPED_UNICODE)
    {
        return json_encode($this->toArray(), $option);
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
     * 创建一个模型实体集合.
     *
     * @param array $entity
     *
     * @return \Leevel\Collection\Collection
     */
    public function collection(array $entity = [])
    {
        return new Collection($entity);
    }

    /**
     * 创建新的实例.
     *
     * @param array $prop
     * @param mixed $connect
     *
     * @return static
     */
    public function newInstance(array $prop = [], $connect = null)
    {
        return new static($prop, $connect);
    }

    /**
     * 获取查询键值
     *
     * @return array
     */
    public function idCondition(): array
    {
        if (null === (($primaryData = $this->id()))) {
            throw new InvalidArgumentException(
                sprintf(
                    'Entity %s has no primary key data.',
                    static::class
                )
            );
        }

        if (!is_array($primaryData)) {
            $primaryData = [
                $this->singlePrimaryKey() => $primaryData,
            ];
        }

        return $primaryData;
    }

    /**
     * 返回数据库查询集合对象
     *
     * @return \Leevel\Database\Select
     */
    public function selectReal(): DatabaseSelect
    {
        return $this->meta()->
        select()->

        asClass(static::class)->

        asCollection();
    }

    /**
     * 返回数据库查询集合对象
     *
     * @return \Leevel\Database\Ddd\Select
     */
    public function select(): Select
    {
        return new Select($this);
    }

    /**
     * 返回模型实体类的 meta 对象
     *
     * @return \Leevel\Database\Ddd\IMeta
     */
    public function meta(): IMeta
    {
        return Meta::instance(static::TABLE, $this->leevelConnect);
    }

    /**
     * 实现 ArrayAccess::offsetExists.
     *
     * @param string $index
     *
     * @return bool
     */
    public function offsetExists($index): bool
    {
        return $this->hasProp($this->normalizeUnCamelizeProp($index));
    }

    /**
     * 实现 ArrayAccess::offsetSet.
     *
     * @param string $index
     * @param mixed  $newval
     */
    public function offsetSet($index, $newval)
    {
        $this->withPropValue($this->normalizeUnCamelizeProp($index), $newval);
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
        return $this->propValue($this->normalizeUnCamelizeProp($index));
    }

    /**
     * 实现 ArrayAccess::offsetUnset.
     *
     * @param string $index
     */
    public function offsetUnset($index)
    {
        $this->offsetSet($index, null);
    }

    /**
     * 保存统一入口.
     *
     * @param string     $saveMethod
     * @param array      $data
     * @param null|array $fill
     *
     * @return $this
     */
    protected function saveEntry(string $saveMethod, array $data, ?array $fill = null)
    {
        foreach ($data as $k => $v) {
            $this->withPropValue($k, $v);
        }

        $this->runEvent(static::BEFORE_SAVE_EVENT);

        // 程序通过内置方法统一实现
        switch (strtolower($saveMethod)) {
            case 'create':
                $this->createReal($fill);

                break;
            case 'update':
                $this->updateReal($fill);

                break;
            case 'replace':
                $this->replaceReal($fill);

                break;
            case 'save':
            default:
                $primaryData = $this->id(/*true*/);

                // 复合主键的情况下，则使用 replace 方式
                if (is_array($primaryData)) {
                    $this->replaceReal($fill);
                }

                // 单一主键
                else {
                    if (empty($primaryData)) {
                        $this->createReal($fill);
                    } else {
                        $this->updateReal($fill);
                    }
                }

                break;
        }

        return $this;
    }

    /**
     * 添加数据.
     *
     * @param null|array $fill
     *
     * @return $this
     */
    protected function createReal(?array $fill = null)
    {
        $this->parseAutoFill('create', $fill);

        $propKey = $this->normalizeWhiteAndBlack(
            array_flip($this->leevelCreatedProp), 'create_prop'
        );

        $saveData = [];

        foreach ($this->leevelCreatedProp as $prop) {
            if (!array_key_exists($prop, $propKey)) {
                continue;
            }

            $saveData[$prop] = $this->{$this->normalizeCamelizeProp($prop)};
        }

        if (!$saveData) {
            if (null === (($primaryKey = $this->primaryKeys()))) {
                throw new InvalidArgumentException(
                    sprintf('Entity %s has no primary key.', static::class)
                );
            }

            foreach ($primaryKey as $value) {
                $saveData[$value] = null;
            }
        }

        $this->leevelFlush = function ($saveData) {
            $this->runEvent(static::BEFORE_CREATE_EVENT, $saveData);

            $lastInsertId = $this->meta()->insert($saveData);

            $this->{$this->autoIncrement()} = $lastInsertId;

            $this->clearChanged();

            $this->runEvent(static::AFTER_CREATE_EVENT, $saveData);
        };

        $this->leevelFlushData = [$saveData];

        return $this;
    }

    /**
     * 更新数据.
     *
     * @param null|array $fill
     *
     * @return $this
     */
    protected function updateReal(?array $fill = null)
    {
        $this->parseAutoFill('update', $fill);

        $propKey = $this->normalizeWhiteAndBlack(
            array_flip($this->leevelChangedProp), 'update_prop'
        );

        $saveData = [];

        foreach ($this->leevelChangedProp as $prop) {
            if (!array_key_exists($prop, $propKey)) {
                continue;
            }

            $saveData[$prop] = $this->{$this->normalizeCamelizeProp($prop)};
        }

        if (!$saveData) {
            return $this;
        }

        $condition = [];

        foreach ($this->primaryKeys() as $field) {
            if (isset($saveData[$field])) {
                unset($saveData[$field]);
            }

            if ($value = $this->{$this->normalizeCamelizeProp($field)}) {
                $condition[$field] = $value;
            }
        }

        if (empty($condition) || empty($saveData)) {
            return $this;
        }

        $this->leevelFlush = function ($condition, $saveData) {
            $this->runEvent(static::BEFORE_UPDATE_EVENT, $saveData, $condition);

            $this->meta()->update($condition, $saveData);

            $this->runEvent(static::BEFORE_UPDATE_EVENT, null, null);

            $this->clearChanged();

            $this->runEvent(static::AFTER_UPDATE_EVENT);
        };

        $this->leevelFlushData = [$condition, $saveData];

        return $this;
    }

    /**
     * 模拟 replace 数据.
     *
     * @param null|array $fill
     *
     * @return mixed
     */
    protected function replaceReal(?array $fill = null)
    {
        try {
            return $this->createReal($fill);
        } catch (Exception $e) {
            return $this->updateReal($fill);
        }
    }

    /**
     * 改变属性.
     *
     * @param string $prop
     * @param mixed  $value
     * @param bool   $force
     * @param mixed  $prop
     */
    protected function withPropValue(string $prop, $value, bool $force = true)
    {
        $this->validateProp($prop);

        $value = $this->{'set'.ucfirst($this->normalizeCamelizeProp($prop))}($value);

        if ($this->isRelation($prop)) {
            return;
        }

        if (!in_array($prop, $this->leevelCreatedProp, true)) {
            $this->leevelCreatedProp[] = $prop;
        }

        if ($force && !in_array($prop, $this->leevelChangedProp, true) &&
            empty(static::STRUCT[$prop]['readonly'])) {
            $this->leevelChangedProp[] = $prop;
        }
    }

    /**
     * 返回属性.
     *
     * @param string $prop
     *
     * @return mixed
     */
    protected function propValue(string $prop)
    {
        $this->validateProp($prop);

        if (!$this->isRelation($prop)) {
            return $this->{'get'.ucfirst($this->normalizeCamelizeProp($prop))}();
        }

        return $this->loadRelationProp($prop);
    }

    /**
     * 是否存在属性.
     *
     * @param string $prop
     *
     * @return bool
     */
    protected function hasProp(string $prop): bool
    {
        if (!$this->hasField($prop)) {
            return false;
        }

        $prop = $this->normalizeCamelizeProp($prop);

        if (!property_exists($this, $prop)) {
            throw new InvalidArgumentException(
                sprintf('Prop `%s` of entity `%s` was not defined.', $prop, get_class($this))
            );
        }

        return true;
    }

    /**
     * 自动填充.
     *
     * @param string $type
     * @param array  $fill
     */
    protected function parseAutoFill(string $type, ?array $fill = null)
    {
        if (null === $fill) {
            return;
        }

        foreach (static::STRUCT as $prop => $value) {
            if ($fill && !in_array($prop, $fill, true)) {
                continue;
            }

            if (array_key_exists($type.'_fill', $value)) {
                $this->normalizeFill($prop, $value[$type.'_fill']);
            }
        }
    }

    /**
     * 格式化自动填充.
     *
     * @param string $prop
     * @param mixed  $value
     */
    protected function normalizeFill(string $prop, $value)
    {
        if (null === $value) {
            $camelizeClass = 'fill'.ucfirst($this->normalizeCamelizeProp($prop));

            if (method_exists($this, $camelizeClass)) {
                $value = $this->{$camelizeClass}($this->propValue($prop));
            }
        }

        $this->withPropValue($prop, $value);
    }

    /**
     * 从关联中读取数据.
     *
     * @param string $prop
     *
     * @return mixed
     */
    protected function parseDataFromRelation(string $prop)
    {
        $relation = $this->loadRelation($prop);
        $result = $relation->sourceQuery();

        $this->withRelationProp($prop, $result);

        return $result;
    }

    /**
     * 验证属性.
     *
     * @param string $prop
     */
    protected function validateProp(string $prop)
    {
        if (!$this->hasProp($prop)) {
            throw new InvalidArgumentException(
                sprintf('Entity `%s` prop or field of struct `%s` was not defined.', get_class($this), $prop)
            );
        }
    }

    /**
     * 校验关联字段定义.
     *
     * @param array $defined
     * @param array $field
     */
    protected function validateRelationDefined(array $defined, array $field): void
    {
        foreach ($field as $v) {
            if (!isset($defined[$v])) {
                throw new InvalidArgumentException(
                    sprintf('Relation `%s` field was not defined.', $v)
                );
            }
        }
    }

    /**
     * 验证关联字段.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param string                       $field
     */
    protected function validateRelationField(IEntity $entity, string $field): void
    {
        if (!$entity->hasField($field)) {
            throw new InvalidArgumentException(
                sprintf(
                    'The field `%s`.`%s` of entity `%s` was not defined.',
                    $entity->table(),
                    $field,
                    get_class($entity)
                )
            );
        }
    }

    /**
     * 格式化黑白名单数据.
     *
     * @param array  $key
     * @param string $type
     *
     * @return array
     */
    protected function normalizeWhiteAndBlack(array $key, string $type): array
    {
        return $this->whiteAndBlack(
            $key,
            $this->leevelBlackWhites[$type]['white'],
            $this->leevelBlackWhites[$type]['black']
        );
    }

    /**
     * 对象转数组.
     *
     * @return array
     */
    protected function toArraySource(array $white = [], array $black = [])
    {
        $args = func_get_args();

        if ($white || $black) {
            $prop = $this->whiteAndBlack(
                array_flip($this->leevelCreatedProp), $white, $black
            );
        } else {
            $prop = $this->normalizeWhiteAndBlack(
                array_flip($this->leevelCreatedProp), 'show_prop'
            );
        }

        foreach ($prop as $k => &$value) {
            $value = $this->propValue($k);
        }

        return $prop;
    }

    /**
     * 黑白名单数据解析.
     *
     * @param array $key
     * @param array $white
     * @param array $black
     *
     * @return array
     */
    protected function whiteAndBlack(array $key, array $white, array $black): array
    {
        if (!empty($white)) {
            $key = array_intersect_key(
                $key,
                array_flip($white)
            );
        } elseif (!empty($black)) {
            $key = array_diff_key(
                $key,
                array_flip($black)
            );
        }

        return $key;
    }

    /**
     * 删除模型实体.
     *
     * @return int
     */
    protected function deleteEntityByKey()
    {
        return $this->meta()->select()->
        where($this->idCondition())->

        delete();
    }

    /**
     * 返回转驼峰命名.
     *
     * @param string $prop
     *
     * @return string
     */
    protected function normalizeCamelizeProp(string $prop): string
    {
        if (isset(static::$leevelCamelizeProp[$prop])) {
            return static::$leevelCamelizeProp[$prop];
        }

        return static::$leevelCamelizeProp[$prop] = Str::camelize($prop);
    }

    /**
     * 返回下划线命名.
     *
     * @param string $prop
     *
     * @return string
     */
    protected function normalizeUnCamelizeProp(string $prop): string
    {
        if (isset(static::$leevelUnCamelizeProp[$prop])) {
            return static::$leevelUnCamelizeProp[$prop];
        }

        return static::$leevelUnCamelizeProp[$prop] = Str::unCamelize($prop);
    }
}
