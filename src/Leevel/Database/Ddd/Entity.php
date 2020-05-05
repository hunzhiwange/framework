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

namespace Leevel\Database\Ddd;

use ArrayAccess;
use BadMethodCallException;
use Closure;
use Exception;
use InvalidArgumentException;
use JsonSerializable;
use Leevel\Collection\Collection;
use Leevel\Database\Condition;
use Leevel\Database\Ddd\Relation\BelongsTo;
use Leevel\Database\Ddd\Relation\HasMany;
use Leevel\Database\Ddd\Relation\HasOne;
use Leevel\Database\Ddd\Relation\ManyMany;
use Leevel\Database\Ddd\Relation\Relation;
use Leevel\Database\ReplaceException;
use Leevel\Database\Select as DatabaseSelect;
use Leevel\Event\IDispatch;
use Leevel\I18n\gettext;
use function Leevel\I18n\gettext as __;
use function Leevel\Support\Arr\convert_json;
use Leevel\Support\Arr\convert_json;
use Leevel\Support\IArray;
use Leevel\Support\IJson;
use function Leevel\Support\Str\camelize;
use Leevel\Support\Str\camelize;
use function Leevel\Support\Str\un_camelize;
use Leevel\Support\Str\un_camelize;
use RuntimeException;
use Throwable;

/**
 * 模型实体 Object Relational Mapping.
 */
abstract class Entity implements IArray, IJson, JsonSerializable, ArrayAccess
{
    /**
     * 保存前事件.
     *
     * @var string
     */
    const BEFORE_SAVE_EVENT = 'saveing';

    /**
     * 保存后事件.
     *
     * @var string
     */
    const AFTER_SAVE_EVENT = 'saved';

    /**
     * 新建前事件.
     *
     * @var string
     */
    const BEFORE_CREATE_EVENT = 'creating';

    /**
     * 新建后事件.
     *
     * @var string
     */
    const AFTER_CREATE_EVENT = 'created';

    /**
     * 更新前事件.
     *
     * @var string
     */
    const BEFORE_UPDATE_EVENT = 'updating';

    /**
     * 更新后事件.
     *
     * @var string
     */
    const AFTER_UPDATE_EVENT = 'updated';

    /**
     * 删除前事件.
     *
     * @var string
     */
    const BEFORE_DELETE_EVENT = 'deleting';

    /**
     * 删除后事件.
     *
     * @var string
     */
    const AFTER_DELETE_EVENT = 'deleted';

    /**
     * 软删除前事件.
     *
     * @var string
     */
    const BEFORE_SOFT_DELETE_EVENT = 'softDeleting';

    /**
     * 软删除后事件.
     *
     * @var string
     */
    const AFTER_SOFT_DELETE_EVENT = 'softDeleted';

    /**
     * 软删除恢复前事件.
     *
     * @var string
     */
    const BEFORE_SOFT_RESTORE_EVENT = 'softRestoring';

    /**
     * 软删除恢复后事件.
     *
     * @var string
     */
    const AFTER_SOFT_RESTORE_EVENT = 'softRestored';

    /**
     * 新建时间字段.
     *
     * @var string
     */
    const CREATED_AT = 'created_at';

    /**
     * 更新时间字段.
     *
     * @var string
     */
    const UPDATED_AT = 'updated_at';

    /**
     * ENUM.
     *
     * @var string
     */
    const ENUM = 'enum';

    /**
     * 字段只读.
     *
     * - 保护核心字段不被修改
     *
     * @var string
     */
    const READONLY = 'readonly';

    /**
     * 构造器属性黑名单.
     *
     * @var string
     */
    const CONSTRUCT_PROP_BLACK = 'construct_prop_black';

    /**
     * 构造器属性白名单.
     *
     * @var string
     */
    const CONSTRUCT_PROP_WHITE = 'construct_prop_white';

    /**
     * 查询显示属性黑名单.
     *
     * @var string
     */
    const SHOW_PROP_BLACK = 'show_prop_black';

    /**
     * 查询显示属性白名单.
     *
     * @var string
     */
    const SHOW_PROP_WHITE = 'show_prop_white';

    /**
     * 查询显示属性是否允许 NULL.
     *
     * - 系统自动过滤为 null 的值
     *
     * @var string
     */
    const SHOW_PROP_NULL = 'show_prop_null';

    /**
     * 创建属性黑名单.
     *
     * @var string
     */
    const CREATE_PROP_BLACK = 'create_prop_black';

    /**
     * 创建属性白名单.
     *
     * @var string
     */
    const CREATE_PROP_WHITE = 'create_prop_white';

    /**
     * 更新属性黑名单.
     *
     * @var string
     */
    const UPDATE_PROP_BLACK = 'update_prop_black';

    /**
     * 更新属性白名单.
     *
     * @var string
     */
    const UPDATE_PROP_WHITE = 'update_prop_white';

    /**
     * 创建填充属性.
     *
     * @var string
     */
    const CREATE_FILL = 'create_fill';

    /**
     * 更新填充属性.
     *
     * @var string
     */
    const UPDATE_FILL = 'update_fill';

    /**
     * 一对一关联.
     *
     * @var int
     */
    const HAS_ONE = 1;

    /**
     * 从属关联.
     *
     * @var int
     */
    const BELONGS_TO = 2;

    /**
     * 一对多关联.
     *
     * @var int
     */
    const HAS_MANY = 3;

    /**
     * 多对多关联.
     *
     * @var int
     */
    const MANY_MANY = 4;

    /**
     * 关联查询作用域.
     *
     * @var string
     */
    const RELATION_SCOPE = 'relation_scope';

    /**
     * 关联查询源键字段.
     *
     * @var string
     */
    const SOURCE_KEY = 'source_key';

    /**
     * 关联目标键字段.
     *
     * @var string
     */
    const TARGET_KEY = 'target_key';

    /**
     * 关联查询中间表源键字段.
     *
     * @var string
     */
    const MIDDLE_SOURCE_KEY = 'middle_source_key';

