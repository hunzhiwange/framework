<?php

declare(strict_types=1);

namespace Leevel\Database\Ddd;

use Leevel\Database\Condition;
use Leevel\Database\Ddd\Relation\BelongsTo;
use Leevel\Database\Ddd\Relation\HasMany;
use Leevel\Database\Ddd\Relation\HasOne;
use Leevel\Database\Ddd\Relation\ManyMany;
use Leevel\Database\Ddd\Relation\Relation;
use Leevel\Database\DuplicateKeyException;
use Leevel\Database\Select as DatabaseSelect;
use Leevel\Event\IDispatch;
use Leevel\I18n\Gettext;
use Leevel\Support\Arr\ConvertJson;
use Leevel\Support\Collection;
use Leevel\Support\IArray;
use Leevel\Support\IJson;
use Leevel\Support\Str\Camelize;
use Leevel\Support\Str\UnCamelize;
use OutOfBoundsException;

/**
 * 实体 Object Relational Mapping.
 */
abstract class Entity implements IArray, IJson, \JsonSerializable, \ArrayAccess
{
    /**
     * 初始化全局事件.
     */
    public const BOOT_EVENT = 'boot';

    /**
     * 数据分析前的保存前事件.
     */
    public const BEFORE_SAVE_EVENT = 'save';

    /**
     * 数据分析后的保存前事件.
     */
    public const BEFORE_SAVEING_EVENT = 'saveing';

    /**
     * 保存后事件.
     */
    public const AFTER_SAVED_EVENT = 'saved';

    /**
     * 数据分析前的新建前事件.
     */
    public const BEFORE_CREATE_EVENT = 'create';

    /**
     * 数据分析后的新建前事件.
     */
    public const BEFORE_CREATING_EVENT = 'creating';

    /**
     * 新建后事件.
     */
    public const AFTER_CREATED_EVENT = 'created';

    /**
     * 数据分析前的更新前事件.
     */
    public const BEFORE_UPDATE_EVENT = 'update';

    /**
     * 数据分析后的更新前事件.
     */
    public const BEFORE_UPDATING_EVENT = 'updating';

    /**
     * 更新后事件.
     */
    public const AFTER_UPDATED_EVENT = 'updated';

    /**
     * 数据分析前的删除前事件.
     */
    public const BEFORE_DELETE_EVENT = 'delete';

    /**
     * 数据分析后的删除前事件.
     */
    public const BEFORE_DELETING_EVENT = 'deleting';

    /**
     * 删除后事件.
     */
    public const AFTER_DELETED_EVENT = 'deleted';

    /**
     * 数据分析前的软删除前事件.
     */
    public const BEFORE_SOFT_DELETE_EVENT = 'softDelete';

    /**
     * 数据分析后的软删除前事件.
     */
    public const BEFORE_SOFT_DELETING_EVENT = 'softDeleting';

    /**
     * 软删除后事件.
     */
    public const AFTER_SOFT_DELETED_EVENT = 'softDeleted';

    /**
     * 数据分析前的软删除恢复前事件.
     */
    public const BEFORE_SOFT_RESTORE_EVENT = 'softRestore';

    /**
     * 数据分析后的软删除恢复前事件.
     */
    public const BEFORE_SOFT_RESTORING_EVENT = 'softRestoring';

    /**
     * 软删除恢复后事件.
     */
    public const AFTER_SOFT_RESTORED_EVENT = 'softRestored';

    /**
     * 枚举字段后缀.
     */
    public const ENUM_SUFFIX = 'enum';

    /**
     * 枚举分隔符号.
     */
    public const ENUM_SEPARATE = ',';

    /**
     * 枚举类.
     */
    public const ENUM_CLASS = 'enum_class';

    /**
     * 字段只读.
     *
     * - 保护核心字段不被修改
     */
    public const READONLY = 'readonly';

    /**
     * 构造器属性黑名单.
     */
    public const CONSTRUCT_PROP_BLACK = 'construct_prop_black';

    /**
     * 构造器属性白名单.
     */
    public const CONSTRUCT_PROP_WHITE = 'construct_prop_white';

    /**
     * 查询显示属性黑名单.
     */
    public const SHOW_PROP_BLACK = 'show_prop_black';

    /**
     * 查询显示属性白名单.
     */
    public const SHOW_PROP_WHITE = 'show_prop_white';

    /**
     * 查询显示属性是否允许 NULL.
     *
     * - 系统自动过滤为 null 的值
     * - 如果字段存在设置，则会保留该字段设置的指定值
     */
    public const SHOW_PROP_NULL = 'show_prop_null';

    /**
     * 创建属性黑名单.
     */
    public const CREATE_PROP_BLACK = 'create_prop_black';

    /**
     * 创建属性白名单.
     */
    public const CREATE_PROP_WHITE = 'create_prop_white';

    /**
     * 更新属性黑名单.
     */
    public const UPDATE_PROP_BLACK = 'update_prop_black';

    /**
     * 更新属性白名单.
     */
    public const UPDATE_PROP_WHITE = 'update_prop_white';

    /**
     * 创建填充属性.
     */
    public const CREATE_FILL = 'create_fill';

    /**
     * 更新填充属性.
     */
    public const UPDATE_FILL = 'update_fill';

    /**
     * 一对一关联实体.
     */
    public const HAS_ONE = 1;

    /**
     * 从属关联实体.
     */
    public const BELONGS_TO = 2;

    /**
     * 一对多关联实体.
     */
    public const HAS_MANY = 3;

    /**
     * 多对多关联实体.
     */
    public const MANY_MANY = 4;

    /**
     * 关联查询作用域.
     */
    public const RELATION_SCOPE = 'relation_scope';

    /**
     * 关联查询源键字段.
     */
    public const SOURCE_KEY = 'source_key';

    /**
     * 关联目标键字段.
     */
    public const TARGET_KEY = 'target_key';

    /**
     * 关联查询中间实体源键字段.
     */
    public const MIDDLE_SOURCE_KEY = 'middle_source_key';

    /**
     * 关联查询中间实体目标键字段.
     */
    public const MIDDLE_TARGET_KEY = 'middle_target_key';

    /**
     * 关联查询中间实体.
     */
    public const MIDDLE_ENTITY = 'middle_entity';

    /**
     * 不包含软删除的数据.
     */
    public const WITHOUT_SOFT_DELETED = 1;

    /**
     * 包含软删除的数据.
     */
    public const WITH_SOFT_DELETED = 2;

    /**
     * 只包含软删除的数据.
     */
    public const ONLY_SOFT_DELETED = 3;

    /**
     * 字段名字.
     */
    public const COLUMN_NAME = 'column_name';

