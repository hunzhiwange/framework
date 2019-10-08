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

use Closure;
use Leevel\Collection\Collection;
use Leevel\Database\Ddd\Relation\BelongsTo;
use Leevel\Database\Ddd\Relation\HasMany;
use Leevel\Database\Ddd\Relation\HasOne;
use Leevel\Database\Ddd\Relation\ManyMany;
use Leevel\Database\Ddd\Relation\Relation;
use Leevel\Database\Select as DatabaseSelect;
use Leevel\Event\IDispatch;

/**
 * 实体基础接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.10.14
 *
 * @version 1.0
 */
interface IEntity
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
     * 创建新的实例.
     *
     * @param array $data
     * @param bool  $fromStorage
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public static function make(array $data = [], bool $fromStorage = false): self;

    /**
     * 数据库查询集合对象.
     *
     * - 查询静态方法入口，更好的 IDE 用户体验.
     * - 屏蔽 __callStatic 防止 IDE 无法识别.
     * - select 别名，致敬经典 QeePHP.
     *
     * @param int $softDeletedType
     *
     * @return \Leevel\Database\Ddd\Select
     */
    public static function find(int $softDeletedType = self::WITHOUT_SOFT_DELETED): Select;

    /**
     * 包含软删除数据的数据库查询集合对象.
     *
     * - 查询静态方法入口，更好的 IDE 用户体验.
     * - 屏蔽 __callStatic 防止 IDE 无法识别.
     * - 获取包含软删除的数据.
     *
     * @param int $softDeletedType
     *
     * @return \Leevel\Database\Ddd\Select
     */
    public static function withSoftDeleted(): Select;

    /**
     * 仅仅包含软删除数据的数据库查询集合对象.
     *
     * - 查询静态方法入口，更好的 IDE 用户体验.
     * - 屏蔽 __callStatic 防止 IDE 无法识别.
     * - 获取只包含软删除的数据.
     *
     * @param int $softDeletedType
     *
     * @return \Leevel\Database\Ddd\Select
     */
    public static function onlySoftDeleted(): Select;

    /**
     * 数据库查询集合对象.
     *
     * @param int $softDeletedType
     *
     * @return \Leevel\Database\Select
     */
    public static function selectCollection(int $softDeletedType = self::WITHOUT_SOFT_DELETED): DatabaseSelect;

    /**
     * 返回模型实体类的 meta 对象
     *
     * @return \Leevel\Database\Ddd\IMeta
     */
    public static function meta(): IMeta;

    /**
     * 数据库连接沙盒.
     *
     * @param mixed    $connect
     * @param \Closure $call
     *
     * @return mixed
     */
    public static function connectSandbox($connect, Closure $call);

    /**
     * 批量设置属性数据.
     *
     * @param array $data
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function withProps(array $data): self;

    /**
     * 设置属性数据.
     *
     * @param string $prop
     * @param mixed  $value
     * @param bool   $force
     * @param bool   $ignoreReadonly
     *
     * @throws \InvalidArgumentException
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function withProp(string $prop, $value, bool $force = true, bool $ignoreReadonly = false): self;

    /**
     * 获取属性数据.
     *
     * @param string $prop
     *
     * @return mixed
     */
    public function prop(string $prop);

    /**
     * 是否存在属性数据.
     *
     * @param string $prop
     *
     * @return bool
     */
    public function hasProp(string $prop): bool;

    /**
     * 自动判断快捷方式.
     *
     * @param array      $data
     * @param null|array $fill
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function save(array $data = [], array $fill = null): self;

    /**
     * 新增快捷方式.
     *
     * @param array      $data
     * @param null|array $fill
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function create(array $data = [], array $fill = null): self;

    /**
     * 更新快捷方式.
     *
     * @param array      $data
     * @param null|array $fill
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function update(array $data = [], array $fill = null): self;

    /**
     * replace 快捷方式.
     *
     * @param array      $data
     * @param null|array $fill
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function replace(array $data = [], array $fill = null): self;

    /**
     * 根据主键 ID 删除模型实体.
     *
     * @param array $ids
     * @param bool  $forceDelete
     *
     * @return int
     */
    public static function destroy(array $ids, bool $forceDelete = false): int;

    /**
     * 根据主键 ID 强制删除模型实体.
     *
     * @param array $ids
     * @param bool  $forceDelete
     *
     * @return int
     */
    public static function forceDestroy(array $ids): int;

    /**
     * 删除模型实体.
     *
     * @param bool $forceDelete
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function delete(bool $forceDelete = false): self;

    /**
     * 强制删除模型实体.
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function forceDelete(): self;

    /**
     * 根据主键 ID 删除模型实体.
     *
     * @param array $ids
     *
     * @return int
     */
    public static function softDestroy(array $ids): int;

    /**
     * 从模型实体中软删除数据.
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function softDelete(): self;

    /**
     * 恢复软删除的模型实体.
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function softRestore(): self;

    /**
     * 检查模型实体是否已经被软删除.
     *
     * @return bool
     */
    public function softDeleted(): bool;

    /**
     * 获取软删除字段.
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public static function deleteAtColumn(): string;

    /**
     * 数据持久化数据.
     *
     * @throws \RuntimeException
     *
     * @return mixed
     */
    public function flush();

    /**
     * 获取数据持久化数据.
     *
     * @return null|array
     */
    public function flushData(): ?array;

    /**
     * 确定对象是否对应数据库中的一条记录.
     *
     * @return bool
     */
    public function newed(): bool;

    /**
     * 获取主键
     * 唯一标识符.
     *
     * @return mixed
     */
    public function id();

    /**
     * 从数据库重新读取当前对象的属性.
     */
    public function refresh(): void;

    /**
     * 返回关联数据.
     *
     * @param string $prop
     *
     * @return mixed
     */
    public function loadRelationProp(string $prop);

    /**
     * 是否为关联属性.
     *
     * @param string $prop
     *
     * @return bool
     */
    public function isRelation(string $prop): bool;

    /**
     * 读取关联.
     *
     * @param string $prop
     *
     * @throws \BadMethodCallException
     *
     * @return \Leevel\Database\Ddd\Relation\Relation
     */
    public function loadRelation(string $prop): Relation;

    /**
     * 取得关联数据.
     *
     * @param string $prop
     *
     * @return mixed
     */
    public function relationProp(string $prop);

    /**
     * 设置关联数据.
     *
     * @param string $prop
     * @param mixed  $value
     */
    public function withRelationProp(string $prop, $value): void;

    /**
     * 预加载关联.
     *
     * @param array $relation
     *
     * @return \Leevel\Database\Ddd\Select
     */
    public static function eager(array $relation): Select;

    /**
     * 设置多对多中间实体.
     *
     * @param \Leevel\Database\Ddd\IEntity $middle
     */
    public function withMiddle(self $middle): void;

    /**
     * 获取多对多中间实体.
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function middle(): ?self;

    /**
     * 一对一关联.
     *
     * @param string        $relatedEntityClass
     * @param string        $targetKey
     * @param string        $sourceKey
     * @param null|\Closure $scope
     *
     * @return \Leevel\Database\Ddd\Relation\HasOne
     */
    public function hasOne(string $relatedEntityClass, string $targetKey, string $sourceKey, ?Closure $scope = null): HasOne;

    /**
     * 定义从属关系.
     *
     * @param string        $relatedEntityClass
     * @param string        $targetKey
     * @param string        $sourceKey
     * @param null|\Closure $scope
     *
     * @return \Leevel\Database\Ddd\Relation\BelongsTo
     */
    public function belongsTo(string $relatedEntityClass, string $targetKey, string $sourceKey, ?Closure $scope = null): BelongsTo;

    /**
     * 一对多关联.
     *
     * @param string        $relatedEntityClass
     * @param string        $targetKey
     * @param string        $sourceKey
     * @param null|\Closure $scope
     *
     * @return \Leevel\Database\Ddd\Relation\HasMany
     */
    public function hasMany(string $relatedEntityClass, string $targetKey, string $sourceKey, ?Closure $scope = null): HasMany;

    /**
     * 多对多关联.
     *
     * @param string        $relatedEntityClass
     * @param string        $middleEntityClass
     * @param string        $targetKey
     * @param string        $sourceKey
     * @param string        $middleTargetKey
     * @param string        $middleSourceKey
     * @param null|\Closure $scope
     *
     * @return \Leevel\Database\Ddd\Relation\ManyMany
     */
    public function manyMany(string $relatedEntityClass, string $middleEntityClass, string $targetKey, string $sourceKey, string $middleTargetKey, string $middleSourceKey, ?Closure $scope = null): ManyMany;

    /**
     * 返回模型实体事件处理器.
     *
     * @return \Leevel\Event\IDispatch
     */
    public static function eventDispatch(): ?IDispatch;

    /**
     * 设置模型实体事件处理器.
     *
     * @param null|\Leevel\Event\IDispatch $dispatch
     */
    public static function withEventDispatch(?IDispatch $dispatch = null): void;

    /**
     * 注册模型实体事件.
     *
     * @param string                                 $event
     * @param \Closure|\Leevel\Event\Observer|string $listener
     */
    public static function event(string $event, $listener): void;

    /**
     * 执行模型实体事件.
     *
     * @param string $event
     * @param array  ...$args
     */
    public function handleEvent(string $event, ...$args): void;

    /**
     * 返回受支持的事件.
     *
     * @return array
     */
    public static function supportEvent(): array;

    /**
     * 返回已经改变.
     *
     * @return array
     */
    public function changed(): array;

    /**
     * 检测是否已经改变.
     *
     * @param string $prop
     *
     * @return bool
     */
    public function hasChanged(string $prop): bool;

    /**
     * 将指定的属性设置已改变.
     *
     * @param array $props
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function addChanged(array $props): self;

    /**
     * 删除改变属性.
     *
     * @param array $props
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function deleteChanged(array $props): self;

    /**
     * 清空改变属性.
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function clearChanged(): self;

    /**
     * 返回主键字段.
     *
     * @return null|array|string
     */
    public static function primaryKey();

    /**
     * 验证主键是否存在并返回主键字段.
     *
     * @throws \InvalidArgumentException
     *
     * @return array|string
     */
    public static function validatePrimaryKey();

    /**
     * 返回主键字段.
     *
     * @return array
     */
    public static function primaryKeys(): array;

    /**
     * 返回自动增长字段.
     *
     * @return string
     */
    public static function autoIncrement(): ?string;

    /**
     * 返回字段名字.
     *
     * @return array
     */
    public static function fields(): array;

    /**
     * 是否存在字段.
     *
     * @param string $field
     *
     * @return bool
     */
    public static function hasField(string $field): bool;

    /**
     * 返回供查询的主键字段
     * 复合主键或者没有主键直接抛出异常.
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public static function singlePrimaryKey(): string;

    /**
     * 返回供查询的主键字段值
     * 复合主键或者没有主键直接抛出异常.
     *
     * @return mixed
     */
    public function singleId();

    /**
     * 返回设置表.
     *
     * @return string
     */
    public static function table(): string;

    /**
     * 获取 enum.
     * 不存在返回 false.
     *
     * @param string     $prop
     * @param null|mixed $enum
     *
     * @throws \InvalidArgumentException
     *
     * @return mixed
     */
    public static function enum(string $prop, $enum = null);

    /**
     * 创建一个模型实体集合.
     *
     * @param array $entity
     *
     * @return \Leevel\Collection\Collection
     */
    public function collection(array $entity = []): Collection;

    /**
     * 获取查询键值
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    public function idCondition(): array;

    /**
     * setter.
     *
     * @param string $prop
     * @param mixed  $value
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function setter(string $prop, $value): self;

    /**
     * getter.
     *
     * @param string $prop
     *
     * @return mixed
     */
    public function getter(string $prop);

    /**
     * set database connect.
     *
     * @param mixed $connect
     */
    public static function withConnect($connect): void;

    /**
     * get database connect.
     *
     * @return mixed
     */
    public static function connect();
}