    /**
     * 关联查询中间表目标键字段.
     *
     * @var string
     */
    const MIDDLE_TARGET_KEY = 'middle_target_key';

    /**
     * 关联查询中间表实体.
     *
     * @var string
     */
    const MIDDLE_ENTITY = 'middle_entity';

    /**
     * 不包含软删除的数据.
     *
     * @var int
     */
    const WITHOUT_SOFT_DELETED = 1;

    /**
     * 包含软删除的数据.
     *
     * @var int
     */
    const WITH_SOFT_DELETED = 2;

    /**
     * 只包含软删除的数据.
     *
     * @var int
     */
    const ONLY_SOFT_DELETED = 3;

    /**
     * 枚举分隔符号.
     *
     * @var int
     */
    const ENUM_SEPARATE = ',';

    /**
     * 已修改的模型实体属性.
     *
     * @var array
     */
    protected array $changedProp = [];

    /**
     * 黑白名单.
     *
     * @var array
     */
    protected array $blackWhites = [
        'construct_prop' => ['white' => [], 'black' => []],
        'create_prop'    => ['white' => [], 'black' => []],
        'update_prop'    => ['white' => [], 'black' => []],
        'show_prop'      => ['white' => [], 'black' => []],
    ];

    /**
     * 指示对象是否对应数据库中的一条记录.
     *
     * @var bool
     */
    protected bool $isNewed = true;

    /**
     * Replace 模式.
     *
     * - 先插入出现主键重复.
     * - false 表示非 replace 模式，其它值表示 replace 模式附带的 fill 数据.
     *
     * @var mixed
     */
    protected $replaceMode = false;

    /**
     * 多对多关联中间实体.
     *
     * @var \Leevel\Database\Ddd\Entity
     */
    protected ?self $relationMiddle = null;

    /**
     * 持久化基础层.
     *
     * @var \Closure
     */
    protected ?Closure $flush = null;

    /**
     * 即将持久化数据.
     *
     * @var array
     */
    protected ?array $flushData = null;

    /**
     * 模型实体事件处理器.
     *
     * @var \Leevel\Event\IDispatch
     */
    protected static ?IDispatch $dispatch = null;

    /**
     * 缓存驼峰法命名属性.
     *
     * @var array
     */
    protected static array $camelizeProp = [];

    /**
     * 缓存下划线命名属性.
     *
     * @var array
     */
    protected static array $unCamelizeProp = [];

    /**
     * 缓存 ENUM 格式化数据.
     *
     * @var array
     */
    protected static array $enums = [];

    /**
     * 是否为软删除数据.
     *
     * @var bool
     */
    protected bool $isSoftDelete = false;

    /**
     * 是否为软删除恢复数据.
     *
     * @var bool
     */
    protected bool $isSoftRestore = false;

    /**
     * 构造函数.
     *
     * - 为最大化避免 getter setter 属性与系统冲突，设置方法以 with 开头，获取方法不带 get.
     * - ORM 主要基于早年的 QeePHP V2，查询器基于这个版本构建.
     * - 关联模型实现设计参照了 Laravel 的设计.
     * - 也借鉴了 Doctrine 和 Java Hibernate 中关于 getter setter 的设计
     *
     * @see https://github.com/dualface/qeephp2_x
     * @see https://github.com/laravel/framework
     * @see https://github.com/doctrine/doctrine2
     * @see http://hibernate.org/
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(array $data = [], bool $fromStorage = false, bool $ignoreUndefinedProp = false)
    {
        $className = static::class;
        foreach (['TABLE', 'ID', 'AUTO', 'STRUCT'] as $item) {
            if (!defined($className.'::'.$item)) {
                $e = sprintf('The entity const %s was not defined.', $item);

                throw new InvalidArgumentException($e);
            }
        }

        foreach (static::STRUCT as $field => $v) {
            foreach (['construct_prop', 'show_prop', 'create_prop', 'update_prop'] as $type) {
                foreach (['black', 'white'] as $bw) {
                    if (isset($v[$type.'_'.$bw]) && true === $v[$type.'_'.$bw]) {
                        $this->blackWhites[$type][$bw][] = $field;
                    }
                }
            }
        }

        if ($fromStorage) {
            $this->isNewed = false;
        }

        if ($data) {
            foreach ($this->normalizeWhiteAndBlack($data, 'construct_prop') as $prop => $_) {
                if (isset($data[$prop])) {
                    $this->withProp($prop, $data[$prop], !$fromStorage, true, $ignoreUndefinedProp);
                }
            }
        }
    }

    /**
     * 获取数据数据.
     *
     * @return mixed
     */
    public function __get(string $prop)
    {
        return $this->prop($prop);
    }

    /**
     * 更新属性数据.
     *
     * @param mixed $value
     */
    public function __set(string $prop, $value): void
    {
        $this->withProp($prop, $value);
    }

    /**
     * 是否存在属性数据.
     */
    public function __isset(string $prop): bool
    {
        return $this->hasProp($prop);
    }

    /**
     * 删除属性数据.
     */
    public function __unset(string $prop): void
    {
        $this->withProp($prop, null);
    }

    /**
     * call.
     *
     * @throws \BadMethodCallException
     *
     * @return mixed
     */
    public function __call(string $method, array $args)
    {
        // getter
        if (0 === strpos($method, 'get')) {
            return $this->getter(lcfirst(substr($method, 3)));
        }

        // setter
        if (0 === strpos($method, 'set')) {
            $this->setter(lcfirst(substr($method, 3)), $args[0] ?? null);

            return $this;
        }

        // relation tips
        try {
            if ($this->isRelation($unCamelize = static::normalize($method))) {
                $e = sprintf(
                    'Method `%s` is not exits,maybe you can try `%s::make()->relation(\'%s\')`.',
                    $method, static::class, $unCamelize
                );

                throw new BadMethodCallException($e);
            }
        } catch (InvalidArgumentException $th) {
        }

        // other method tips
        $e = sprintf(
            'Method `%s` is not exits,maybe you can try `%s::select|make()->%s(...)`.',
            $method, static::class, $method
        );

        throw new BadMethodCallException($e);
    }

