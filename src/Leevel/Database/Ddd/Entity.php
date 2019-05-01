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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
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
use Leevel\Database\ReplaceException;
use Leevel\Database\Select as DatabaseSelect;
use Leevel\Event\IDispatch;
use Leevel\I18n\Helper\gettext;
use function Leevel\I18n\Helper\gettext as __;
use Leevel\Support\IArray;
use Leevel\Support\IJson;
use function Leevel\Support\Str\camelize;
use Leevel\Support\Str\camelize;
use function Leevel\Support\Str\un_camelize;
use Leevel\Support\Str\un_camelize;

/**
 * 模型实体 Object Relational Mapping.
 * 为最大化避免 getter setter 属性与系统冲突，getFoo 修改为 getterFoo，setBar 修改为 setterBar
 * 系统自身的属性均加前缀 leevel，设置以 with 开头.
 * ORM 主要基于妖怪大神的 QeePHP V2 设计灵感，查询器基于这个版本构建.
 * 例外参照了 Laravel 关联模型实现设计.
 * Doctrine 和 Java Hibernate 中关于 getter setter 的设计
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.04.27
 * @since 2018.10 进行一次大规模重构
 * @since 1.0.0-beta.1 2019.04.24 getFoo 修改为 getterFoo，setBar 修改为 setterBar
 * @see https://github.com/dualface/qeephp2_x
 * @see https://github.com/laravel/framework
 * @see https://github.com/doctrine/doctrine2
 * @see http://hibernate.org/
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
     * 已修改的模型实体属性.
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
     * 指示对象是否对应数据库中的一条记录.
     *
     * @var bool
     */
    protected $leevelNewed = true;

    /**
     * Replace 模式.
     * 先插入出现主键重复.
     * false 表示非 replace 模式，其它值表示 replace 模式附带的 fill 数据.
     *
     * @var mixed
     */
    protected $leevelReplace = false;

    /**
     * 多对多关联中间实体.
     *
     * @var \Leevel\Database\Ddd\Entity
     */
    protected $leevelRelationMiddle;

    /**
     * 作用域查询对象.
     *
     * @var \Leevel\Database\Select
     */
    protected $leevelScopeSelect;

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
     * 是否已经持久化数据.
     *
     * @var bool
     */
    protected $leevelFlushed = false;

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
    protected static $leevelCamelize = [];

    /**
     * 缓存下划线命名属性.
     *
     * @var array
     */
    protected static $leevelUnCamelize = [];

    /**
     * 缓存 ENUM 格式化数据.
     *
     * @var array
     */
    protected static $leevelEnums = [];

    /**
     * 构造函数.
     *
     * @param array $data
     * @param bool  $fromStorage
     */
    public function __construct(array $data = [], bool $fromStorage = false)
    {
        $className = static::class;

        foreach (['TABLE', 'ID', 'AUTO', 'STRUCT'] as $item) {
            if (!defined($className.'::'.$item)) {
                $e = sprintf('The entity const %s was not defined.', $item);

                throw new InvalidArgumentException($e);
            }
        }

        foreach (static::STRUCT as $field => $v) {
            foreach ([
                'construct_prop', 'show_prop', 'create_prop',
                'update_prop', 'create_fill', 'update_fill',
            ] as $type) {
                foreach (['black', 'white'] as $bw) {
                    if (isset($v[$type.'_'.$bw]) && true === $v[$type.'_'.$bw]) {
                        $this->leevelBlackWhites[$type][$bw][] = $field;
                    }
                }
            }
        }

        if ($fromStorage) {
            $this->leevelNewed = false;
        }

        if ($data) {
            foreach ($this->normalizeWhiteAndBlack($data, 'construct_prop') as $prop => $value) {
                if (isset($data[$prop])) {
                    $this->withPropValue($prop, $data[$prop], !$fromStorage, true);
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
    public function __set(string $prop, $value): void
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
        if (0 === strpos($method, 'getter')) {
            if (!method_exists($this, 'getter')) {
                $e = sprintf('The entity `%s` `%s` method was not defined.', static::class, 'getter');

                throw new InvalidArgumentException($e);
            }

            return $this->getter(lcfirst(substr($method, 6)));
        }

        // setter
        if (0 === strpos($method, 'setter')) {
            if (!method_exists($this, 'setter')) {
                $e = sprintf('The entity `%s` `%s` method was not defined.', static::class, 'setter');

                throw new InvalidArgumentException($e);
            }

            $this->setter(lcfirst(substr($method, 6)), $args[0] ?? null);

            return $this;
        }

        // relation
        try {
            $unCamelize = $this->normalize($method);

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

        $this->handleEvent(static::BEFORE_FIND_EVENT);
        $this->handleEvent(static::BEFORE_SELECT_EVENT);

        $data = $this->select()->{$method}(...$args);

        if ($data instanceof Collection) {
            $this->handleEvent(static::AFTER_SELECT_EVENT, $data);
        } else {
            $this->handleEvent(static::AFTER_FIND_EVENT, $data);
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
    public function __toString(): string
    {
        return $this->toJson();
    }

    /**
     * 创建新的实例.
     *
     * @param array $data
     * @param bool  $fromStorage
     *
     * @return static
     */
    public static function make(array $data = [], bool $fromStorage = false): IEntity
    {
        return new static($data, $fromStorage);
    }

    /**
     * 批量修改属性.
     *
     * @param array $data
     *
     * @return $this
     */
    public function withProps(array $data): IEntity
    {
        foreach ($data as $prop => $value) {
            $this->offsetSet($prop, $value);
        }

        return $this;
    }

    /**
     * 自动判断快捷方式.
     *
     * @param array      $data
     * @param null|array $fill
     *
     * @return $this
     */
    public function save(array $data = [], array $fill = null): IEntity
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
    public function create(array $data = [], array $fill = null): IEntity
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
    public function update(array $data = [], array $fill = null): IEntity
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
    public function replace(array $data = [], array $fill = null): IEntity
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
    public static function destroys(array $ids): int
    {
        $count = 0;

        $instance = new static();

        foreach ($instance->whereIn($instance->singlePrimaryKey(), $ids)->findAll() as $entity) {
            if ($entity->destroy()->flush()) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * 销毁模型实体.
     *
     * @return $this
     */
    public function destroy(): IEntity
    {
        if (null === $this->primaryKey()) {
            $e = sprintf('Entity %s has no primary key.', static::class);

            throw new InvalidArgumentException($e);
        }

        $this->leevelFlushed = false;

        $this->leevelFlush = function ($condition) {
            $this->handleEvent(static::BEFORE_DELETE_EVENT, $condition);

            $num = $this->metaConnect()->delete($condition);

            $this->handleEvent(static::AFTER_DELETE_EVENT);

            return $num;
        };

        $this->leevelFlushData = [$this->idCondition()];

        return $this;
    }

    /**
     * 数据持久化数据.
     *
     * @return mixed
     */
    public function flush()
    {
        if (!$this->leevelFlush || true === $this->leevelFlushed) {
            return;
        }

        try {
            $result = call_user_func_array($this->leevelFlush, $this->leevelFlushData);
        } catch (ReplaceException $e) {
            if (false === $this->leevelReplace) {
                throw $e;
            }

            $this->leevelFlush = null;
            $this->leevelFlushData = null;
            $this->updateReal($this->leevelReplace);
            $this->leevelReplace = false;

            return $this->flush();
        }

        $this->leevelFlush = null;
        $this->leevelFlushData = null;
        $this->leevelFlushed = true;

        $this->handleEvent(static::AFTER_SAVE_EVENT);

        return $result;
    }

    /**
     * 获取是否已经持久化数据.
     *
     * @return bool
     */
    public function flushed(): bool
    {
        return $this->leevelFlushed;
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
     * 确定对象是否对应数据库中的一条记录.
     *
     * @return bool
     */
    public function newed(): bool
    {
        return $this->leevelNewed;
    }

    /**
     * 获取主键
     * 唯一标识符.
     *
     * @return mixed
     */
    public function id()
    {
        $result = [];

        foreach (($keys = $this->primaryKeys()) as $value) {
            if (!($tmp = $this->__get($value))) {
                continue;
            }

            $result[$value] = $tmp;
        }

        if (!$result) {
            return;
        }

        // 复合主键，但是数据不完整则忽略
        if (count($keys) > 1 && count($keys) !== count($result)) {
            return;
        }

        if (1 === count($result)) {
            $result = reset($result);
        }

        return $result;
    }

    /**
     * 从数据库重新读取当前对象的属性.
     */
    public function refresh(): void
    {
        $key = $this->primaryKey();

        if (null === $key) {
            $e = sprintf('Entity %s do not have primary key.', static::class);

            throw new InvalidArgumentException($e);
        }

        if (is_array($key)) {
            $map = $this->id();
        } else {
            $map = [$this->singlePrimaryKey(), $this->id()];
        }

        $data = $this
            ->metaConnect()
            ->select()
            ->where($map)
            ->findOne();

        foreach ($data as $k => $v) {
            $this->withPropValue($k, $v, false);
        }
    }

    /**
     * 返回关联数据.
     *
     * @param string $prop
     *
     * @return mixed
     */
    public function loadRelationProp(string $prop)
    {
        if ($result = $this->relationProp($prop)) {
            return $result;
        }

        return $this->loadDataFromRelation($prop);
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
        $prop = $this->normalize($prop);

        $this->validate($prop);

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
        $prop = $this->normalize($prop);

        $this->validate($prop);

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
     * 取得关联数据.
     *
     * @param string $prop
     *
     * @return mixed
     */
    public function relationProp(string $prop)
    {
        $this->validate($prop);

        return $this->propGetter($prop);
    }

    /**
     * 设置关联数据.
     *
     * @param string $prop
     * @param mixed  $value
     */
    public function withRelationProp(string $prop, $value): void
    {
        $this->validate($prop);

        $this->propSetter($prop, $value);
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
    public function belongsTo(string $relatedEntityClass, string $targetKey, string $sourceKey): BelongsTo
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
    public static function eventDispatch(): ?IDispatch
    {
        return static::$leevelDispatch;
    }

    /**
     * 设置模型实体事件处理器.
     *
     * @param null|\Leevel\Event\IDispatch $dispatch
     */
    public static function withEventDispatch(IDispatch $dispatch = null): void
    {
        static::$leevelDispatch = $dispatch;
    }

    /**
     * 注册模型实体事件.
     *
     * @param string                                 $event
     * @param \Closure|\Leevel\Event\Observer|string $listener
     */
    public static function event(string $event, $listener): void
    {
        if (null === static::$leevelDispatch &&
            static::lazyloadPlaceholder() && null === static::$leevelDispatch) {
            return;
        }

        static::isSupportEvent($event);

        static::$leevelDispatch->register(
            "entity.{$event}:".static::class,
            $listener
        );
    }

    /**
     * 执行模型实体事件.
     *
     * @param string $event
     */
    public function handleEvent(string $event, ...$args): void
    {
        if (null === static::$leevelDispatch) {
            return;
        }

        $this->isSupportEvent($event);

        array_unshift($args, $this);
        array_unshift($args, "entity.{$event}:".get_class($this));

        static::$leevelDispatch->handle(...$args);
    }

    /**
     * 验证事件是否受支持
     *
     * @param string $event
     */
    public static function isSupportEvent(string $event): void
    {
        if (!in_array($event, static::supportEvent(), true)) {
            $e = sprintf('Event `%s` do not support.');

            throw new InvalidArgumentException($e);
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
    public function hasChanged(string $prop): bool
    {
        return in_array($prop, $this->leevelChangedProp, true);
    }

    /**
     * 将指定的属性设置已改变.
     *
     * @param array $props
     *
     * @return $this
     */
    public function addChanged(array $props): IEntity
    {
        foreach ($props as $prop) {
            if (!in_array($prop, $this->leevelChangedProp, true)) {
                continue;
            }

            $this->leevelChangedProp[] = $prop;
        }

        return $this;
    }

    /**
     * 删除改变属性.
     *
     * @param array $props
     *
     * @return $this
     */
    public function deleteChanged(array $props): IEntity
    {
        $this->leevelChangedProp = array_values(array_diff($this->leevelChangedProp, $props));

        return $this;
    }

    /**
     * 清空改变属性.
     *
     * @return $this
     */
    public function clearChanged(): IEntity
    {
        $this->leevelChangedProp = [];

        return $this;
    }

    /**
     * 返回主键字段.
     *
     * @return null|array|string
     */
    public static function primaryKey()
    {
        $keys = static::primaryKeys();

        return 1 === count($keys) ? reset($keys) : $keys;
    }

    /**
     * 返回主键字段.
     *
     * @return array
     */
    public static function primaryKeys(): array
    {
        return (array) static::ID;
    }

    /**
     * 返回自动增长字段.
     *
     * @return string
     */
    public static function autoIncrement(): ?string
    {
        return static::AUTO;
    }

    /**
     * 返回字段名字.
     *
     * @return array
     */
    public static function fields(): array
    {
        return static::STRUCT;
    }

    /**
     * 是否存在字段.
     *
     * @param string $field
     *
     * @return bool
     */
    public static function hasField(string $field): bool
    {
        return array_key_exists($field, static::fields());
    }

    /**
     * 返回供查询的主键字段
     * 复合主键或者没有主键直接抛出异常.
     *
     * @return string
     */
    public static function singlePrimaryKey(): string
    {
        $key = static::primaryKey();

        if (!is_string($key)) {
            $e = sprintf('Entity %s do not have primary key or composite id not supported.', static::class);

            throw new InvalidArgumentException($e);
        }

        return $key;
    }

    /**
     * 返回供查询的主键字段值
     * 复合主键或者没有主键直接抛出异常.
     *
     * @return mixed
     */
    public function singleId()
    {
        $this->singlePrimaryKey();

        return $this->id();
    }

    /**
     * 返回设置表.
     *
     * @return string
     */
    public static function table(): string
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
    public function withConnect($connect): IEntity
    {
        $this->leevelConnect = $connect;

        return $this;
    }

    /**
     * 获取 enum.
     * 不存在返回 false.
     *
     * @param string $prop
     * @param mixed  $enum
     * @param string $separate
     *
     * @return mixed
     */
    public static function enum(string $prop, $enum = null, string $separate = ',')
    {
        $prop = static::normalize($prop);
        $enumDefined = static::class.'::'.strtoupper($prop).'_ENUM';

        if (!defined($enumDefined)) {
            return false;
        }

        if (!isset(static::$leevelEnums[static::class]) ||
            !isset(static::$leevelEnums[static::class][$prop])) {
            $enums = constant($enumDefined);
            $enums = array_values($enums);

            foreach ($enums as &$e) {
                if (!isset($e[1])) {
                    $e = sprintf('Invalid enum in the field `%s` of entity `%s`.', $prop, static::class);

                    throw new InvalidArgumentException($e);
                }

                $e[1] = __($e[1]);
            }

            static::$leevelEnums[static::class][$prop] = $enums;
        } else {
            $enums = static::$leevelEnums[static::class][$prop];
        }

        if (null === $enum) {
            return $enums;
        }

        $enums = array_column($enums, 1, 0);
        $enumSep = explode(',', (string) $enum);

        foreach ($enumSep as $v) {
            if (!isset($enums[$v]) && !isset($enums[(int) $v])) {
                $e = sprintf('Value not a enum in the field `%s` of entity `%s`.', $prop, static::class);

                throw new InvalidArgumentException($e);
            }

            $result[] = isset($enums[$v]) ? $enums[$v] : $enums[(int) $v];
        }

        return implode($separate, $result);
    }

    /**
     * 对象转数组.
     *
     * @return array
     */
    public function toArray(): array
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
    public function toJson($option = null): string
    {
        if (null === $option) {
            $option = JSON_UNESCAPED_UNICODE;
        }

        return json_encode($this->toArray(), $option);
    }

    /**
     * 实现 JsonSerializable::jsonSerialize.
     *
     * @return array
     */
    public function jsonSerialize(): array
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
    public function collection(array $entity = []): Collection
    {
        return new Collection($entity);
    }

    /**
     * 获取查询键值.
     *
     * @return array
     */
    public function idCondition(): array
    {
        if (null === (($ids = $this->id()))) {
            $e = sprintf('Entity %s has no primary key data.', static::class);

            throw new InvalidArgumentException($e);
        }

        if (!is_array($ids)) {
            $ids = [$this->singlePrimaryKey() => $ids];
        }

        return $ids;
    }

    /**
     * 设置作用域查询对象.
     *
     * @param \Leevel\Database\Select $select
     */
    public function withScopeSelect(DatabaseSelect $select): void
    {
        $this->leevelScopeSelect = $select;
    }

    /**
     * 返回数据库查询集合对象.
     *
     * @return \Leevel\Database\Select
     */
    public function databaseSelect(): DatabaseSelect
    {
        if ($this->leevelScopeSelect) {
            return $this->leevelScopeSelect;
        }

        return $this
            ->metaConnect()
            ->select()
            ->asClass(static::class, [true])
            ->asCollection();
    }

    /**
     * 返回数据库查询集合对象.
     *
     * @return \Leevel\Database\Ddd\Select
     */
    public function select(): Select
    {
        return new Select($this);
    }

    /**
     * 返回模型实体类的 meta 对象.
     *
     * @return \Leevel\Database\Ddd\IMeta
     */
    public function metaConnect(): IMeta
    {
        return static::meta($this->leevelConnect);
    }

    /**
     * 返回模型实体类的 meta 对象.
     *
     * @param mixed $connect
     *
     * @return \Leevel\Database\Ddd\IMeta
     */
    public static function meta($connect = null): IMeta
    {
        return Meta::instance(static::TABLE)->setConnect($connect);
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
        return $this->hasProp($index);
    }

    /**
     * 实现 ArrayAccess::offsetSet.
     *
     * @param string $index
     * @param mixed  $newval
     */
    public function offsetSet($index, $newval): void
    {
        $this->withPropValue($index, $newval);
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
        return $this->propValue($index);
    }

    /**
     * 实现 ArrayAccess::offsetUnset.
     *
     * @param string $index
     */
    public function offsetUnset($index): void
    {
        $this->offsetSet($index, null);
    }

    /**
     * 保存统一入口.
     *
     * @param string     $method
     * @param array      $data
     * @param null|array $fill
     *
     * @return $this
     */
    protected function saveEntry(string $method, array $data, ?array $fill = null): IEntity
    {
        foreach ($data as $k => $v) {
            $this->withPropValue($k, $v);
        }

        $this->handleEvent(static::BEFORE_SAVE_EVENT);

        // 程序通过内置方法统一实现
        switch (strtolower($method)) {
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
                $ids = $this->id();

                if (is_array($ids)) {
                    $this->replaceReal($fill);
                } else {
                    if (empty($ids)) {
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
    protected function createReal(?array $fill = null): IEntity
    {
        $this->leevelFlushed = false;

        $this->parseAutoFill('create', $fill);

        $propKey = $this->normalizeWhiteAndBlack(
            array_flip($this->leevelChangedProp), 'create_prop'
        );

        $saveData = [];

        foreach ($this->leevelChangedProp as $prop) {
            if (!array_key_exists($prop, $propKey)) {
                continue;
            }

            $saveData[$prop] = $this->__get($prop);
        }

        if (!$saveData) {
            if (null === (($primaryKey = $this->primaryKeys()))) {
                $e = sprintf('Entity %s has no primary key.', static::class);

                throw new InvalidArgumentException($e);
            }

            foreach ($primaryKey as $value) {
                $saveData[$value] = null;
            }
        }

        $this->leevelFlush = function ($saveData) {
            $this->handleEvent(static::BEFORE_CREATE_EVENT, $saveData);

            $lastInsertId = $this->metaConnect()->insert($saveData);

            if ($auto = $this->autoIncrement()) {
                $this->withPropValue($auto, $lastInsertId, false, true);
            }

            $this->leevelNewed = false;

            $this->clearChanged();

            $this->handleEvent(static::AFTER_CREATE_EVENT, $saveData);

            return $lastInsertId;
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
    protected function updateReal(?array $fill = null): IEntity
    {
        $this->leevelFlushed = false;

        $this->parseAutoFill('update', $fill);

        $propKey = $this->normalizeWhiteAndBlack(
            array_flip($this->leevelChangedProp), 'update_prop'
        );

        $saveData = [];

        foreach ($this->leevelChangedProp as $prop) {
            if (!array_key_exists($prop, $propKey)) {
                continue;
            }

            $saveData[$prop] = $this->__get($prop);
        }

        if (!$saveData) {
            return $this;
        }

        $condition = [];

        foreach ($this->primaryKeys() as $field) {
            if (isset($saveData[$field])) {
                unset($saveData[$field]);
            }

            if ($value = $this->__get($field)) {
                $condition[$field] = $value;
            }
        }

        if (empty($condition) || empty($saveData)) {
            return $this;
        }

        $this->leevelFlush = function ($condition, $saveData) {
            $this->handleEvent(static::BEFORE_UPDATE_EVENT, $saveData, $condition);

            $num = $this->metaConnect()->update($condition, $saveData);

            $this->handleEvent(static::BEFORE_UPDATE_EVENT, null, null);

            $this->clearChanged();

            $this->handleEvent(static::AFTER_UPDATE_EVENT);

            return $num;
        };

        $this->leevelFlushData = [$condition, $saveData];

        return $this;
    }

    /**
     * 模拟 replace 数据.
     *
     * @param null|array $fill
     */
    protected function replaceReal(?array $fill = null): void
    {
        $this->leevelReplace = $fill;
        $this->createReal($fill);
    }

    /**
     * 改变属性.
     *
     * @param string $prop
     * @param mixed  $value
     * @param bool   $force
     * @param bool   $ignoreReadonly
     */
    protected function withPropValue(string $prop, $value, bool $force = true, bool $ignoreReadonly = false): void
    {
        $prop = $this->normalize($prop);

        $this->validate($prop);

        if ($this->isRelation($prop)) {
            $e = sprintf('Cannot set a relation prop `%s` on entity `%s`.', $prop, static::class);

            throw new InvalidArgumentException($e);
        }

        $this->propSetter($prop, $value);

        if (!$force) {
            return;
        }

        if (false === $ignoreReadonly &&
            isset(static::STRUCT[$prop]['readonly']) &&
            true === static::STRUCT[$prop]['readonly']) {
            $e = sprintf('Cannot set a read-only prop `%s` on entity `%s`.', $prop, static::class);

            throw new InvalidArgumentException($e);
        }

        if (in_array($prop, $this->leevelChangedProp, true)) {
            return;
        }

        $this->leevelChangedProp[] = $prop;
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
        $prop = $this->normalize($prop);

        $this->validate($prop);

        if (!$this->isRelation($prop)) {
            return $this->propGetter($prop);
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
        $prop = $this->normalize($prop);

        if (!$this->hasField($prop)) {
            return false;
        }

        $prop = $this->asProp($prop);

        if (!property_exists($this, $prop)) {
            $e = sprintf('Prop `%s` of entity `%s` was not defined.', $prop, get_class($this));

            throw new InvalidArgumentException($e);
        }

        return true;
    }

    /**
     * 取得 getter 数据.
     *
     * @param string $prop
     *
     * @return mixed
     */
    protected function propGetter(string $prop)
    {
        return $this->{'getter'.ucfirst($this->asProp($prop))}();
    }

    /**
     * 设置 setter 数据.
     *
     * @param string $prop
     * @param mixed  $value
     */
    protected function propSetter(string $prop, $value): void
    {
        $this->{'setter'.ucfirst($this->asProp($prop))}($value);
    }

    /**
     * 自动填充.
     *
     * @param string $type
     * @param array  $fill
     */
    protected function parseAutoFill(string $type, ?array $fill = null): void
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
    protected function normalizeFill(string $prop, $value): void
    {
        if (null === $value) {
            $camelizeClass = 'fill'.ucfirst($this->asProp($prop));

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
    protected function loadDataFromRelation(string $prop)
    {
        $relation = $this->loadRelation($prop);
        $result = $relation->sourceQuery();

        $this->withRelationProp($prop, $result);

        return $result;
    }

    /**
     * 校验并转换 prop.
     *
     * @param string $prop
     *
     * @return string
     */
    protected function prop(string $prop): string
    {
        $this->validate($prop);

        return $this->asProp($prop);
    }

    /**
     * 验证 getter setter 属性.
     *
     * @param string $prop
     */
    protected function validate(string $prop): void
    {
        $prop = $this->normalize($prop);

        if (!$this->hasProp($prop)) {
            $e = sprintf('Entity `%s` prop or field of struct `%s` was not defined.', get_class($this), $prop);

            throw new InvalidArgumentException($e);
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
                $e = sprintf('Relation `%s` field was not defined.', $v);

                throw new InvalidArgumentException($e);
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
            $e = sprintf(
                'The field `%s`.`%s` of entity `%s` was not defined.',
                $entity->table(), $field, get_class($entity)
            );

            throw new InvalidArgumentException($e);
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
    protected function toArraySource(array $white = [], array $black = [], string $separate = ','): array
    {
        if ($white || $black) {
            $prop = $this->whiteAndBlack(
                $this->fields(), $white, $black
            );
        } else {
            $prop = $this->normalizeWhiteAndBlack(
                $this->fields(), 'show_prop'
            );
        }

        $result = [];

        foreach ($prop as $k => $value) {
            if ($this->isRelation($k)) {
                continue;
            }

            $value = $this->propValue($k);
            $result[$k] = $value;

            $result = static::prepareEnum($k, $result, $separate);
        }

        return $result;
    }

    /**
     * 准备 enum 数据.
     *
     * @param string $prop
     * @param array  $data
     * @param string $separate
     *
     * @return array
     */
    protected static function prepareEnum(string $prop, array $data, string $separate = ','): array
    {
        if (!isset($data[$prop])) {
            return $data;
        }

        if (false === ($enum = static::enum($prop, $data[$prop], $separate))) {
            return $data;
        }

        $data[$prop.'_'.self::ENUM] = $enum;

        return $data;
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
        if ($white) {
            $key = array_intersect_key($key, array_flip($white));
        } elseif ($black) {
            $key = array_diff_key($key, array_flip($black));
        }

        return $key;
    }

    /**
     * 延迟载入占位符.
     *
     * @return bool
     */
    protected static function lazyloadPlaceholder(): bool
    {
        return Lazyload::placeholder();
    }

    /**
     * 统一处理前转换下划线命名风格.
     *
     * @param string $name
     *
     * @return string
     */
    protected static function normalize(string $prop): string
    {
        if (isset(static::$leevelUnCamelize[$prop])) {
            return static::$leevelUnCamelize[$prop];
        }

        return static::$leevelUnCamelize[$prop] = un_camelize($prop);
    }

    /**
     * 返回转驼峰命名.
     *
     * @param string $prop
     *
     * @return string
     */
    protected function asProp(string $prop): string
    {
        if (isset(static::$leevelCamelize[$prop])) {
            return static::$leevelCamelize[$prop];
        }

        return static::$leevelCamelize[$prop] = camelize($prop);
    }
}

// import fn.
class_exists(un_camelize::class);
class_exists(camelize::class);
class_exists(gettext::class);