    /**
     * 虚拟字段.
     *
     * - 虚拟字段仅用于存储多余的实体数据，比如连表查询后的数据
     * - 不会参与新增和更新
     */
    public const VIRTUAL_COLUMN = 'virtual_column';

    /**
     * 已修改的实体属性.
     */
    protected array $changedProp = [];

    /**
     * 构造器属性白名单.
     */
    protected array $constructPropWhite = [];

    /**
     * 构造器属性黑名单.
     */
    protected array $constructPropBlack = [];

    /**
     * 创建实体属性白名单.
     */
    protected array $createPropWhite = [];

    /**
     * 创建实体属性黑名单.
     */
    protected array $createPropBlack = [];

    /**
     * 更新实体属性白名单.
     */
    protected array $updatePropWhite = [];

    /**
     * 更新实体属性黑名单.
     */
    protected array $updatePropBlack = [];

    /**
     * 字段展示白名单.
     */
    protected array $showPropWhite = [];

    /**
     * 字段展示黑名单.
     */
    protected array $showPropBlack = [];

    /**
     * 设置显示属性每一项值回调.
     */
    protected ?\Closure $showPropEachCallback = null;

    /**
     * 指示对象是否对应数据库中的一条记录.
     */
    protected bool $newed = true;

    /**
     * Replace 模式.
     *
     * - 先插入出现主键或者唯一键重复.
     * - false 表示非 replace 模式，true 表示 replace 模式.
     */
    protected bool $replaceMode = false;

    /**
     * 允许自动填充字段.
     */
    protected ?array $fill = null;

    /**
     * 是否启用乐观锁版本字段.
     */
    protected bool $version = false;

    /**
     * 扩展查询条件.
     */
    protected array $condition = [];

    /**
     * 多对多关联中间实体.
     */
    protected ?self $relationMiddle = null;

    /**
     * 持久化基础层.
     */
    protected ?\Closure $flush = null;

    /**
     * 即将持久化数据.
     */
    protected ?array $flushData = null;

    /**
     * 实体事件处理器.
     */
    protected static ?IDispatch $dispatch = null;

    /**
     * 缓存驼峰法命名属性.
     */
    protected static array $camelizeProp = [];

    /**
     * 缓存下划线命名属性.
     */
    protected static array $unCamelizeProp = [];

    /**
     * 是否为软删除数据.
     */
    protected bool $isSoftDelete = false;

    /**
     * 是否为软删除恢复数据.
     */
    protected bool $isSoftRestore = false;

    /**
     * 唯一键值缓存.
     */
    protected mixed $primaryId = null;

    /**
     * 原始数据.
     */
    protected array $original = [];

    /**
     * 全局数据库连接.
     */
    protected static ?string $globalConnect = null;

    /**
     * 实体初始化.
     */
    protected static array $boot = [];

    /**
     * 全局作用域.
     */
    protected static array $globalScope = [];

    /**
     * 不带指定的全局作用域名字.
     */
    protected static array $withoutGlobalScopeNames = [];

    /**
     * 是否定义了枚举类.
     */
    protected static bool $hasDefinedEnum = false;

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
        if (!isset(static::$boot[static::class])) {
            static::$boot[static::class] = true;
            static::boot();
        }

        foreach (['TABLE', 'ID', 'AUTO', 'STRUCT'] as $item) {
            if (!static::definedEntityConstant($item)) {
                $e = sprintf('The entity const %s was not defined.', $item);

                throw new \InvalidArgumentException($e);
            }
        }

        foreach (static::fields() as $field => $v) {
            // 黑白名单
            foreach ([
                self::CONSTRUCT_PROP_WHITE, self::CONSTRUCT_PROP_BLACK,
                self::CREATE_PROP_WHITE, self::CREATE_PROP_BLACK,
                self::SHOW_PROP_WHITE, self::SHOW_PROP_BLACK,
                self::UPDATE_PROP_WHITE, self::UPDATE_PROP_BLACK,
            ] as $type) {
                if (isset($v[$type]) && true === $v[$type]) {
                    $this->{Camelize::handle($type)}[] = $field;
                }
            }

            // 检查定义的枚举类
            if (isset($v[self::ENUM_CLASS])) {
                if (!enum_exists($v[self::ENUM_CLASS])) {
                    throw new \Exception(sprintf('Enum %s is not exists.', $enumClass));
                }

                static::$hasDefinedEnum = true;
            }
        }

        if ($data) {
            $this->original = $data;
            foreach ($this->normalizeWhiteAndBlack($data, 'construct_prop') as $prop => $_) {
                if (isset($data[$prop])) {
                    $this->withProp($prop, $data[$prop], $fromStorage, true, $ignoreUndefinedProp);
                }
            }
        }

