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
use Leevel\Support\IArray;
use Leevel\Support\IJson;
use Leevel\Support\Str\Camelize;
use Leevel\Support\Str\UnCamelize;
use Leevel\Validate\IValidator;
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
    public const BEFORE_SAVING_EVENT = 'saving';

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
     * 字段备注.
     */
    public const COLUMN_COMMENT = 'column_comment';

    /**
     * 字段验证器.
     */
    public const COLUMN_VALIDATOR = 'column_validator';

    /**
     * 验证器默认场景.
     */
    public const VALIDATOR_SCENES = 'validator_scenes';

    /**
     * 验证器错误消息.
     */
    public const VALIDATOR_MESSAGES = 'validator_messages';

    /**
     * 字段结构.
     */
    public const COLUMN_STRUCT = 'column_struct';

    /**
     * 虚拟字段.
     *
     * - 虚拟字段仅用于存储多余的实体数据，比如连表查询后的数据
     * - 不会参与新增和更新
     */
    public const VIRTUAL_COLUMN = 'virtual_column';

    /**
     * 共享字段.
     *
     * - 共享字段数据，存放一些自定义数据
     */
    public const META = 'meta';

    /**
     * 已修改的实体属性.
     */
    protected array $changedPropFramework = [];

    /**
     * 构造器属性白名单.
     */
    protected array $constructPropWhiteFramework = [];

    /**
     * 构造器属性黑名单.
     */
    protected array $constructPropBlackFramework = [];

    /**
     * 创建实体属性白名单.
     */
    protected array $createPropWhiteFramework = [];

    /**
     * 创建实体属性黑名单.
     */
    protected array $createPropBlackFramework = [];

    /**
     * 更新实体属性白名单.
     */
    protected array $updatePropWhiteFramework = [];

    /**
     * 更新实体属性黑名单.
     */
    protected array $updatePropBlackFramework = [];

    /**
     * 字段展示白名单.
     */
    protected array $showPropWhiteFramework = [];

    /**
     * 字段展示黑名单.
     */
    protected array $showPropBlackFramework = [];

    /**
     * 设置显示属性每一项值回调.
     */
    protected ?\Closure $showPropEachCallbackFramework = null;

    /**
     * 指示对象是否对应数据库中的一条记录.
     */
    protected bool $newedFramework = true;

    /**
     * Replace 模式.
     *
     * - 先插入出现主键或者唯一键重复.
     * - false 表示非 replace 模式，true 表示 replace 模式.
     */
    protected bool $replaceModeFramework = false;

    /**
     * 允许自动填充字段.
     */
    protected ?array $fillFramework = null;

    /**
     * 是否启用乐观锁版本字段.
     */
    protected bool $enabledVersionFramework = false;

    /**
     * 扩展查询条件.
     */
    protected array $conditionFramework = [];

    /**
     * 多对多关联中间实体.
     */
    protected ?self $relationMiddleFramework = null;

    /**
     * 持久化基础层.
     */
    protected ?\Closure $flushFramework = null;

    /**
     * 即将持久化数据.
     */
    protected ?array $flushDataFramework = null;

    /**
     * 实体事件处理器.
     */
    protected static ?IDispatch $dispatchFramework = null;

    /**
     * 缓存驼峰法命名属性.
     */
    protected static array $camelizePropFramework = [];

    /**
     * 缓存下划线命名属性.
     */
    protected static array $unCamelizePropFramework = [];

    /**
     * 是否为软删除数据.
     */
    protected bool $isSoftDeleteFramework = false;

    /**
     * 是否为软删除恢复数据.
     */
    protected bool $isSoftRestoreFramework = false;

    /**
     * 唯一键值缓存.
     */
    protected mixed $primaryIdFramework = null;

    /**
     * 原始数据.
     */
    protected array $originalFramework = [];

    /**
     * 全局数据库连接.
     */
    protected static ?string $globalConnectFramework = null;

    /**
     * 实体初始化.
     */
    protected static array $bootFramework = [];

    /**
     * 全局作用域.
     */
    protected static array $globalScopeFramework = [];

    /**
     * 不带指定的全局作用域名字.
     */
    protected static array $withoutGlobalScopeNamesFramework = [];

    /**
     * 是否定义了枚举类.
     */
    protected static bool $hasDefinedEnumFramework = false;

    /**
     * Database connect.
     */
    protected static array $databaseConnectFramework = [];

    /**
     * 类属性数据缓存.
     */
    protected static array $propertiesCachedFramework = [];

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
        if (!isset(static::$bootFramework[static::class])) {
            static::$bootFramework[static::class] = true;
            static::boot();
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
                    $this->{Camelize::handle($type).'Framework'}[] = $field;
                }
            }

            // 检查定义的枚举类
            if (isset($v[self::ENUM_CLASS])) {
                if (!enum_exists($v[self::ENUM_CLASS])) {
                    throw new \Exception(sprintf('Enum %s is not exists.', $v[self::ENUM_CLASS]));
                }

                static::$hasDefinedEnumFramework = true;
            }
        }

        $this->fillDefaultValueWhenConstruct($fromStorage, $ignoreUndefinedProp);

        if ($data) {
            $this->originalFramework = $data;
            foreach ($this->normalizeWhiteAndBlack($data, 'construct_prop') as $prop => $_) {
                if (isset($data[$prop])) {
                    $this->withProp($prop, $data[$prop], $fromStorage, true, $ignoreUndefinedProp);
                }
            }
        }

        if ($fromStorage) {
            $this->newedFramework = false;
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
        if (!$this->newedFramework) {
            foreach (static::primaryKey() as $value) {
                $this->withProp($value, null, false, true);
            }
            $this->newedFramework = true;
        }
        $this->primaryIdFramework = null;
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
        static::$globalScopeFramework[static::class][$scopeName] = $call;
    }

    /**
     * 不带指定全局作用域查询.
     */
    public static function withoutGlobalScope(array $scopeNames, int $softDeletedType = self::WITHOUT_SOFT_DELETED): Select
    {
        static::$withoutGlobalScopeNamesFramework[static::class] = $scopeNames;

        try {
            $select = static::select($softDeletedType);
        } finally {
            unset(static::$withoutGlobalScopeNamesFramework[static::class]);
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
        $withoutGlobalScopeNames = static::$withoutGlobalScopeNamesFramework[static::class] ?? null;
        if (isset(static::$globalScopeFramework[static::class])) {
            foreach (static::$globalScopeFramework[static::class] as $scopeName => $call) {
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
     * 通过主键或条件查找实体.
     */
    public static function findEntity(null|int|string|array\Closure $idOrCondition = null, array $column = ['*'], int $softDeletedType = self::WITHOUT_SOFT_DELETED): static
    {
        return static::select($softDeletedType)->findEntity($idOrCondition, $column);
    }

    /**
     * 通过主键或条件查找多个实体.
     */
    public static function findMany(null|array|\Closure $idsOrCondition = null, array $column = ['*'], int $softDeletedType = self::WITHOUT_SOFT_DELETED): EntityCollection
    {
        return static::select($softDeletedType)->findMany($idsOrCondition, $column);
    }

    /**
     * 通过主键或条件查找实体，未找到则抛出异常.
     */
    public static function findOrFail(null|int|string|array|\Closure $idOrCondition = null, array $column = ['*'], int $softDeletedType = self::WITHOUT_SOFT_DELETED): static
    {
        return static::select($softDeletedType)->findOrFail($idOrCondition, $column);
    }

    /**
     * 取得实体仓储.
     */
    public static function repository(?self $entity = null): Repository
    {
        if (!$entity) {
            $entity = static::class;

            /** @var static $entity */
            $entity = new $entity();
        }

        if (\defined($entity::class.'::REPOSITORY')) {
            $name = (string) static::entityConstant('REPOSITORY');

            /** @var Repository $repository */
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
            ->asCollection(true, [static::class])
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
        $oldConnect = static::connect();
        $oldGlobalConnect = static::globalConnect();
        static::withConnect($connect);
        static::withGlobalConnect($connect);

        try {
            return $call();
        } catch (\Throwable $e) {
            throw $e;
        } finally {
            static::withConnect($oldConnect);
            static::withGlobalConnect($oldGlobalConnect);
        }
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
            $constantStruct = static::fields();

            if ($ignoreReadonly) {
                $this->propSetter($prop, $value);
            } else {
                if (false === $ignoreReadonly
                    && isset($constantStruct[$prop][self::READONLY])
                    && true === $constantStruct[$prop][self::READONLY]) {
                    $e = sprintf('Cannot set a read-only prop `%s` on entity `%s`.', $prop, static::class);

                    throw new \InvalidArgumentException($e);
                }
                $this->propSetter($prop, $value);
            }

            if (\in_array($prop, $this->changedPropFramework, true)) {
                return $this;
            }
            $this->changedPropFramework[] = $prop;
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
        $this->fillFramework = $fill;

        return $this;
    }

    /**
     * 设置允许自动填充字段为所有字段.
     */
    public function fillAll(): self
    {
        $this->fillFramework = ['*'];

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
        if ($this->conditionFramework) {
            $condition = array_merge($this->conditionFramework, $condition);
        }

        $this->flushFramework = function (array $condition) {
            $this->handleEvent(self::BEFORE_DELETING_EVENT, $condition);
            if (static::shouldVirtual()) {
                $num = $this->virtualDelete($condition);
            } else {
                $num = static::meta()->delete($condition);
            }
            $this->handleEvent(self::AFTER_DELETED_EVENT);

            return $num;
        };
        $this->flushDataFramework = [$condition];

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
        $this->isSoftDeleteFramework = true;
        $this->clearChanged();
        $this->withProp(static::deleteAtColumn(), time());

        return $this->update();
    }

    /**
     * 恢复软删除的实体.
     */
    public function softRestore(): self
    {
        $this->isSoftRestoreFramework = true;
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

        $deleteAt = (string) static::entityConstant('DELETE_AT');
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
        if (!$this->flushFramework) {
            // @todo 返回 0 统一格式
            return null;
        }

        try {
            $flush = $this->flushFramework;

            /** @phpstan-ignore-next-line */
            $result = $flush(...$this->flushDataFramework);
        } catch (DuplicateKeyException $e) {
            if (false === $this->replaceModeFramework) {
                throw $e;
            }

            try {
                $this->flushFramework = null;
                $this->flushDataFramework = null;
                $this->updateReal();
                $this->replaceModeFramework = false;

                return $this->flush();
            } catch (EntityIdentifyConditionException) {
                // 避免新增数据记录唯一值重复时无法正确抛出重复异常
                throw $e;
            }
        }

        $this->flushFramework = null;
        $this->flushDataFramework = null;
        $this->replaceModeFramework = false;
        $this->conditionFramework = [];
        $this->id(false);
        $this->handleEvent(self::AFTER_SAVED_EVENT);

        return $result;
    }

    /**
     * 获取数据持久化.
     */
    public function flushData(): ?array
    {
        return $this->flushDataFramework;
    }

    /**
     * 设置确定对象是否对应数据库中的一条记录.
     */
    public function withNewed(bool $newed = true): self
    {
        $this->newedFramework = $newed;

        return $this;
    }

    /**
     * 确定对象是否对应数据库中的一条记录.
     */
    public function newed(): bool
    {
        return $this->newedFramework;
    }

    /**
     * 获取原始数据.
     */
    public function original(): array
    {
        return $this->originalFramework;
    }

    /**
     * 获取唯一值.
     *
     * - 主键优先，唯一键候选.
     * - 数据库唯一键.
     */
    public function id(bool $cached = true): array|false
    {
        if ($cached && null !== $this->primaryIdFramework) {
            // @phpstan-ignore-next-line
            return $this->primaryIdFramework;
        }

        $id = $this->parseUniqueKeyValue(static::primaryKey());
        if (false === $id) {
            // @todo 查看一下和现在的设计冲突
            if (static::definedEntityConstant('UNIQUE')) {
                foreach ((array) static::entityConstant('UNIQUE') as $uniqueKey) {
                    if (false !== $id = $this->parseUniqueKeyValue($uniqueKey)) {
                        break;
                    }
                }
            }
        }

        return $this->primaryIdFramework = $id;
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
        foreach ((array) $data as $k => $v) {
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
     */
    public function relation(string $prop, null|array|string|\Closure $relationScope = null): Relation
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

        // 关联查询作用域
        $relationScope = $this->prepareRelationScope($defined, $relationScope);

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
        $this->relationMiddleFramework = $middle;
    }

    /**
     * 获取多对多中间实体.
     */
    public function middle(): ?self
    {
        return $this->relationMiddleFramework;
    }

    /**
     * 一对一关联.
     */
    public function hasOne(string $relatedEntityClass, string $targetKey, string $sourceKey, ?\Closure $scope = null): HasOne
    {
        /** @var Entity $entity */
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
        /** @var Entity $entity */
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
        /** @var Entity $entity */
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
        /** @var Entity $entity */
        $entity = new $relatedEntityClass();

        /** @var Entity $middleEntity */
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
        return static::$dispatchFramework;
    }

    /**
     * 设置实体事件处理器.
     */
    public static function withEventDispatch(?IDispatch $dispatch = null): void
    {
        static::$dispatchFramework = $dispatch;
    }

    /**
     * 注册实体事件.
     *
     * @throws \InvalidArgumentException
     */
    public static function event(string $event, \Closure|\SplObserver|string $listener): void
    {
        if (null === static::$dispatchFramework
            && static::lazyloadPlaceholder() && null === static::$dispatchFramework) {
            $e = 'Event dispatch was not set.';

            throw new \InvalidArgumentException($e);
        }

        static::validateSupportEvent($event);
        // @phpstan-ignore-next-line
        static::$dispatchFramework->register(
            "entity.{$event}:".static::class,
            $listener
        );
    }

    /**
     * 执行实体事件.
     */
    public function handleEvent(string $event, ...$args): void // @phpstan-ignore-line
    {
        if (null === static::$dispatchFramework) {
            return;
        }

        static::validateSupportEvent($event);
        array_unshift($args, $this);
        array_unshift($args, "entity.{$event}:".$this::class);

        static::$dispatchFramework->handle(...$args);
    }

    /**
     * 返回受支持的事件.
     */
    public static function supportEvent(): array
    {
        return [
            self::BOOT_EVENT,
            self::BEFORE_SAVE_EVENT,
            self::BEFORE_SAVING_EVENT,
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
        return $this->changedPropFramework;
    }

    /**
     * 检测属性是否已经改变.
     */
    public function hasChanged(string $prop): bool
    {
        return \in_array($prop, $this->changedPropFramework, true);
    }

    /**
     * 添加指定属性为已改变.
     */
    public function addChanged(array $props): self
    {
        foreach ($props as $prop) {
            if (\in_array($prop, $this->changedPropFramework, true)) {
                continue;
            }

            $this->changedPropFramework[] = $prop;
        }

        return $this;
    }

    /**
     * 删除已改变属性.
     */
    public function deleteChanged(array $props): self
    {
        $this->changedPropFramework = array_values(array_diff($this->changedPropFramework, $props));

        return $this;
    }

    /**
     * 清空已改变属性.
     */
    public function clearChanged(): self
    {
        $this->changedPropFramework = [];

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
        // @phpstan-ignore-next-line
        return static::entityConstant('AUTO');
    }

    /**
     * 返回字段名字.
     */
    public static function fields(): array
    {
        if (!isset(static::$propertiesCachedFramework[static::class])) {
            static::propertiesCache(static::class);
        }

        return (array) static::$propertiesCachedFramework[static::class];
    }

    /**
     * 返回所有字段名字.
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
     * 返回字段名字.
     */
    public static function columnName(string $column): string
    {
        $name = static::fields()[$column][static::COLUMN_NAME] ?? '';
        if (!$name) {
            return '';
        }

        return __($name);
    }

    /**
     * 返回字段验证规则.
     */
    public static function columnValidators(string $validatorScenes, array $validatorFields = []): array
    {
        $validatorRules = [];
        $validatorMessages = [];
        foreach (static::fields() as $field => $v) {
            if ($validatorFields && !\in_array($field, $validatorFields, true)) {
                continue;
            }

            if (isset($v[self::COLUMN_VALIDATOR][self::VALIDATOR_SCENES])) {
                $columnValidator = $v[self::COLUMN_VALIDATOR];
                $defaultValidator = (array) $columnValidator[self::VALIDATOR_SCENES];

                // 默认场景
                if (self::VALIDATOR_SCENES === $validatorScenes) {
                    $validatorRules[$field] = $defaultValidator;
                } elseif (\array_key_exists(':'.$validatorScenes, $columnValidator)) { // 合并场景
                    $validatorRules[$field] = array_merge($defaultValidator, (array) $columnValidator[':'.$validatorScenes]);
                } elseif (\array_key_exists($validatorScenes, $columnValidator)) {
                    // 继承场景
                    if (null === $columnValidator[$validatorScenes]) {
                        $validatorRules[$field] = $defaultValidator;
                    } else { // 覆盖场景
                        $validatorRules[$field] = (array) $columnValidator[$validatorScenes];
                    }
                }

                // 自定义消息
                if (isset($v[self::VALIDATOR_MESSAGES])) {
                    $validatorMessages[$field] = $v[self::VALIDATOR_MESSAGES];
                }
            }

            // 枚举校验
            if (!isset($v[self::ENUM_CLASS])) {
                continue;
            }

            $enumClass = $v[self::ENUM_CLASS];
            if (!enum_exists($enumClass)) {
                throw new \Exception(sprintf('Enum %s is not exists.', $enumClass));
            }

            $validatorRules[$field] ??= [];
            $validatorRules[$field][] = ['in', $enumClass::values()];
            // 枚举为可选，可以利用数据库的默认值来填充
            $validatorRules[$field][] = IValidator::OPTIONAL;
        }

        return [$validatorRules, $validatorMessages];
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
        return (string) static::entityConstant('TABLE');
    }

    /**
     * 设置显示白名单属性.
     */
    public function only(array $onlyPropertys, bool $overrideProperty = false): static
    {
        $entity = clone $this;
        $entity->showPropWhiteFramework = $overrideProperty ? $onlyPropertys : [...$this->showPropWhiteFramework, ...$onlyPropertys];

        return $entity;
    }

    /**
     * 设置显示黑名单属性.
     */
    public function except(array $exceptPropertys, bool $overrideProperty = false): static
    {
        $entity = clone $this;
        $entity->showPropBlackFramework = $overrideProperty ? $exceptPropertys : [...$this->showPropBlackFramework, ...$exceptPropertys];

        return $entity;
    }

    /**
     * 设置显示属性每一项值回调.
     */
    public function each(\Closure $callback): static
    {
        $entity = clone $this;
        $entity->showPropEachCallbackFramework = $callback;

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
            $value = $this->propGetter(static::unCamelizeProp($k), true);
            if (null === $value) {
                if (!\array_key_exists(self::SHOW_PROP_NULL, $option)) {
                    continue;
                }
                $value = $option[self::SHOW_PROP_NULL];
                if ($this->showPropEachCallbackFramework) {
                    $showPropEachCallback = $this->showPropEachCallbackFramework;
                    $value = $showPropEachCallback($value, $k);
                }
            } elseif ($isRelationProp) {
                if ($this->showPropEachCallbackFramework) {
                    $showPropEachCallback = $this->showPropEachCallbackFramework;
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
    public function collection(array $entity = []): EntityCollection
    {
        return new EntityCollection($entity, [static::class]);
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
        $this->conditionFramework = $condition;

        return $this;
    }

    /**
     * 设置是否启用乐观锁版本字段.
     */
    public function version(bool $version = true): self
    {
        $this->enabledVersionFramework = $version;

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
    public function setter(string $prop, mixed $value): self
    {
        $this->{$this->realProp($prop)} = $value;

        return $this;
    }

    /**
     * Getter.
     */
    public function getter(string $prop): mixed
    {
        return $this->{$this->realProp($prop)} ?? null;
    }

    /**
     * 设置全局数据库连接.
     */
    public static function withGlobalConnect(?string $connect = null): void
    {
        static::$globalConnectFramework = $connect ?: null;
    }

    /**
     * 获取全局数据库连接.
     */
    public static function globalConnect(): ?string
    {
        return static::$globalConnectFramework;
    }

    /**
     * Set database connect.
     */
    public static function withConnect(?string $connect = null): void
    {
        if ($connect) {
            static::$databaseConnectFramework[static::class] = $connect;
        } elseif (isset(static::$databaseConnectFramework[static::class])) {
            unset(static::$databaseConnectFramework[static::class]);
        }
    }

    /**
     * Get database connect.
     */
    public static function connect(): ?string
    {
        if (!(static::definedEntityConstant('WITHOUT_GLOBAL_CONNECT')
                && true === static::entityConstant('WITHOUT_GLOBAL_CONNECT'))
            && static::$globalConnectFramework) {
            return static::$globalConnectFramework;
        }

        return static::$databaseConnectFramework[static::class] ??
            (\defined($constConnect = static::class.'::CONNECT') ?
                \constant($constConnect) : null);
    }

    /**
     * 获取实体常量.
     */
    public static function entityConstant(string $const): mixed
    {
        return \constant(static::class.'::'.$const);
    }

    /**
     * 是否定义实体常量.
     */
    public static function definedEntityConstant(string $const): bool
    {
        return \defined(static::class.'::'.$const);
    }

    /**
     * 构造时填充默认值.
     */
    protected function fillDefaultValueWhenConstruct(bool $fromStorage = false, bool $ignoreUndefinedProp = false): void
    {
        if (!$fields = static::fields()) {
            return;
        }

        foreach (static::fields() as $field => $v) {
            $camelizeProp = static::camelizeProp($field);
            if (method_exists($this, $defaultValueMethod = $camelizeProp.'DefaultValue')) {
                $this->withProp($field, $this->{$defaultValueMethod}(), $fromStorage, true, $ignoreUndefinedProp);
            }
        }
    }

    protected function parseDatabaseColumnType(string $type): string
    {
        return match ($type) {
            'int', 'tinyint', 'smallint', 'mediumint', 'bigint', 'boolean' => 'int',
            'float', 'double' => 'float',
            default => 'string',
        };
    }

    protected function formatPropValue(string $camelizeProp, mixed $value): mixed
    {
        if (!$fields = static::fields()) {
            return $value;
        }

        $defaultFormat = $fields[static::unCamelizeProp($camelizeProp)][self::COLUMN_STRUCT]['format'] ?? null;
        if (!isset($defaultFormat)) {
            return $value;
        }

        if (\is_callable($defaultFormat)) {
            return $defaultFormat($value);
        }

        if (method_exists($this, $transformValueMethod = $camelizeProp.'FormatValue')) {
            return $this->{$transformValueMethod}($value);
        }

        return $value;
    }

    /**
     * 转换值.
     */
    protected function transformPropValue(string $camelizeProp, mixed $value): mixed
    {
        if (!$fields = static::fields()) {
            return $value;
        }

        $defaultType = $fields[static::unCamelizeProp($camelizeProp)][self::COLUMN_STRUCT]['type'] ?? null;
        if (!isset($defaultType)) {
            return $value;
        }
        $defaultType = $this->parseDatabaseColumnType($defaultType);

        if (method_exists($this, $transformValueMethod = $camelizeProp.'TransformValue')) {
            return $this->{$transformValueMethod}($value);
        }
        if (method_exists($this, $builtinTransformValueMethod = $defaultType.'BuiltinTransformValue')) {
            return $this->{$builtinTransformValueMethod}($value);
        }

        return $value;
    }

    protected function intBuiltinTransformValue(mixed $value): int
    {
        return (int) $value;
    }

    protected function stringBuiltinTransformValue(mixed $value): string
    {
        return (string) $value;
    }

    protected function floatBuiltinTransformValue(mixed $value): float
    {
        return (float) $value;
    }

    /**
     * 类属性数据缓存.
     */
    protected static function propertiesCache(string $className): void
    {
        if (isset(static::$propertiesCachedFramework[$className])) {
            return;
        }

        static::$propertiesCachedFramework[$className] = [];
        if (str_contains($className, '@anonymous')) {
            return;
        }

        /** @phpstan-ignore-next-line */
        $reflectionClass = new \ReflectionClass($className);
        foreach ($reflectionClass->getProperties(\ReflectionProperty::IS_PROTECTED) as $reflectionProperty) {
            if ($reflectionProperty->isStatic()) {
                continue;
            }
            if (!$structAttributes = $reflectionProperty->getAttributes(Struct::class)) {
                continue;
            }

            $propertyStruct = [];
            foreach ($structAttributes as $structAttribute) {
                foreach ($structAttribute->getArguments()[0] as $configKey => $configValue) {
                    $propertyStruct[$configKey] = $configValue;
                }
            }

            static::$propertiesCachedFramework[$className][self::unCamelizeProp($reflectionProperty->getName())] = $propertyStruct;
        }
    }

    /**
     * 实体初始化方法.
     */
    protected static function boot(): void
    {
        static::bootEvent();
        static::bootCheck();
    }

    protected static function bootCheck(): void
    {
        foreach (['TABLE', 'ID', 'AUTO'] as $item) {
            if (!static::definedEntityConstant($item)) {
                $e = sprintf('The entity const %s was not defined.', $item);

                throw new \InvalidArgumentException($e);
            }
        }
    }

    /**
     * 实体初始化全局事件.
     */
    protected static function bootEvent(): void
    {
        if (null === static::$dispatchFramework) {
            return;
        }

        static::$dispatchFramework->handle('entity.'.self::BOOT_EVENT.':'.self::class, static::class);
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
        foreach ($entitys as $entity) { // @phpstan-ignore-line
            $entity->{$type}($forceDelete)->flush();
        }

        // @phpstan-ignore-next-line
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

        $this->handleEvent(self::BEFORE_SAVING_EVENT);

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
     */
    protected function createReal(): self
    {
        $this->handleEvent(self::BEFORE_CREATE_EVENT);
        $this->parseAutoFill('create');
        $saveData = $this->normalizeWhiteAndBlackChangedData('create');
        $this->removeVirtualColumnData($saveData);

        $this->flushFramework = function (array $saveData): string|int {
            $this->handleEvent(self::BEFORE_CREATING_EVENT, $saveData);

            if (static::shouldVirtual()) {
                $lastInsertId = $this->virtualInsert($saveData);
            } else {
                $lastInsertId = static::meta()->insert($saveData);
            }
            if (($auto = $this->autoIncrement()) && $lastInsertId) {
                $this->withProp($auto, $lastInsertId, true, true, true);
            }
            $this->newedFramework = false;
            $this->clearChanged();

            $this->handleEvent(self::AFTER_CREATED_EVENT, $saveData);

            return $lastInsertId;
        };
        $this->flushDataFramework = [$saveData];

        return $this;
    }

    protected function removeVirtualColumnData(array &$data): void
    {
        $fields = static::fields();
        foreach ($data as $field => $v) {
            if (!empty($fields[$field][static::VIRTUAL_COLUMN])) {
                unset($data[$field]);
            }
        }
    }

    /**
     * 更新数据.
     */
    protected function updateReal(): self
    {
        $this->handleEvent(self::BEFORE_UPDATE_EVENT);
        if (true === $this->isSoftDeleteFramework) {
            $this->handleEvent(self::BEFORE_SOFT_DELETE_EVENT);
        }
        if (true === $this->isSoftRestoreFramework) {
            $this->handleEvent(self::BEFORE_SOFT_RESTORE_EVENT);
        }
        $this->parseAutoFill('update');
        $saveData = $this->normalizeWhiteAndBlackChangedData('update');
        foreach ($condition = $this->idCondition() as $field => $value) {
            if (isset($saveData[$field]) && $value === $saveData[$field]) {
                unset($saveData[$field]);
            }
        }

        $this->removeVirtualColumnData($saveData);

        if (!$saveData) {
            return $this;
        }

        if ($this->conditionFramework) {
            $condition = array_merge($this->conditionFramework, $condition);
        }

        $hasVersion = $this->parseVersionData($condition, $saveData);
        $this->flushFramework = function (array $condition, array $saveData) use ($hasVersion): int {
            $this->handleEvent(self::BEFORE_UPDATING_EVENT, $saveData, $condition);
            if (true === $this->isSoftDeleteFramework) {
                $this->handleEvent(self::BEFORE_SOFT_DELETING_EVENT, $saveData, $condition);
            }
            if (true === $this->isSoftRestoreFramework) {
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
            if (true === $this->isSoftDeleteFramework) {
                $this->handleEvent(self::AFTER_SOFT_DELETED_EVENT);
                $this->isSoftDeleteFramework = false;
            }
            if (true === $this->isSoftRestoreFramework) {
                $this->handleEvent(self::AFTER_SOFT_RESTORED_EVENT);
                $this->isSoftRestoreFramework = false;
            }

            return $num;
        };
        $this->flushDataFramework = [$condition, $saveData];

        return $this;
    }

    /**
     * 插入数据 insert (虚拟写入).
     *
     * - 可被重写，存储虚拟实体
     */
    protected function virtualInsert(array $saveData): int|string
    {
        return 1;
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
        if (false === $this->enabledVersionFramework || !static::definedEntityConstant('VERSION')) {
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
        if ($this->newedFramework) {
            $this->replaceModeFramework = true;
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
            array_flip($this->changedPropFramework),
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
        foreach ($this->changedPropFramework as $prop) {
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
    protected function propGetter(string $prop, bool $transformGetterPropValue = false): mixed
    {
        $method = 'get'.ucfirst($prop = static::camelizeProp($prop));
        $value = $this->getter($prop);
        if (null === $value) {
            return null;
        }

        if (method_exists($this, $method)) {
            // @todo 自定义 getter 是否需要纳入格式化
            return $this->{$method}($prop);
        }

        // @todo 只有 toArray 纳入格式化，还是所有获取值都纳入格式化
        if ($transformGetterPropValue) {
            $value = $this->formatPropValue($prop, $value);
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
        $prop = static::camelizeProp($prop);
        $value = $this->transformPropValue($prop, $value);

        $method = 'set'.ucfirst($prop);
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
        if (null === $this->fillFramework) {
            return;
        }

        $fillAll = \in_array('*', $this->fillFramework, true);
        foreach (static::fields() as $prop => $value) {
            // @phpstan-ignore-next-line
            if (!$fillAll && !\in_array($prop, $this->fillFramework, true)) {
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
            $this->{$type.'WhiteFramework'},
            $this->{$type.'BlackFramework'}
        );
    }

    /**
     * 准备枚举数据.
     */
    protected static function prepareEnum(array &$data): void
    {
        if (!static::$hasDefinedEnumFramework) {
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
        if (isset(static::$unCamelizePropFramework[$prop])) {
            return static::$unCamelizePropFramework[$prop];
        }

        return static::$unCamelizePropFramework[$prop] = UnCamelize::handle($prop);
    }

    /**
     * 返回转驼峰命名.
     */
    protected static function camelizeProp(string $prop): string
    {
        if (isset(static::$camelizePropFramework[$prop])) {
            return static::$camelizePropFramework[$prop];
        }

        return static::$camelizePropFramework[$prop] = Camelize::handle($prop);
    }

    /**
     * @throws \BadMethodCallException
     */
    protected function parseRelationScopeByName(string $scope): \Closure
    {
        $call = [$this, 'relationScope'.ucfirst($scope)];

        // 如果关联作用域为 private 会触发 __call 魔术方法中的异常
        if (!method_exists($this, $call[1])) {
            $e = sprintf(
                'Relation scope `%s` of entity `%s` is not exits.',
                $call[1],
                static::class,
            );

            throw new \BadMethodCallException($e);
        }

        // @phpstan-ignore-next-line
        return \Closure::fromCallable($call);
    }

    protected function prepareRelationScope(array $defined, null|array|string|\Closure $relationScope): ?\Closure
    {
        if (!isset($defined[self::RELATION_SCOPE]) && !$relationScope) {
            return null;
        }

        return function (Relation $relation) use ($defined, $relationScope): void {
            // 执行默认的关联作用域
            if (isset($defined[self::RELATION_SCOPE])) {
                $this->parseRelationScopeByName($defined[self::RELATION_SCOPE])($relation);
            }

            // 传入当前关联的关联作用域
            if (!$relationScope) {
                return;
            }

            if (\is_string($relationScope)) {
                $this->parseRelationScopeByName($relationScope)($relation);
            } elseif (\is_array($relationScope)) {
                foreach ($relationScope as $queryMethod => $args) {
                    $relation->{$queryMethod}(...(array) $args);
                }
            } elseif ($relationScope instanceof \Closure) {
                $relationScope($relation);
            }
        };
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