    /**
     * call static.
     *
     * @throws \BadMethodCallException
     */
    public static function __callStatic(string $method, array $args)
    {
        $e = sprintf(
            'Method `%s` is not exits,maybe you can try `%s::select|make()->%s(...)`.',
            $method, static::class, $method
        );

        throw new BadMethodCallException($e);
    }

    /**
     * 将模型实体转化为 JSON.
     */
    public function __toString(): string
    {
        return $this->toJson(...func_get_args());
    }

    /**
     * 创建新的实例.
     *
     * @return \Leevel\Database\Ddd\Entity
     */
    public static function make(array $data = [], bool $fromStorage = false, bool $ignoreUndefinedProp = false): self
    {
        return new static($data, $fromStorage, $ignoreUndefinedProp);
    }

    /**
     * 数据库查询集合对象.
     *
     * - 查询静态方法入口，更好的 IDE 用户体验.
     * - 屏蔽 __callStatic 防止 IDE 无法识别.
     *
     * @return \Leevel\Database\Ddd\Select
     */
    public static function select(int $softDeletedType = self::WITHOUT_SOFT_DELETED): Select
    {
        return new Select(new static(), $softDeletedType);
    }

    /**
     * 数据库查询集合对象.
     *
     * - 查询静态方法入口，更好的 IDE 用户体验.
     * - 屏蔽 __callStatic 防止 IDE 无法识别.
     * - select 别名，致敬经典 QeePHP.
     *
     * @return \Leevel\Database\Ddd\Select
     */
    public static function find(int $softDeletedType = self::WITHOUT_SOFT_DELETED): Select
    {
        return static::select($softDeletedType);
    }

    /**
     * 包含软删除数据的数据库查询集合对象.
     *
     * - 查询静态方法入口，更好的 IDE 用户体验.
     * - 屏蔽 __callStatic 防止 IDE 无法识别.
     * - 获取包含软删除的数据.
     *
     * @return \Leevel\Database\Ddd\Select
     */
    public static function withSoftDeleted(): Select
    {
        return static::select(static::WITH_SOFT_DELETED);
    }

    /**
     * 仅仅包含软删除数据的数据库查询集合对象.
     *
     * - 查询静态方法入口，更好的 IDE 用户体验.
     * - 屏蔽 __callStatic 防止 IDE 无法识别.
     * - 获取只包含软删除的数据.
     *
     * @return \Leevel\Database\Ddd\Select
     */
    public static function onlySoftDeleted(): Select
    {
        return static::select(static::ONLY_SOFT_DELETED);
    }

    /**
     * 数据库查询集合对象.
     */
    public static function selectCollection(int $softDeletedType = self::WITHOUT_SOFT_DELETED): DatabaseSelect
    {
        $select = static::meta()
            ->select()
            ->asSome(fn (...$args): self => new static(...$args), [true, true])
            ->asCollection();

        static::prepareSoftDeleted($select, $softDeletedType);

        return $select;
    }

    /**
     * 返回模型实体类的 meta 对象.
     *
     * @return \Leevel\Database\Ddd\Meta
     */
    public static function meta(): Meta
    {
        return Meta::instance(static::TABLE)
            ->setDatabaseConnect(static::connect());
    }

    /**
     * 数据库连接沙盒.
     *
     * @return mixed
     */
    public static function connectSandbox(?string $connect, Closure $call)
    {
        $old = static::connect();
        static::withConnect($connect);

        try {
            $result = $call();
            static::withConnect($old);
        } catch (Throwable $th) {
            static::withConnect($old);

            throw $th;
        }

        return $result;
    }

    /**
     * 批量设置属性数据.
     *
     * @return \Leevel\Database\Ddd\Entity
     */
    public function withProps(array $data, bool $force = true, bool $ignoreReadonly = false, bool $ignoreUndefinedProp = false): self
    {
        foreach ($data as $prop => $value) {
            $this->withProp($prop, $value, $force, $ignoreReadonly, $ignoreUndefinedProp);
        }

        return $this;
    }

    /**
     * 设置属性数据.
     *
     * @param mixed $value
     *
     * @throws \InvalidArgumentException
     *
     * @return \Leevel\Database\Ddd\Entity
     */
    public function withProp(string $prop, $value, bool $force = true, bool $ignoreReadonly = false, bool $ignoreUndefinedProp = false): self
    {
        $prop = static::normalize($prop);

        try {
            $this->validate($prop);
        } catch (InvalidArgumentException $e) {
            if ($ignoreUndefinedProp) {
                return $this;
            }

            throw $e;
        }

        if ($this->isRelation($prop)) {
            $e = sprintf('Cannot set a relation prop `%s` on entity `%s`.', $prop, static::class);

            throw new InvalidArgumentException($e);
        }

        $this->propSetter($prop, $value);

        if (!$force) {
            return $this;
        }

        if (false === $ignoreReadonly &&
            isset(static::STRUCT[$prop][self::READONLY]) &&
            true === static::STRUCT[$prop][self::READONLY]) {
            $e = sprintf('Cannot set a read-only prop `%s` on entity `%s`.', $prop, static::class);

            throw new InvalidArgumentException($e);
        }

        if (in_array($prop, $this->changedProp, true)) {
            return $this;
        }

        $this->changedProp[] = $prop;

        return $this;
    }

    /**
     * 获取属性数据.
     *
     * @return mixed
     */
    public function prop(string $prop)
    {
        $prop = static::normalize($prop);
        $this->validate($prop);

        if (!$this->isRelation($prop)) {
            return $this->propGetter($prop);
        }

        return $this->relationProp($prop);
    }