        if ($fromStorage) {
            $this->newed = false;
            // 缓存一次唯一键
            $this->id(false);
        }
    }

    /**
     * 实现魔术方法 __get.
     */
    public function __get(string $prop): mixed
    {
        return $this->prop($prop);
    }

    /**
     * 实现魔术方法 __set.
     */
    public function __set(string $prop, mixed $value): void
    {
        $this->withProp($prop, $value);
    }

    /**
     * 实现魔术方法 __isset.
     */
    public function __isset(string $prop): bool
    {
        return $this->hasProp($prop);
    }

    /**
     * 实现魔术方法 __unset.
     */
    public function __unset(string $prop): void
    {
        $this->withProp($prop, null);
    }

    /**
     * 实现魔术方法 __call.
     *
     * @throws \BadMethodCallException
     */
    public function __call(string $method, array $args): mixed
    {
        // getter
        if (str_starts_with($method, 'get')) {
            return $this->getter(lcfirst(substr($method, 3)));
        }

        // setter
        if (str_starts_with($method, 'set')) {
            $this->setter(lcfirst(substr($method, 3)), $args[0] ?? null);

            return $this;
        }

        // relation tips
        try {
            if (static::isRelation($unCamelize = static::unCamelizeProp($method))) {
                $e = sprintf(
                    'Method `%s` is not exits,maybe you can try `%s::make()->relation(\'%s\')`.',
                    $method,
                    static::class,
                    $unCamelize
                );

                throw new \BadMethodCallException($e);
            }
        } catch (EntityPropNotDefinedException) {
        }

        // other method tips
        $e = sprintf(
            'Method `%s` is not exits,maybe you can try `%s::select|make()->%s(...)`.',
            $method,
            static::class,
            $method
        );

        throw new \BadMethodCallException($e);
    }

    /**
     * 实现魔术方法 __callStatic.
     *
     * @throws \BadMethodCallException
     */
    public static function __callStatic(string $method, array $args): void
    {
        $e = sprintf(
            'Method `%s` is not exits,maybe you can try `%s::select|make()->%s(...)`.',
            $method,
            static::class,
            $method
        );

        throw new \BadMethodCallException($e);
    }

    /**
     * 实现魔术方法 __toString.
     */
    public function __toString(): string
    {
        return $this->toJson();
    }

    /**
     * 实现魔术方法 __clone.
     *
     * - 返回当前实体的复制.
     * - 复制的实体没有唯一键值，保存数据时将会在数据库新增一条记录
     */
    public function __clone()
    {
        if (!$this->newed) {
            foreach (static::primaryKey() as $value) {
                $this->withProp($value, null, false, true);
            }
            $this->newed = true;
        }
        $this->primaryId = null;
    }

    /**
     * 创建实例.
     */
    public static function make(array $data = [], bool $fromStorage = false, bool $ignoreUndefinedProp = false): static
    {
        return new static($data, $fromStorage, $ignoreUndefinedProp);
    }

    /**
     * 新增批量赋值.
     */
    public static function createAssign(array $data, bool $ignoreUndefinedProp = true): static
    {
        return new static($data, false, $ignoreUndefinedProp);
    }

    /**
     * 更新批量赋值.
     */
    public static function updateAssign(array $data, bool $ignoreUndefinedProp = true): static
    {
        return new static($data, true, $ignoreUndefinedProp);
    }

    /**
     * 添加全局作用域.
     */
    public static function addGlobalScope(string $scopeName, \Closure $call): void
    {
        static::$globalScope[static::class][$scopeName] = $call;
    }

    /**
     * 不带指定全局作用域查询.
     */
    public static function withoutGlobalScope(array $scopeNames, int $softDeletedType = self::WITHOUT_SOFT_DELETED): Select
    {
        static::$withoutGlobalScopeNames[static::class] = $scopeNames;

        try {
            $select = static::select($softDeletedType);
        } finally {
            unset(static::$withoutGlobalScopeNames[static::class]);
        }

        return $select;
    }

    /**
     * 获取实体查询对象.
     *
     * - 查询静态方法入口，更好的 IDE 用户体验.
     * - 屏蔽 __callStatic 防止 IDE 无法识别.
     */
    public static function select(int $softDeletedType = self::WITHOUT_SOFT_DELETED): Select
    {
        $select = new Select($entity = new static(), $softDeletedType);
        $withoutGlobalScopeNames = static::$withoutGlobalScopeNames[static::class] ?? null;
        if (isset(static::$globalScope[static::class])) {
            foreach (static::$globalScope[static::class] as $scopeName => $call) {
                if ($withoutGlobalScopeNames && \in_array($scopeName, $withoutGlobalScopeNames, true)) {
                    continue;
                }
                $call($select, $entity);
            }
        }

        return $select;
    }

    /**
     * 获取实体查询对象.
     *
     * - 查询静态方法入口，更好的 IDE 用户体验.
     * - 屏蔽 __callStatic 防止 IDE 无法识别.
     * - select 别名，致敬经典 QeePHP.
     */
    public static function find(int $softDeletedType = self::WITHOUT_SOFT_DELETED): Select
    {
        return static::select($softDeletedType);
    }

    /**
     * 包含软删除数据的实体查询对象.
     *
     * - 查询静态方法入口，更好的 IDE 用户体验.
     * - 屏蔽 __callStatic 防止 IDE 无法识别.
     * - 获取包含软删除的数据.
     */
    public static function withSoftDeleted(): Select
    {
        return static::select(self::WITH_SOFT_DELETED);
    }

    /**
     * 仅仅包含软删除数据的实体查询对象.
     *
     * - 查询静态方法入口，更好的 IDE 用户体验.
     * - 屏蔽 __callStatic 防止 IDE 无法识别.
     * - 获取只包含软删除的数据.
     */
    public static function onlySoftDeleted(): Select
    {
        return static::select(self::ONLY_SOFT_DELETED);
    }

    /**
     * 取得实体仓储.
     */
    public static function repository(?self $entity = null): Repository
    {
        if (!$entity) {
            $entity = static::class;
            $entity = new $entity();
        }

        if (\defined($entity::class.'::REPOSITORY')) {
            $name = $entity::REPOSITORY;
            $repository = new $name($entity, static::eventDispatch());
        } else {
            $repository = new Repository($entity, static::eventDispatch());
        }

        return $repository;
    }

    /**
     * 实体查询集合对象.
     */
    public static function selectCollection(int $softDeletedType = self::WITHOUT_SOFT_DELETED): DatabaseSelect
    {
        $select = static::meta()
            ->select()
            ->asSome(fn (...$args): self => new static(...$args), [true, true])
            ->asCollection()
        ;

        static::prepareSoftDeleted($select, $softDeletedType);

        return $select;
    }

    /**
     * 返回实体类的元对象.
     */
    public static function meta(): Meta
    {
        if (static::shouldVirtual()) {
            return static::virtualMeta();
        }

        return Meta::instance(static::table())
            ->setDatabaseConnect(static::connect())
        ;
    }

    /**
     * 数据库连接沙盒.
     */
    public static function connectSandbox(?string $connect, \Closure $call): mixed
    {
        $old = static::connect();
        static::withConnect($connect);

        try {
            $result = $call();
            static::withConnect($old);
        } catch (\Throwable $e) {
            static::withConnect($old);

            throw $e;
        }

        return $result;
    }

    /**
     * 批量设置属性数据.
     */
    public function withProps(array $data, bool $fromStorage = false, bool $ignoreReadonly = false, bool $ignoreUndefinedProp = false): self
    {
        foreach ($data as $prop => $value) {
            $this->withProp($prop, $value, $fromStorage, $ignoreReadonly, $ignoreUndefinedProp);
        }

        return $this;
    }

    /**
     * 设置属性数据.
     *
     * @throws \InvalidArgumentException
     */
    public function withProp(string $prop, mixed $value, bool $fromStorage = false, bool $ignoreReadonly = false, bool $ignoreUndefinedProp = false): self
    {
        try {
            static::validate($prop = static::unCamelizeProp($prop));
        } catch (EntityPropNotDefinedException $e) {
            if ($ignoreUndefinedProp) {
                return $this;
            }

            throw $e;
        }

        if (static::isRelation($prop)) {
            $e = sprintf('Cannot set a relation prop `%s` on entity `%s`.', $prop, static::class);

            throw new \InvalidArgumentException($e);
        }

        if ($fromStorage) {
            $this->propSetter($prop, $value);
        } else {
            if ($value === $this->propGetter($prop)) {
                return $this;
            }

            if ($ignoreReadonly) {
                $this->propSetter($prop, $value);
            } else {
                $constantStruct = static::fields();
                if (false === $ignoreReadonly
                    && isset($constantStruct[$prop][self::READONLY])
                    && true === $constantStruct[$prop][self::READONLY]) {
                    $e = sprintf('Cannot set a read-only prop `%s` on entity `%s`.', $prop, static::class);

                    throw new \InvalidArgumentException($e);
                }
                $this->propSetter($prop, $value);
            }

            if (\in_array($prop, $this->changedProp, true)) {
                return $this;
            }
            $this->changedProp[] = $prop;
        }

        return $this;
    }

    /**
     * 获取属性数据.
     */
    public function prop(string $prop): mixed
    {
        static::validate($prop = static::unCamelizeProp($prop));
        if (!static::isRelation($prop)) {
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
     * 自动判断操作快捷方式.
     */
    public function save(array $data = []): self
    {
        $this->saveEntry('save', $data);

        return $this;
    }

    /**
     * 新增快捷方式.
     */
    public function create(array $data = []): self
    {
        $this->saveEntry('create', $data);

        return $this;
    }

    /**
     * 更新快捷方式.
     */
    public function update(array $data = []): self
    {
        $this->saveEntry('update', $data);

        return $this;
    }

    /**
     * 替换快捷方式.
     */
    public function replace(array $data = []): self
    {
        $this->saveEntry('replace', $data);

        return $this;
    }

    /**
     * 设置允许自动填充字段.
     */
    public function fill(?array $fill = null): self
    {
        $this->fill = $fill;

        return $this;
    }

    /**
     * 设置允许自动填充字段为所有字段.
     */
    public function fillAll(): self
    {
        $this->fill = ['*'];

        return $this;
    }

    /**
     * 根据主键 ID 删除实体.
     */
    public static function destroy(array $ids, bool $forceDelete = false): int
    {
        return static::selectAndDestroyEntitys($ids, 'delete', $forceDelete);
    }

    /**
     * 根据主键 ID 强制删除实体.
     */
    public static function forceDestroy(array $ids): int
    {
        return static::destroy($ids, true);
    }

    /**
     * 删除实体.
     */
    public function delete(bool $forceDelete = false): self
    {
        $this->handleEvent(self::BEFORE_DELETE_EVENT);

        if (false === $forceDelete && static::definedEntityConstant('DELETE_AT')) {
            return $this->softDelete();
        }

        $condition = $this->idCondition(false);
        if ($this->condition) {
            $condition = array_merge($this->condition, $condition);
        }

        $this->flush = function (array $condition) {
            $this->handleEvent(self::BEFORE_DELETING_EVENT, $condition);
            if (static::shouldVirtual()) {
                $num = $this->virtualDelete($condition);
            } else {
                $num = static::meta()->delete($condition);
            }
            $this->handleEvent(self::AFTER_DELETED_EVENT);

            return $num;
        };
        $this->flushData = [$condition];

        return $this;
    }

    /**
     * 强制删除实体.
     */
    public function forceDelete(): self
    {
        return $this->delete(true);
    }

    /**
     * 根据单一主键 ID 软删除实体.
     */
    public static function softDestroy(array $ids): int
    {
        return static::selectAndDestroyEntitys($ids, 'softDelete');
    }

    /**
     * 从实体中软删除数据.
     */
    public function softDelete(): self
    {
        $this->isSoftDelete = true;
        $this->clearChanged();
        $this->withProp(static::deleteAtColumn(), time());

        return $this->update();
    }

    /**
     * 恢复软删除的实体.
     */
    public function softRestore(): self
    {
        $this->isSoftRestore = true;
        $this->clearChanged();
        $this->withProp(static::deleteAtColumn(), 0);

        return $this->update();
    }

    /**
     * 检查实体是否已经被软删除.
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
        if (!static::definedEntityConstant('DELETE_AT')) {
            $e = sprintf(
                'Entity `%s` soft delete field was not defined.',
                static::class
            );

            throw new \InvalidArgumentException($e);
        }

        $deleteAt = static::entityConstant('DELETE_AT');
        if (!static::hasField($deleteAt)) {
            $e = sprintf(
                'Entity `%s` soft delete field `%s` was not found.',
                static::class,
                $deleteAt
            );

            throw new \InvalidArgumentException($e);
        }

        return $deleteAt;
    }

    /**
     * 数据持久化.
     *
     * - 软删除返回影响行数 (没有属性需要更新将不会执行 SQL，返回结果为 null）
     * - 物理删除返回影响行数
     * - 更新返回影响行数（没有属性需要更新将不会执行 SQL，返回结果为 null）
     * - 新增返回最进插入 ID
     */
    public function flush(): mixed
    {
        if (!$this->flush) {
            // @todo 返回 0 统一格式
            return null;
        }

        try {
            $flush = $this->flush;
            $result = $flush(...$this->flushData);
        } catch (DuplicateKeyException $e) {
            if (false === $this->replaceMode) {
                throw $e;
            }

            try {
                $this->flush = null;
                $this->flushData = null;
                $this->updateReal();
                $this->replaceMode = false;

                return $this->flush();
            } catch (EntityIdentifyConditionException) {
                // 避免新增数据记录唯一值重复时无法正确抛出重复异常
                throw $e;
            }
        }

        $this->flush = null;
        $this->flushData = null;
        $this->replaceMode = false;
        $this->condition = [];
        $this->id(false);
        $this->handleEvent(self::AFTER_SAVED_EVENT);

        return $result;
    }

    /**
     * 获取数据持久化.
     */
    public function flushData(): ?array
    {
        return $this->flushData;
    }

    /**
     * 设置确定对象是否对应数据库中的一条记录.
     */
    public function withNewed(bool $newed = true): self
    {
        $this->newed = $newed;

        return $this;
    }

    /**
     * 确定对象是否对应数据库中的一条记录.
     */
    public function newed(): bool
    {
        return $this->newed;
    }

    /**
     * 获取原始数据.
     */
    public function original(): array
    {
        return $this->original;
    }

    /**
     * 获取唯一值.
     *
     * - 主键优先，唯一键候选.
     * - 数据库唯一键.
     */
    public function id(bool $cached = true): array|false
    {
        if ($cached && null !== $this->primaryId) {
            return $this->primaryId;
        }

        $id = $this->parseUniqueKeyValue(static::primaryKey());
        if (false === $id) {
            if (static::definedEntityConstant('UNIQUE')) {
                foreach (static::entityConstant('UNIQUE') as $uniqueKey) {
                    if (false !== $id = $this->parseUniqueKeyValue($uniqueKey)) {
                        break;
                    }
                }
            }
        }

        return $this->primaryId = $id;
    }

    /**
     * 从数据库重新读取当前对象的属性.
     */
    public function refresh(): void
    {
        $data = static::meta()
            ->select()
            ->where($this->idCondition())
            ->findOne()
        ;
        foreach ($data as $k => $v) {
            $this->withProp($k, $v, true, true, true);
        }
    }

    /**
     * 是否为关联属性.
     */
    public static function isRelation(string $prop): bool
    {
        static::validate($prop = static::unCamelizeProp($prop));
        $struct = static::fields()[$prop];
        if (isset($struct[self::BELONGS_TO])
            || isset($struct[self::HAS_MANY])
            || isset($struct[self::HAS_ONE])
            || isset($struct[self::MANY_MANY])) {
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
        if (!static::isRelation($prop)) {
            $e = sprintf(
                'Prop `%s` of entity `%s` is not a relation type.',
                $prop,
                static::class,
            );

            throw new \InvalidArgumentException($e);
        }

        $prop = static::unCamelizeProp($prop);
        $defined = static::fields()[$prop];

        $relationScope = null;
        if (isset($defined[self::RELATION_SCOPE])) {
            $call = [$this, 'relationScope'.ucfirst($defined[self::RELATION_SCOPE])];
            // 如果关联作用域为 private 会触发 __call 魔术方法中的异常
            if (!method_exists($this, $call[1])) {
                $e = sprintf(
                    'Relation scope `%s` of entity `%s` is not exits.',
                    $call[1],
                    static::class,
                );

                throw new \BadMethodCallException($e);
            }
            $relationScope = \Closure::fromCallable($call);
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
     */
    public function relationProp(string $prop): mixed
    {
        static::validate($prop);
        if ($result = $this->propGetter($prop)) {
            return $result;
        }

        return $this->loadDataFromRelation($prop);
    }

    /**
     * 设置关联数据.
     */
    public function withRelationProp(string $prop, mixed $value): self
    {
        static::validate($prop);
        $this->propSetter($prop, $value);

        return $this;
    }

    /**
     * 预加载关联.
     */
    public static function eager(array $relation): Select
    {
        return static::select()->eager($relation);
    }

    /**
     * 设置多对多中间实体.
     */
    public function withMiddle(self $middle): void
    {
        $this->relationMiddle = $middle;
    }

    /**
     * 获取多对多中间实体.
     */
    public function middle(): ?self
    {
        return $this->relationMiddle;
    }

    /**
     * 一对一关联.
     */
    public function hasOne(string $relatedEntityClass, string $targetKey, string $sourceKey, ?\Closure $scope = null): HasOne
    {
        $entity = new $relatedEntityClass();
        $this->validateRelationField($entity, $targetKey);
        $this->validateRelationField($this, $sourceKey);

        return new HasOne($entity, $this, $targetKey, $sourceKey, $scope);
    }

    /**
     * 定义从属关系.
     */
    public function belongsTo(string $relatedEntityClass, string $targetKey, string $sourceKey, ?\Closure $scope = null): BelongsTo
    {
        $entity = new $relatedEntityClass();
        $this->validateRelationField($entity, $targetKey);
        $this->validateRelationField($this, $sourceKey);

        return new BelongsTo($entity, $this, $targetKey, $sourceKey, $scope);
    }

    /**
     * 一对多关联.
     */
    public function hasMany(string $relatedEntityClass, string $targetKey, string $sourceKey, ?\Closure $scope = null): HasMany
    {
        $entity = new $relatedEntityClass();
        $this->validateRelationField($entity, $targetKey);
        $this->validateRelationField($this, $sourceKey);

        return new HasMany($entity, $this, $targetKey, $sourceKey, $scope);
    }

    /**
     * 多对多关联.
     */
    public function manyMany(string $relatedEntityClass, string $middleEntityClass, string $targetKey, string $sourceKey, string $middleTargetKey, string $middleSourceKey, ?\Closure $scope = null): ManyMany
    {
        $entity = new $relatedEntityClass();
        $middleEntity = new $middleEntityClass();

        $this->validateRelationField($entity, $targetKey);
        $this->validateRelationField($middleEntity, $middleTargetKey);
        $this->validateRelationField($this, $sourceKey);
        $this->validateRelationField($middleEntity, $middleSourceKey);

        return new ManyMany(
            $entity,
            $this,
            $middleEntity,
            $targetKey,
            $sourceKey,
            $middleTargetKey,
            $middleSourceKey,
            $scope,
        );
    }

    /**
     * 返回实体事件处理器.
     */
    public static function eventDispatch(): ?IDispatch
    {
        return static::$dispatch;
    }

    /**
     * 设置实体事件处理器.
     */
    public static function withEventDispatch(?IDispatch $dispatch = null): void
    {
        static::$dispatch = $dispatch;
    }

    /**
     * 注册实体事件.
     *
     * @throws \InvalidArgumentException
     */
    public static function event(string $event, \Closure|\SplObserver|string $listener): void
    {
        if (null === static::$dispatch
            && static::lazyloadPlaceholder() && null === static::$dispatch) {
            $e = 'Event dispatch was not set.';

            throw new \InvalidArgumentException($e);
        }

        static::validateSupportEvent($event);
        static::$dispatch->register(
            "entity.{$event}:".static::class,
            $listener
        );
    }

    /**
     * 执行实体事件.
     */
    public function handleEvent(string $event, ...$args): void
    {
        if (null === static::$dispatch) {
            return;
        }

        static::validateSupportEvent($event);
        array_unshift($args, $this);
        array_unshift($args, "entity.{$event}:".$this::class);

        static::$dispatch->handle(...$args);
    }

    /**
     * 返回受支持的事件.
     */
    public static function supportEvent(): array
    {
        return [
            self::BOOT_EVENT,
            self::BEFORE_SAVE_EVENT,
            self::BEFORE_SAVEING_EVENT,
            self::AFTER_SAVED_EVENT,
            self::BEFORE_CREATE_EVENT,
            self::BEFORE_CREATING_EVENT,
            self::AFTER_CREATED_EVENT,
            self::BEFORE_UPDATE_EVENT,
            self::BEFORE_UPDATING_EVENT,
            self::AFTER_UPDATED_EVENT,
            self::BEFORE_DELETE_EVENT,
            self::BEFORE_DELETING_EVENT,
            self::AFTER_DELETED_EVENT,
            self::BEFORE_SOFT_DELETE_EVENT,
            self::BEFORE_SOFT_DELETING_EVENT,
            self::AFTER_SOFT_DELETED_EVENT,
            self::BEFORE_SOFT_RESTORE_EVENT,
            self::BEFORE_SOFT_RESTORING_EVENT,
            self::AFTER_SOFT_RESTORED_EVENT,
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
     * 检测属性是否已经改变.
     */
    public function hasChanged(string $prop): bool
    {
        return \in_array($prop, $this->changedProp, true);
    }

    /**
     * 添加指定属性为已改变.
     */
    public function addChanged(array $props): self
    {
        foreach ($props as $prop) {
            if (\in_array($prop, $this->changedProp, true)) {
                continue;
            }

            $this->changedProp[] = $prop;
        }

        return $this;
    }

    /**
     * 删除已改变属性.
     */
    public function deleteChanged(array $props): self
    {
        $this->changedProp = array_values(array_diff($this->changedProp, $props));

        return $this;
    }

    /**
     * 清空已改变属性.
     */
    public function clearChanged(): self
    {
        $this->changedProp = [];

        return $this;
    }

    /**
     * 返回主键字段.
     *
     * @throws \InvalidArgumentException
     */
    public static function primaryKey(): array
    {
        $key = (array) static::entityConstant('ID');
        if (\in_array(null, $key, true)) {
            $key = [];
        }

        if (!$key) {
            // 如果没有设置主键，那么所有字段将会变成虚拟主键
            // 如果没有设置主键，但是设置了唯一键，这个时候你可以手动将唯一键设置为虚拟主键，系统不会自动帮你处理
            $key = [];
            foreach (static::fields() as $k => $_) {
                if (!static::isRelation($k)) {
                    $key[] = $k;
                }
            }
            if (!$key) {
                $e = sprintf('Entity %s has no primary key.', static::class);

                throw new \InvalidArgumentException($e);
            }
        }

        return $key;
    }

    /**
     * 返回自动增长字段.
     */
    public static function autoIncrement(): ?string
    {
        return static::entityConstant('AUTO');
    }

    /**
     * 返回字段名字.
     */
    public static function fields(): array
    {
        return static::entityConstant('STRUCT');
    }

    /**
     * 返回字段名字.
     */
    public static function columnNames(): array
    {
        $columnNames = [];
        foreach (static::fields() as $field => $v) {
            if (isset($v[static::COLUMN_NAME])) {
                $columnNames[$field] = __($v[static::COLUMN_NAME]);
            }
        }

        return $columnNames;
    }

    /**
     * 是否存在字段.
     */
    public static function hasField(string $field): bool
    {
        return \array_key_exists($field, static::fields());
    }

    /**
     * 返回供查询的主键字段.
     *
     * - 复合主键直接抛出异常.
     *
     * @throws \InvalidArgumentException
     */
    public static function singlePrimaryKey(): string
    {
        $key = static::primaryKey();
        if (\count($key) > 1) {
            $e = sprintf('Entity %s does not support composite primary keys.', static::class);

            throw new \InvalidArgumentException($e);
        }

        return reset($key);
    }

    /**
     * 返回设置表.
     */
    public static function table(): string
    {
        return static::entityConstant('TABLE');
    }

    /**
     * 设置显示白名单属性.
     */
    public function only(array $onlyPropertys, bool $overrideProperty = false): static
    {
        $entity = clone $this;
        $entity->showPropWhite = $overrideProperty ? $onlyPropertys : [...$this->showPropWhite, ...$onlyPropertys];

        return $entity;
    }

    /**
     * 设置显示黑名单属性.
     */
    public function except(array $exceptPropertys, bool $overrideProperty = false): static
    {
        $entity = clone $this;
        $entity->showPropBlack = $overrideProperty ? $exceptPropertys : [...$this->showPropBlack, ...$exceptPropertys];

        return $entity;
    }

    /**
     * 设置显示属性每一项值回调.
     */
    public function each(\Closure $callback): static
    {
        $entity = clone $this;
        $entity->showPropEachCallback = $callback;

        return $entity;
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        $prop = $this->normalizeWhiteAndBlack($this->fields(), 'show_prop');
        $result = [];
        foreach ($prop as $k => $option) {
            $isRelationProp = static::isRelation($k);
            $value = $this->propGetter(static::unCamelizeProp($k));
            if (null === $value) {
                if (!\array_key_exists(self::SHOW_PROP_NULL, $option)) {
                    continue;
                }
                $value = $option[self::SHOW_PROP_NULL];
                if ($this->showPropEachCallback) {
                    $showPropEachCallback = $this->showPropEachCallback;
                    $value = $showPropEachCallback($value, $k);
                }
            } elseif ($isRelationProp) {
                if ($this->showPropEachCallback) {
                    $showPropEachCallback = $this->showPropEachCallback;
                    $value = $showPropEachCallback($value, $k);
                }
                $value = $value->toArray();
            }

            $result[$k] = $value;
        }

        if ($result) {
            static::prepareEnum($result);
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function toJson(?int $option = null): string
    {
        return ConvertJson::handle($this->toArray(), $option);
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * 创建一个实体集合.
     */
    public function collection(array $entity = []): Collection
    {
        return new Collection($entity, [static::class]);
    }

    /**
     * 获取查询条件.
     *
     * - 主键优先，唯一键候选
     *
     * @throws \Leevel\Database\Ddd\EntityIdentifyConditionException
     */
    public function idCondition(bool $cached = true): array
    {
        if (false === $id = $this->id($cached)) {
            /** @todo 唯一键重复，保存提示这个错误，需要优化 */
            $e = sprintf('Entity %s has no identify condition data.', static::class);

            throw new EntityIdentifyConditionException($e);
        }

        return $id;
    }

    /**
     * 设置扩展查询条件.
     */
    public function condition(array $condition): self
    {
        $this->condition = $condition;

        return $this;
    }

    /**
     * 设置是否启用乐观锁版本字段.
     */
    public function version(bool $version = true): self
    {
        $this->version = $version;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function offsetExists(mixed $index): bool
    {
        return $this->hasProp($index);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet(mixed $index, mixed $newval): void
    {
        $this->withProp($index, $newval);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetGet(mixed $index): mixed
    {
        return $this->prop($index);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetUnset(mixed $offset): void
    {
        $this->withProp($offset, null);
    }

    /**
     * 设置全局数据库连接.
     */
    public static function withGlobalConnect(?string $connect = null, ?\Closure $call = null): void
    {
        static::$globalConnect = $connect;
    }

    /**
     * 获取全局数据库连接.
     */
    public static function globalConnect(): ?string
    {
        return static::$globalConnect;
    }

    /**
     * 是否为虚拟实体.
     */
    public static function shouldVirtual(): bool
    {
        return static::definedEntityConstant('VIRTUAL')
            && true === static::entityConstant('VIRTUAL');
    }

    /**
     * Setter.
     */
    abstract public function setter(string $prop, mixed $value): self;

    /**
     * Getter.
     */
    abstract public function getter(string $prop): mixed;

    /**
     * Set database connect.
     */
    abstract public static function withConnect(?string $connect = null): void;

    /**
     * Get database connect.
     */
    abstract public static function connect(): ?string;

    /**
     * 实体初始化方法.
     */
    protected static function boot(): void
    {
        static::bootEvent();
    }

    /**
     * 实体初始化全局事件.
     */
    protected static function bootEvent(): void
    {
        if (null === static::$dispatch) {
            return;
        }

        static::$dispatch->handle('entity.'.self::BOOT_EVENT.':'.self::class, static::class);
    }

    /**
     * 获取实体常量.
     */
    protected static function entityConstant(string $const): mixed
    {
        return \constant(static::class.'::'.$const);
    }

    /**
     * 是否定义实体常量.
     */
    protected static function definedEntityConstant(string $const): bool
    {
        return \defined(static::class.'::'.$const);
    }

    /**
     * 验证事件是否受支持.
     *
     * @throws \InvalidArgumentException
     */
    protected static function validateSupportEvent(string $event): void
    {
        if (!\in_array($event, static::supportEvent(), true)) {
            $e = sprintf('Event `%s` do not support.', $event);

            throw new \InvalidArgumentException($e);
        }
    }

    /**
     * 是否定义属性.
     */
    protected static function hasPropDefined(string $prop): bool
    {
        return static::hasField(static::unCamelizeProp($prop));
    }

    /**
     * 查找并删除实体.
     */
    protected static function selectAndDestroyEntitys(array $ids, string $type, bool $forceDelete = false): int
    {
        $entitys = static::select()
            ->whereIn(static::singlePrimaryKey(), $ids)
            ->findAll()
        ;

        /** @var \Leevel\Database\Ddd\Entity $entity */
        foreach ($entitys as $entity) {
            $entity->{$type}($forceDelete)->flush();
        }

        return \count($entitys);
    }

    /**
     * 准备软删除查询条件.
     *
     * @throws \InvalidArgumentException
     */
    protected static function prepareSoftDeleted(DatabaseSelect $select, int $softDeletedType): void
    {
        if (!static::definedEntityConstant('DELETE_AT')) {
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

                throw new \InvalidArgumentException($e);
        }
    }

    /**
     * 保存统一入口.
     */
    protected function saveEntry(string $method, array $data): self
    {
        $this->handleEvent(self::BEFORE_SAVE_EVENT);

        foreach ($data as $k => $v) {
            $this->withProp($k, $v);
        }

        $this->handleEvent(self::BEFORE_SAVEING_EVENT);

        // 程序通过内置方法统一实现
        switch (strtolower($method)) {
            case 'create':
                $this->createReal();

                break;

            case 'update':
                $this->updateReal();

                break;

            case 'replace':
            case 'save':
            default:
                $this->replaceReal();

                break;
        }

        return $this;
    }

    /**
     * 添加数据.
     *
     * @throws \InvalidArgumentException
     */
    protected function createReal(): self
    {
        $this->handleEvent(self::BEFORE_CREATE_EVENT);
        $this->parseAutoFill('create');
        $saveData = $this->normalizeWhiteAndBlackChangedData('create');

        $this->flush = function (array $saveData): ?int {
            $this->handleEvent(self::BEFORE_CREATING_EVENT, $saveData);

            if (static::shouldVirtual()) {
                $lastInsertId = $this->virtualInsert($saveData);
            } else {
                $lastInsertId = static::meta()->insert($saveData);
            }
            if (($auto = $this->autoIncrement()) && $lastInsertId) {
                $this->withProp($auto, $lastInsertId, true, true, true);
            }
            $this->newed = false;
            $this->clearChanged();

            $this->handleEvent(self::AFTER_CREATED_EVENT, $saveData);

            return $lastInsertId;
        };
        $this->flushData = [$saveData];

        return $this;
    }

    /**
     * 更新数据.
     */
    protected function updateReal(): self
    {
        $this->handleEvent(self::BEFORE_UPDATE_EVENT);
        if (true === $this->isSoftDelete) {
            $this->handleEvent(self::BEFORE_SOFT_DELETE_EVENT);
        }
        if (true === $this->isSoftRestore) {
            $this->handleEvent(self::BEFORE_SOFT_RESTORE_EVENT);
        }
        $this->parseAutoFill('update');
        $saveData = $this->normalizeWhiteAndBlackChangedData('update');
        foreach ($condition = $this->idCondition() as $field => $value) {
            if (isset($saveData[$field]) && $value === $saveData[$field]) {
                unset($saveData[$field]);
            }
        }
        if (!$saveData) {
            return $this;
        }

        if ($this->condition) {
            $condition = array_merge($this->condition, $condition);
        }

        $hasVersion = $this->parseVersionData($condition, $saveData);
        $this->flush = function (array $condition, array $saveData) use ($hasVersion): int {
            $this->handleEvent(self::BEFORE_UPDATING_EVENT, $saveData, $condition);
            if (true === $this->isSoftDelete) {
                $this->handleEvent(self::BEFORE_SOFT_DELETING_EVENT, $saveData, $condition);
            }
            if (true === $this->isSoftRestore) {
                $this->handleEvent(self::BEFORE_SOFT_RESTORING_EVENT, $saveData, $condition);
            }

            if (static::shouldVirtual()) {
                $num = $this->virtualUpdate($condition, $saveData);
            } else {
                $num = static::meta()->update($condition, $saveData);
            }
            $this->clearChanged();
            if ($hasVersion) {
                $constantVersion = (string) static::entityConstant('VERSION');
                $this->withProp($constantVersion, $condition[$constantVersion] + 1);
            }

            $this->handleEvent(self::AFTER_UPDATED_EVENT);
            if (true === $this->isSoftDelete) {
                $this->handleEvent(self::AFTER_SOFT_DELETED_EVENT);
                $this->isSoftDelete = false;
            }
            if (true === $this->isSoftRestore) {
                $this->handleEvent(self::AFTER_SOFT_RESTORED_EVENT);
                $this->isSoftRestore = false;
            }

            return $num;
        };
        $this->flushData = [$condition, $saveData];

        return $this;
    }

    /**
     * 插入数据 insert (虚拟写入).
     *
     * - 可被重写，存储虚拟实体
     */
    protected function virtualInsert(array $saveData): ?int
    {
        return null;
    }

    /**
     * 更新数据并返回影响行数（虚拟更新）.
     *
     * - 可被重写，存储虚拟实体
     */
    protected function virtualUpdate(array $condition, array $saveData): int
    {
        return 0;
    }

    /**
     * 删除数据并返回影响行数（虚拟删除）.
     *
     * - 可被重写，删除虚拟实体
     */
    protected function virtualDelete(array $condition): int
    {
        return 0;
    }

    /**
     * 获取实体查询对象（虚拟查询）.
     *
     * - 虚拟实体仅用于简单的保存数据对象，不允许重写查询，可以直接赋值即可
     * - 即使重写实现非常复杂，如果以后确实有这个需要再看看
     *
     * @throws \RuntimeException
     */
    final protected static function virtualMeta(): Meta
    {
        throw new \RuntimeException('The virtual entity does not support select.');
    }

    /**
     * 分析乐观锁数据.
     */
    protected function parseVersionData(array &$condition, array &$saveData): bool
    {
        if (false === $this->version || !static::definedEntityConstant('VERSION')) {
            return false;
        }

        $constantVersion = (string) static::entityConstant('VERSION');
        if (!isset($condition[$constantVersion])) {
            if (null === ($versionData = $this->prop($constantVersion))) {
                return false;
            }
            $condition[$constantVersion] = $versionData;
        }

        $saveData[$constantVersion] = Condition::raw('['.$constantVersion.']+1');

        return true;
    }

    /**
     * 模拟 replace 数据.
     */
    protected function replaceReal(): void
    {
        if ($this->newed) {
            $this->replaceMode = true;
            $this->createReal();
        } else {
            $this->updateReal();
        }
    }

    /**
     * 整理黑白名单变更数据.
     */
    protected function normalizeWhiteAndBlackChangedData(string $type): array
    {
        $propKey = $this->normalizeWhiteAndBlack(
            array_flip($this->changedProp),
            $type.'_prop'
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
            if (!\array_key_exists($prop, $propKey)) {
                continue;
            }
            $saveData[$prop] = $this->prop($prop);
        }

        return $saveData;
    }

    /**
     * 取得 getter 数据.
     */
    protected function propGetter(string $prop): mixed
    {
        $method = 'get'.ucfirst($prop = static::camelizeProp($prop));
        $value = $this->getter($prop);
        if (null === $value) {
            return null;
        }

        if (method_exists($this, $method)) {
            return $this->{$method}($prop);
        }

        return $value;
    }

    /**
     * 设置 setter 数据.
     *
     * @throws \RuntimeException
     */
    protected function propSetter(string $prop, mixed $value): void
    {
        $method = 'set'.ucfirst($prop = static::camelizeProp($prop));
        if (null !== $value && method_exists($this, $method)) {
            if (!$this->{$method}($value) instanceof static) {
                $e = sprintf('Return type of entity setter must be instance of %s.', static::class);

                throw new \RuntimeException($e);
            }
        } else {
            $this->setter($prop, $value);
        }
    }

    /**
     * 自动填充.
     */
    protected function parseAutoFill(string $type): void
    {
        if (null === $this->fill) {
            return;
        }

        $fillAll = \in_array('*', $this->fill, true);
        foreach (static::fields() as $prop => $value) {
            if (!$fillAll && !\in_array($prop, $this->fill, true)) {
                continue;
            }

            if (\array_key_exists($type.'_fill', $value)) {
                $this->normalizeFill($prop, $value[$type.'_fill']);
            }
        }
    }

    /**
     * 格式化自动填充.
     */
    protected function normalizeFill(string $prop, mixed $value): void
    {
        if (null === $value) {
            $camelizeClass = 'fill'.ucfirst(static::camelizeProp($prop));
            if (method_exists($this, $camelizeClass)) {
                $value = $this->{$camelizeClass}($this->prop($prop));
            }
        }

        $this->withProp($prop, $value);
    }

    /**
     * 从关联中读取数据.
     */
    protected function loadDataFromRelation(string $prop): mixed
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
        static::validate($prop);

        return static::camelizeProp($prop);
    }

    /**
     * 验证 getter setter 属性.
     *
     * @throws \Leevel\Database\Ddd\EntityPropNotDefinedException
     */
    protected static function validate(string $prop): void
    {
        $prop = static::unCamelizeProp($prop);
        if (!static::hasPropDefined($prop)) {
            $e = sprintf('Entity `%s` prop or field of struct `%s` was not defined.', static::class, $prop);

            throw new EntityPropNotDefinedException($e);
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

                throw new \InvalidArgumentException($e);
            }
        }
    }

    /**
     * 验证关联字段.
     *
     * @throws \InvalidArgumentException
     */
    protected function validateRelationField(self $entity, string $field): void
    {
        if (!$entity->hasField($field)) {
            $e = sprintf(
                'The field `%s`.`%s` of entity `%s` was not defined.',
                $entity->table(),
                $field,
                $entity::class
            );

            throw new \InvalidArgumentException($e);
        }
    }

    /**
     * 格式化黑白名单数据.
     */
    protected function normalizeWhiteAndBlack(array $key, string $type): array
    {
        $type = Camelize::handle($type);

        return $this->whiteAndBlack(
            $key,
            $this->{$type.'White'},
            $this->{$type.'Black'}
        );
    }

    /**
     * 准备枚举数据.
     */
    protected static function prepareEnum(array &$data): void
    {
        if (!static::$hasDefinedEnum) {
            return;
        }

        $fields = static::fields();
        foreach ($data as $prop => $value) {
            if (!(isset($fields[$prop][self::ENUM_CLASS]) && null !== $value)) {
                continue;
            }

            $enumClass = $fields[$prop][self::ENUM_CLASS];

            if (\is_string($value) && str_contains($value, ',')) {
                $enumValue = explode(',', $value);
            } else {
                $enumValue = [$value];
            }

            $tempValue = [];
            foreach ($enumValue as $v) {
                try {
                    $tempValue[] = __($enumClass::description($v));
                } catch (OutOfBoundsException) {
                    // 枚举值不存在不抛出异常，避免业务中新增枚举无法匹配
                    $tempValue[] = '';
                }
            }
            $data[$prop.'_'.self::ENUM_SUFFIX] = implode(self::ENUM_SEPARATE, $tempValue);
        }
    }

    /**
     * 获取指定唯一键的值.
     */
    protected function parseUniqueKeyValue(array $key): array|false
    {
        $result = [];
        foreach ($key as $value) {
            if (null === ($tmp = $this->prop($value))) {
                continue;
            }
            $result[$value] = $tmp;
        }

        if (!$result) {
            return false;
        }

        // 复合主键，但是数据不完整则忽略
        if (\count($key) > 1 && \count($key) !== \count($result)) {
            return false;
        }

        return $result;
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
    protected static function unCamelizeProp(string $prop): string
    {
        if (isset(static::$unCamelizeProp[$prop])) {
            return static::$unCamelizeProp[$prop];
        }

        return static::$unCamelizeProp[$prop] = UnCamelize::handle($prop);
    }

    /**
     * 返回转驼峰命名.
     */
    protected static function camelizeProp(string $prop): string
    {
        if (isset(static::$camelizeProp[$prop])) {
            return static::$camelizeProp[$prop];
        }

        return static::$camelizeProp[$prop] = Camelize::handle($prop);
    }
}

if (!\function_exists(__NAMESPACE__.'\\__')) {
    function __(string $text, ...$data): string
    {
        if (!class_exists(Gettext::class)) {
            return sprintf($text, ...$data);
        }

        return Gettext::handle($text, ...$data);
    }
}