    /**
     * 是否存在属性数据.
     */
    public function hasProp(string $prop): bool
    {
        return null !== $this->prop($prop);
    }

    /**
     * 自动判断快捷方式.
     *
     * @return \Leevel\Database\Ddd\Entity
     */
    public function save(array $data = [], ?array $fill = null): self
    {
        $this->saveEntry('save', $data, $fill);

        return $this;
    }

    /**
     * 新增快捷方式.
     *
     * @return \Leevel\Database\Ddd\Entity
     */
    public function create(array $data = [], ?array $fill = null): self
    {
        $this->saveEntry('create', $data, $fill);

        return $this;
    }

    /**
     * 更新快捷方式.
     *
     * @return \Leevel\Database\Ddd\Entity
     */
    public function update(array $data = [], ?array $fill = null): self
    {
        $this->saveEntry('update', $data, $fill);

        return $this;
    }

    /**
     * replace 快捷方式.
     *
     * @return \Leevel\Database\Ddd\Entity
     */
    public function replace(array $data = [], ?array $fill = null): self
    {
        $this->saveEntry('replace', $data, $fill);

        return $this;
    }

    /**
     * 根据主键 ID 删除模型实体.
     */
    public static function destroy(array $ids, bool $forceDelete = false): int
    {
        return static::selectAndDestroyEntitys($ids, 'delete', $forceDelete);
    }

    /**
     * 根据主键 ID 强制删除模型实体.
     */
    public static function forceDestroy(array $ids): int
    {
        return static::destroy($ids, true);
    }

    /**
     * 删除模型实体.
     *
     * @return \Leevel\Database\Ddd\Entity
     */
    public function delete(bool $forceDelete = false): self
    {
        if (false === $forceDelete && defined(static::class.'::DELETE_AT')) {
            return $this->softDelete();
        }

        static::validatePrimaryKey();
        $this->flush = function ($condition) {
            $this->handleEvent(static::BEFORE_DELETE_EVENT, $condition);
            $num = static::meta()->delete($condition);
            $this->handleEvent(static::AFTER_DELETE_EVENT);

            return $num;
        };
        $this->flushData = [$this->idCondition()];

        return $this;
    }

    /**
     * 强制删除模型实体.
     *
     * @return \Leevel\Database\Ddd\Entity
     */
    public function forceDelete(): self
    {
        return $this->delete(true);
    }

    /**
     * 根据主键 ID 删除模型实体.
     */
    public static function softDestroy(array $ids): int
    {
        return static::selectAndDestroyEntitys($ids, 'softDelete');
    }

    /**
     * 从模型实体中软删除数据.
     *
     * @return \Leevel\Database\Ddd\Entity
     */
    public function softDelete(): self
    {
        $this->isSoftDelete = true;
        $this->clearChanged();
        $this->withProp(static::deleteAtColumn(), time());

        return $this->update();
    }

    /**
     * 恢复软删除的模型实体.
     *
     * @return \Leevel\Database\Ddd\Entity
     */
    public function softRestore(): self
    {
        $this->isSoftRestore = true;
        $this->clearChanged();
        $this->withProp(static::deleteAtColumn(), 0);

        return $this->update();
    }

    /**
     * 检查模型实体是否已经被软删除.
     */
    public function softDeleted(): bool
    {
        return (int) $this->prop(static::deleteAtColumn()) > 0;
    }

    /**
     * 获取软删除字段.
     *
     * @throws \InvalidArgumentException
     */
    public static function deleteAtColumn(): string
    {
        if (!defined(static::class.'::DELETE_AT')) {
            $e = sprintf(
                'Entity `%s` soft delete field was not defined.',
                static::class
            );

            throw new InvalidArgumentException($e);
        }

        $deleteAt = static::DELETE_AT;
        if (!static::hasField($deleteAt)) {
            $e = sprintf(
                'Entity `%s` soft delete field `%s` was not found.',
                static::class, $deleteAt
            );

            throw new InvalidArgumentException($e);
        }

        return $deleteAt;
    }

    /**
     * 数据持久化数据.
     *
     * @throws \RuntimeException
     *
     * @return mixed
     */
    public function flush()
    {
        if (!$this->flush) {
            $e = sprintf('Entity `%s` has no data need to be flush.', static::class);

            throw new RuntimeException($e);
        }

        try {
            $flush = $this->flush;
            $result = $flush(...$this->flushData);
        } catch (ReplaceException $e) {
            if (false === $this->replaceMode) {
                throw $e;
            }

            try {
                $this->flush = null;
                $this->flushData = null;
                $this->updateReal($this->replaceMode);
                $this->replaceMode = false;

                return $this->flush();
            } catch (Exception $e) {
                $messages = [
                    sprintf('Entity %s has no primary key data.', static::class),
                    sprintf('Entity `%s` has no data need to be update.', static::class),
                ];
                if (in_array($e->getMessage(), $messages, true)) {
                    return;
                }

                throw $e;
            }
        }

        $this->flush = null;
        $this->flushData = null;
        $this->handleEvent(static::AFTER_SAVE_EVENT);

        return $result;
    }

    /**
     * 获取数据持久化数据.
     */
    public function flushData(): ?array
    {
        return $this->flushData;
    }

    /**
     * 确定对象是否对应数据库中的一条记录.
     */
    public function newed(): bool
    {
        return $this->isNewed;
    }

    /**
     * 获取主键.
     *
     * - 唯一标识符.
     *
     * @return mixed
     */
    public function id()
    {
        $result = [];
        foreach ($keys = static::primaryKeys() as $value) {
            if (!$tmp = $this->prop($value)) {
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
        $data = static::meta()
            ->select()
            ->where($this->idCondition())
            ->findOne();

        foreach ($data as $k => $v) {
            $this->withProp($k, $v, false, true, true);
        }
    }

    /**
     * 是否为关联属性.
     */
    public function isRelation(string $prop): bool
    {
        $prop = static::normalize($prop);
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
     * @throws \InvalidArgumentException
     * @throws \BadMethodCallException
     */
    public function relation(string $prop): Relation
    {
        if (!$this->isRelation($prop)) {
            $e = sprintf(
                'Prop `%s` of entity `%s` is not a relation type.',
                $prop, static::class,
            );

            throw new InvalidArgumentException($e);
        }

        $prop = static::normalize($prop);
        $defined = static::STRUCT[$prop];

        $relationScope = null;
        if (isset($defined[self::RELATION_SCOPE])) {
            $call = [$this, 'relationScope'.ucfirst($defined[self::RELATION_SCOPE])];
            // 如果关联作用域为 private 会触发 __call 魔术方法中的异常
            if (!method_exists($this, $call[1])) {
                $e = sprintf(
                    'Relation scope `%s` of entity `%s` is not exits.',
                    $call[1], static::class,
                );

                throw new BadMethodCallException($e);
            }
            $relationScope = Closure::fromCallable($call);
        }

        if (isset($defined[self::BELONGS_TO])) {
            $this->validateRelationDefined($defined, [self::SOURCE_KEY, self::TARGET_KEY]);

            return $this->belongsTo(
               $defined[self::BELONGS_TO],
               $defined[self::TARGET_KEY],
               $defined[self::SOURCE_KEY],
               $relationScope,
           );
        }

        if (isset($defined[self::HAS_MANY])) {
            $this->validateRelationDefined($defined, [self::SOURCE_KEY, self::TARGET_KEY]);

            return $this->hasMany(
               $defined[self::HAS_MANY],
               $defined[self::TARGET_KEY],
               $defined[self::SOURCE_KEY],
               $relationScope,
           );
        }

        if (isset($defined[self::HAS_ONE])) {
            $this->validateRelationDefined($defined, [self::SOURCE_KEY, self::TARGET_KEY]);

            return $this->hasOne(
               $defined[self::HAS_ONE],
               $defined[self::TARGET_KEY],
               $defined[self::SOURCE_KEY],
               $relationScope,
           );
        }

        $this->validateRelationDefined($defined, [
            self::MIDDLE_ENTITY, self::SOURCE_KEY, self::TARGET_KEY,
            self::MIDDLE_TARGET_KEY, self::MIDDLE_SOURCE_KEY,
        ]);

        return $this->manyMany(
            $defined[self::MANY_MANY],
            $defined[self::MIDDLE_ENTITY],
            $defined[self::TARGET_KEY],
            $defined[self::SOURCE_KEY],
            $defined[self::MIDDLE_TARGET_KEY],
            $defined[self::MIDDLE_SOURCE_KEY],
            $relationScope,
        );
    }

    /**
     * 取得关联数据.
     *
     * @return mixed
     */
    public function relationProp(string $prop)
    {
        $this->validate($prop);
        if ($result = $this->propGetter($prop)) {
            return $result;
        }

        return $this->loadDataFromRelation($prop);
    }

    /**
     * 设置关联数据.
     *
     * @param mixed $value
     */
    public function withRelationProp(string $prop, $value): void
    {
        $this->validate($prop);
        $this->propSetter($prop, $value);
    }

    /**
     * 预加载关联.
     *
     * @return \Leevel\Database\Ddd\Select
     */
    public static function eager(array $relation): Select
    {
        return static::select()->eager($relation);
    }

    /**
     * 设置多对多中间实体.
     *
     * @param \Leevel\Database\Ddd\Entity $middle
     */
    public function withMiddle(self $middle): void
    {
        $this->relationMiddle = $middle;
    }

    /**
     * 获取多对多中间实体.
     *
     * @return \Leevel\Database\Ddd\Entity
     */
    public function middle(): ?self
    {
        return $this->relationMiddle;
    }

    /**
     * 一对一关联.
     */
    public function hasOne(string $relatedEntityClass, string $targetKey, string $sourceKey, ?Closure $scope = null): HasOne
    {
        $entity = new $relatedEntityClass();
        $this->validateRelationField($entity, $targetKey);
        $this->validateRelationField($this, $sourceKey);

        return new HasOne($entity, $this, $targetKey, $sourceKey, $scope);
    }

    /**
     * 定义从属关系.
     */
    public function belongsTo(string $relatedEntityClass, string $targetKey, string $sourceKey, ?Closure $scope = null): BelongsTo
    {
        $entity = new $relatedEntityClass();
        $this->validateRelationField($entity, $targetKey);
        $this->validateRelationField($this, $sourceKey);

        return new BelongsTo($entity, $this, $targetKey, $sourceKey, $scope);
    }

    /**
     * 一对多关联.
     */
    public function hasMany(string $relatedEntityClass, string $targetKey, string $sourceKey, ?Closure $scope = null): HasMany
    {
        $entity = new $relatedEntityClass();
        $this->validateRelationField($entity, $targetKey);
        $this->validateRelationField($this, $sourceKey);

        return new HasMany($entity, $this, $targetKey, $sourceKey, $scope);
    }

    /**
     * 多对多关联.
     */
    public function manyMany(string $relatedEntityClass, string $middleEntityClass, string $targetKey, string $sourceKey, string $middleTargetKey, string $middleSourceKey, ?Closure $scope = null): ManyMany
    {
        $entity = new $relatedEntityClass();
        $middleEntity = new $middleEntityClass();

        $this->validateRelationField($entity, $targetKey);
        $this->validateRelationField($middleEntity, $middleTargetKey);
        $this->validateRelationField($this, $sourceKey);
        $this->validateRelationField($middleEntity, $middleSourceKey);

        return new ManyMany(
            $entity, $this, $middleEntity, $targetKey,
            $sourceKey, $middleTargetKey, $middleSourceKey,
            $scope,
        );
    }

    /**
     * 返回模型实体事件处理器.
     *
     * @return \Leevel\Event\IDispatch
     */
    public static function eventDispatch(): ?IDispatch
    {
        return static::$dispatch;
    }

    /**
     * 设置模型实体事件处理器.
     */
    public static function withEventDispatch(?IDispatch $dispatch = null): void
    {
        static::$dispatch = $dispatch;
    }

    /**
     * 注册模型实体事件.
     *
     * @param \Closure|\Leevel\Event\Observer|string $listener
     *
     * @throws \InvalidArgumentException
     */
    public static function event(string $event, $listener): void
    {
        if (null === static::$dispatch &&
            static::lazyloadPlaceholder() && null === static::$dispatch) {
            $e = 'Event dispatch was not set.';

            throw new InvalidArgumentException($e);
        }

        static::validateSupportEvent($event);
        static::$dispatch->register(
            "entity.{$event}:".static::class,
            $listener
        );
    }

    /**
     * 执行模型实体事件.
     *
     * @param array ...$args
     */
    public function handleEvent(string $event, ...$args): void
    {
        if (null === static::$dispatch) {
            return;
        }

        static::validateSupportEvent($event);
        array_unshift($args, $this);
        array_unshift($args, "entity.{$event}:".get_class($this));

        static::$dispatch->handle(...$args);
    }

    /**
     * 返回受支持的事件.
     */
    public static function supportEvent(): array
    {
        return [
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
     */
    public function changed(): array
    {
        return $this->changedProp;
    }

    /**
     * 检测是否已经改变.
     */
    public function hasChanged(string $prop): bool
    {
        return in_array($prop, $this->changedProp, true);
    }

    /**
     * 将指定的属性设置已改变.
     *
     * @return \Leevel\Database\Ddd\Entity
     */
    public function addChanged(array $props): self
    {
        foreach ($props as $prop) {
            if (in_array($prop, $this->changedProp, true)) {
                continue;
            }

            $this->changedProp[] = $prop;
        }

        return $this;
    }

    /**
     * 删除改变属性.
     *
     * @return \Leevel\Database\Ddd\Entity
     */
    public function deleteChanged(array $props): self
    {
        $this->changedProp = array_values(array_diff($this->changedProp, $props));

        return $this;
    }

    /**
     * 清空改变属性.
     *
     * @return \Leevel\Database\Ddd\Entity
     */
    public function clearChanged(): self
    {
        $this->changedProp = [];

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
        if (!$keys) {
            return null;
        }

        return 1 === count($keys) ? reset($keys) : $keys;
    }

    /**
     * 验证主键是否存在并返回主键字段.
     *
     * @throws \InvalidArgumentException
     *
     * @return array|string
     */
    public static function validatePrimaryKey()
    {
        if (null === $key = static::primaryKey()) {
            $e = sprintf('Entity %s has no primary key.', static::class);

            throw new InvalidArgumentException($e);
        }

        return $key;
    }

    /**
     * 返回主键字段.
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
     */
    public static function fields(): array
    {
        return static::STRUCT;
    }

    /**
     * 是否存在字段.
     */
    public static function hasField(string $field): bool
    {
        return array_key_exists($field, static::fields());
    }

    /**
     * 返回供查询的主键字段.
     *
     * - 复合主键或者没有主键直接抛出异常.
     *
     * @throws \InvalidArgumentException
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
     * 返回供查询的主键字段值.
     *
     * - 复合主键或者没有主键直接抛出异常.
     *
     * @return mixed
     */
    public function singleId()
    {
        static::singlePrimaryKey();

        return $this->id();
    }

    /**
     * 返回设置表.
     */
    public static function table(): string
    {
        return static::TABLE;
    }

    /**
     * 获取枚举.
     *
     * - 不存在返回 false.
     *
     * @param null|mixed $enum
     *
     * @throws \InvalidArgumentException
     *
     * @return mixed
     */
    public static function enum(string $prop, $enum = null)
    {
        $prop = static::normalize($prop);
        $enumDefined = static::class.'::'.strtoupper($prop).'_ENUM';

        if (!defined($enumDefined)) {
            return false;
        }

        if (!isset(static::$enums[static::class]) ||
            !isset(static::$enums[static::class][$prop])) {
            $enums = constant($enumDefined);
            $enums = array_values($enums);

            foreach ($enums as &$e) {
                if (!isset($e[1])) {
                    $e = sprintf('Invalid enum in the field `%s` of entity `%s`.', $prop, static::class);

                    throw new InvalidArgumentException($e);
                }

                $e[1] = __($e[1]);
            }

            static::$enums[static::class][$prop] = $enums;
        } else {
            $enums = static::$enums[static::class][$prop];
        }

        if (null === $enum) {
            return $enums;
        }

        $result = [];
        $enums = array_column($enums, 1, 0);
        foreach (explode(',', (string) $enum) as $v) {
            if (!isset($enums[$v]) && !isset($enums[(int) $v])) {
                $e = sprintf('Value not a enum in the field `%s` of entity `%s`.', $prop, static::class);

                throw new InvalidArgumentException($e);
            }
            $result[] = $enums[$v] ?? $enums[(int) $v];
        }

        return implode(self::ENUM_SEPARATE, $result);
    }

    /**
     * 对象转数组.
     */
    public function toArray(): array
    {
        return $this->toArraySource(...func_get_args());
    }

    /**
     * 对象转 JSON.
     */
    public function toJson(?int $option = null): string
    {
        $args = func_get_args();
        array_shift($args);

        return convert_json($this->toArray(...$args), $option);
    }

    /**
     * 实现 JsonSerializable::jsonSerialize.
     */
    public function jsonSerialize(): array
    {
        return $this->toArray(...func_get_args());
    }

    /**
     * 创建一个模型实体集合.
     */
    public function collection(array $entity = []): Collection
    {
        return new Collection($entity, [static::class]);
    }

    /**
     * 获取查询键值.
     *
     * @throws \InvalidArgumentException
     */
    public function idCondition(): array
    {
        static::validatePrimaryKey();

        if (null === $ids = $this->id()) {
            $e = sprintf('Entity %s has no primary key data.', static::class);

            throw new InvalidArgumentException($e);
        }

        if (!is_array($ids)) {
            $ids = [static::singlePrimaryKey() => $ids];
        }

        return $ids;
    }

    /**
     * 实现 ArrayAccess::offsetExists.
     *
     * @param string $index
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
        $this->withProp($index, $newval);
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
        return $this->prop($index);
    }

    /**
     * 实现 ArrayAccess::offsetUnset.
     *
     * @param string $index
     */
    public function offsetUnset($index): void
    {
        $this->withProp($index, null);
    }

    /**
     * Setter.
     *
     * @param mixed $value
     *
     * @return \Leevel\Database\Ddd\Entity
     */
    abstract public function setter(string $prop, $value): self;

    /**
     * Getter.
     *
     * @return mixed
     */
    abstract public function getter(string $prop);

    /**
     * Set database connect.
     */
    abstract public static function withConnect(?string $connect = null): void;

    /**
     * Get database connect.
     */
    abstract public static function connect(): ?string;

    /**
     * 验证事件是否受支持.
     *
     * @throws \InvalidArgumentException
     */
    protected static function validateSupportEvent(string $event): void
    {
        if (!in_array($event, static::supportEvent(), true)) {
            $e = sprintf('Event `%s` do not support.', $event);

            throw new InvalidArgumentException($e);
        }
    }

    /**
     * 是否定义属性.
     */
    protected function hasPropDefined(string $prop): bool
    {
        return static::hasField(static::normalize($prop));
    }

    /**
     * 查找并删除实体.
     */
    protected static function selectAndDestroyEntitys(array $ids, string $type, bool $forceDelete = false): int
    {
        $entitys = static::select()
            ->whereIn(static::singlePrimaryKey(), $ids)
            ->findAll();

        /** @var \Leevel\Database\Ddd\Entity $entity */
        foreach ($entitys as $entity) {
            $entity->{$type}($forceDelete)->flush();
        }

        return count($entitys);
    }

    /**
     * 准备软删除查询条件.
     *
     * @throws \InvalidArgumentException
     */
    protected static function prepareSoftDeleted(DatabaseSelect $select, int $softDeletedType): void
    {
        if (!defined(static::class.'::DELETE_AT')) {
            return;
        }

        switch ($softDeletedType) {
            case self::WITH_SOFT_DELETED:
                break;
            case self::ONLY_SOFT_DELETED:
                $select->where(static::deleteAtColumn(), '>', 0);

                break;
            case self::WITHOUT_SOFT_DELETED:
                $select->where(static::deleteAtColumn(), 0);

                break;
            default:
                $e = sprintf('Invalid soft deleted type %d.', $softDeletedType);

                throw new InvalidArgumentException($e);
        }
    }

    /**
     * 保存统一入口.
     *
     * @return \Leevel\Database\Ddd\Entity
     */
    protected function saveEntry(string $method, array $data, ?array $fill = null): self
    {
        foreach ($data as $k => $v) {
            $this->withProp($k, $v);
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
     * @throws \InvalidArgumentException
     *
     * @return \Leevel\Database\Ddd\Entity
     */
    protected function createReal(?array $fill = null): self
    {
        $this->parseAutoFill('create', $fill);
        $saveData = $this->normalizeWhiteAndBlackChangedData('create');

        $this->flush = function (array $saveData): ?int {
            $this->handleEvent(static::BEFORE_CREATE_EVENT, $saveData);

            $lastInsertId = static::meta()->insert($saveData);
            if ($auto = $this->autoIncrement()) {
                $this->withProp($auto, $lastInsertId, false, true, true);
            }
            $this->isNewed = false;
            $this->clearChanged();

            $this->handleEvent(static::AFTER_CREATE_EVENT, $saveData);

            return $lastInsertId;
        };
        $this->flushData = [$saveData];

        return $this;
    }

    /**
     * 更新数据.
     *
     * @throws \RuntimeException
     *
     * @return \Leevel\Database\Ddd\Entity
     */
    protected function updateReal(?array $fill = null): self
    {
        $this->parseAutoFill('update', $fill);
        $saveData = $this->normalizeWhiteAndBlackChangedData('update');
        foreach ($condition = $this->idCondition() as $field => $value) {
            if (isset($saveData[$field])) {
                unset($saveData[$field]);
            }
        }

        if (!$saveData) {
            $e = sprintf('Entity `%s` has no data need to be update.', static::class);

            throw new RuntimeException($e);
        }

        if ($hasVersion = defined(static::class.'::VERSION')) {
            $condition[static::VERSION] = $this->prop(static::VERSION);
            $saveData[static::VERSION] = Condition::raw('['.static::VERSION.']+1');
        }

        $this->flush = function (array $condition, array $saveData) use ($hasVersion): int {
            $this->handleEvent(static::BEFORE_UPDATE_EVENT, $saveData, $condition);
            if (true === $this->isSoftDelete) {
                $this->handleEvent(static::BEFORE_SOFT_DELETE_EVENT, $saveData, $condition);
            }
            if (true === $this->isSoftRestore) {
                $this->handleEvent(static::BEFORE_SOFT_RESTORE_EVENT, $saveData, $condition);
            }

            $num = static::meta()->update($condition, $saveData);
            $this->clearChanged();
            if ($hasVersion) {
                $this->withProp(static::VERSION, $condition[static::VERSION] + 1, false);
            }

            $this->handleEvent(static::AFTER_UPDATE_EVENT);
            if (true === $this->isSoftDelete) {
                $this->handleEvent(static::AFTER_SOFT_DELETE_EVENT);
                $this->isSoftDelete = false;
            }
            if (true === $this->isSoftRestore) {
                $this->handleEvent(static::AFTER_SOFT_RESTORE_EVENT);
                $this->isSoftRestore = false;
            }

            return $num;
        };
        $this->flushData = [$condition, $saveData];

        return $this;
    }

    /**
     * 模拟 replace 数据.
     */
    protected function replaceReal(?array $fill = null): void
    {
        $this->replaceMode = $fill;
        $this->createReal($fill);
    }

    /**
     * 整理黑白名单变更数据.
     */
    protected function normalizeWhiteAndBlackChangedData(string $type): array
    {
        $propKey = $this->normalizeWhiteAndBlack(
            array_flip($this->changedProp), $type.'_prop'
        );

        return $this->normalizeChangedData($propKey);
    }

    /**
     * 整理变更数据.
     */
    protected function normalizeChangedData(array $propKey): array
    {
        $saveData = [];
        foreach ($this->changedProp as $prop) {
            if (!array_key_exists($prop, $propKey)) {
                continue;
            }
            $saveData[$prop] = $this->prop($prop);
        }

        return $saveData;
    }

    /**
     * 取得 getter 数据.
     *
     * @return mixed
     */
    protected function propGetter(string $prop)
    {
        $method = 'get'.ucfirst($prop = $this->asProp($prop));
        if (method_exists($this, $method)) {
            return $this->{$method}($prop);
        }

        return $this->getter($prop);
    }

    /**
     * 设置 setter 数据.
     *
     * @param mixed $value
     */
    protected function propSetter(string $prop, $value): void
    {
        $method = 'set'.ucfirst($prop = $this->asProp($prop));
        if (method_exists($this, $method)) {
            $this->{$method}($value);
        } else {
            $this->setter($prop, $value);
        }
    }

    /**
     * 自动填充.
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
     * @param mixed $value
     */
    protected function normalizeFill(string $prop, $value): void
    {
        if (null === $value) {
            $camelizeClass = 'fill'.ucfirst($this->asProp($prop));
            if (method_exists($this, $camelizeClass)) {
                $value = $this->{$camelizeClass}($this->prop($prop));
            }
        }

        $this->withProp($prop, $value);
    }

    /**
     * 从关联中读取数据.
     *
     * @return mixed
     */
    protected function loadDataFromRelation(string $prop)
    {
        $relation = $this->relation($prop);
        $result = $relation->sourceQuery();
        $this->withRelationProp($prop, $result);

        return $result;
    }

    /**
     * 校验并转换真实属性.
     */
    protected function realProp(string $prop): string
    {
        $this->validate($prop);

        return $this->asProp($prop);
    }

    /**
     * 验证 getter setter 属性.
     *
     * @throws \InvalidArgumentException
     */
    protected function validate(string $prop): void
    {
        $prop = static::normalize($prop);
        if (!$this->hasPropDefined($prop)) {
            $e = sprintf('Entity `%s` prop or field of struct `%s` was not defined.', get_class($this), $prop);

            throw new InvalidArgumentException($e);
        }
    }

    /**
     * 校验关联字段定义.
     *
     * @throws \InvalidArgumentException
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
     * @param \Leevel\Database\Ddd\Entity $entity
     *
     * @throws \InvalidArgumentException
     */
    protected function validateRelationField(self $entity, string $field): void
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
     */
    protected function normalizeWhiteAndBlack(array $key, string $type): array
    {
        return $this->whiteAndBlack(
            $key,
            $this->blackWhites[$type]['white'],
            $this->blackWhites[$type]['black']
        );
    }

    /**
     * 对象转数组.
     */
    protected function toArraySource(array $white = [], array $black = [], array $relationWhiteAndBlack = []): array
    {
        if ($white || $black) {
            $prop = $this->whiteAndBlack($this->fields(), $white, $black);
        } else {
            $prop = $this->normalizeWhiteAndBlack($this->fields(), 'show_prop');
        }

        $result = [];
        foreach ($prop as $k => $option) {
            $isRelationProp = $this->isRelation($k);
            $value = $this->propGetter(static::normalize($k));
            if (null === $value) {
                if (!array_key_exists(self::SHOW_PROP_NULL, $option)) {
                    continue;
                }
                $value = $option[self::SHOW_PROP_NULL];
            } elseif ($isRelationProp) {
                $value = $this->normalizeRelationValue($value, $k, $relationWhiteAndBlack);
            }

            $result[$k] = $value;
            if (!$isRelationProp && null !== $value) {
                $result = static::prepareEnum($k, $result);
            }
        }

        return $result;
    }

    /**
     * 整理关联属性数据.
     */
    protected function normalizeRelationValue(IArray $value, string $prop, array $relationWhiteAndBlack): array
    {
        if (isset($relationWhiteAndBlack[$prop])) {
            list($white, $black, $whiteAndBlack) = array_pad($relationWhiteAndBlack[$prop], 3, []);
        } else {
            $white = $black = $whiteAndBlack = [];
        }

        return $value->toArray($white, $black, $whiteAndBlack);
    }

    /**
     * 准备 enum 数据.
     */
    protected static function prepareEnum(string $prop, array $data): array
    {
        if (false === $enum = static::enum($prop, $data[$prop])) {
            return $data;
        }

        $data[$prop.'_'.self::ENUM] = $enum;

        return $data;
    }

    /**
     * 黑白名单数据解析.
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
     */
    protected static function lazyloadPlaceholder(): bool
    {
        return Lazyload::placeholder();
    }

    /**
     * 统一处理前转换下划线命名风格.
     */
    protected static function normalize(string $prop): string
    {
        if (isset(static::$unCamelizeProp[$prop])) {
            return static::$unCamelizeProp[$prop];
        }

        return static::$unCamelizeProp[$prop] = un_camelize($prop);
    }

    /**
     * 返回转驼峰命名.
     */
    protected function asProp(string $prop): string
    {
        if (isset(static::$camelizeProp[$prop])) {
            return static::$camelizeProp[$prop];
        }

        return static::$camelizeProp[$prop] = camelize($prop);
    }
}

// import fn.
class_exists(un_camelize::class);
class_exists(camelize::class);
class_exists(gettext::class);
class_exists(convert_json::class);
