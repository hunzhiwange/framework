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
     * 批量查找前事件.
     *
     * @var string
     */
    const BEFORE_SELECT_EVENT = 'selecting';

    /**
     * 批量查找后事件.
     *
     * @var string
     */
    const AFTER_SELECT_EVENT = 'selected';

    /**
     * 查找前事件.
     *
     * @var string
     */
    const BEFORE_FIND_EVENT = 'finding';

    /**
     * 查找后事件.
     *
     * @var string
     */
    const AFTER_FIND_EVENT = 'finded';

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
    const SCOPE = 'scope';

    /**
     * 创建新的实例.
     *
     * @param array $data
     * @param bool  $fromStorage
     *
     * @return static
     */
    public static function make(array $data, bool $fromStorage);

    /**
     * 自动判断快捷方式.
     *
     * @param array      $data
     * @param null|array $fill
     *
     * @return $this
     */
    public function save(array $data = [], array $fill = null): self;

    /**
     * 新增快捷方式.
     *
     * @param array      $data
     * @param null|array $fill
     *
     * @return $this
     */
    public function create(array $data = [], array $fill = null): self;

    /**
     * 更新快捷方式.
     *
     * @param array      $data
     * @param null|array $fill
     *
     * @return $this
     */
    public function update(array $data = [], array $fill = null): self;

    /**
     * replace 快捷方式.
     *
     * @param array      $data
     * @param null|array $fill
     *
     * @return $this
     */
    public function replace(array $data = [], array $fill = null): self;

    /**
     * 根据主键 ID 删除模型实体.
     *
     * @param array $ids
     *
     * @return int
     */
    public static function destroys(array $ids): int;

    /**
     * 销毁模型实体.
     *
     * @return $this
     */
    public function destroy(): self;

    /**
     * 数据持久化数据.
     *
     * @return mixed
     */
    public function flush();

    /**
     * 获取是否已经持久化数据.
     *
     * @return bool
     */
    public function flushed(): bool;

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
    public function refresh();

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
    public function withRelationProp(string $prop, $value);

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
     * @param string $relatedEntityClass
     * @param string $targetKey
     * @param string $sourceKey
     *
     * @return \Leevel\Database\Ddd\Relation\HasOne
     */
    public function hasOne(string $relatedEntityClass, string $targetKey, string $sourceKey): HasOne;

    /**
     * 定义从属关系.
     *
     * @param string $relatedEntityClass
     * @param string $targetKey
     * @param string $sourceKey
     *
     * @return \Leevel\Database\Ddd\Relation\BelongsTo
     */
    public function belongsTo($relatedEntityClass, string $targetKey, string $sourceKey): BelongsTo;

    /**
     * 一对多关联.
     *
     * @param string $relatedEntityClass
     * @param string $targetKey
     * @param string $sourceKey
     *
     * @return \Leevel\Database\Ddd\Relation\HasMany
     */
    public function hasMany(string $relatedEntityClass, string $targetKey, string $sourceKey): HasMany;

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
    public function manyMany(string $relatedEntityClass, string $middleEntityClass, string $targetKey, string $sourceKey, string $middleTargetKey, string $middleSourceKey): ManyMany;

    /**
     * 返回模型实体事件处理器.
     *
     * @return \Leevel\Event\IDispatch
     */
    public static function eventDispatch(): IDispatch;

    /**
     * 设置模型实体事件处理器.
     *
     * @param null|\Leevel\Event\IDispatch $dispatch
     */
    public static function withEventDispatch(IDispatch $dispatch = null);

    /**
     * 注册模型实体事件.
     *
     * @param string                        $event
     * @param \leevel\event\observer|string $listener
     */
    public static function registerEvent(string $event, $listener);

    /**
     * 执行模型实体事件.
     *
     * @param string $event
     */
    public function runEvent(string $event);

    /**
     * 验证事件是否受支持
     *
     * @param string $event
     *
     * @return bool
     */
    public static function isSupportEvent(string $event);

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
     * @return $this
     */
    public function addChanged(array $props): self;

    /**
     * 删除改变属性.
     *
     * @param array $props
     *
     * @return $this
     */
    public function deleteChanged(array $props): self;

    /**
     * 清空改变属性.
     *
     * @return $this
     */
    public function clearChanged(): self;

    /**
     * 返回主键字段.
     *
     * @return null|array|string
     */
    public function primaryKey();

    /**
     * 返回主键字段.
     *
     * @return array
     */
    public function primaryKeys(): array;

    /**
     * 返回自动增长字段.
     *
     * @return string
     */
    public function autoIncrement(): ?string;

    /**
     * 返回字段名字.
     *
     * @return array
     */
    public function fields(): array;

    /**
     * 是否存在字段.
     *
     * @param string $field
     *
     * @return bool
     */
    public function hasField(string $field): bool;

    /**
     * 返回供查询的主键字段
     * 复合主键或者没有主键直接抛出异常.
     *
     * @return string
     */
    public function singlePrimaryKey(): string;

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
    public function table(): string;

    /**
     * 设置连接.
     *
     * @param mixed $connect
     *
     * @return $this
     */
    public function withConnect($connect): self;

    /**
     * 创建一个模型实体集合.
     *
     * @param array $entity
     *
     * @return \Leevel\Collection\Collection
     */
    public function collection(array $entity = []);

    /**
     * 获取查询键值
     *
     * @return array
     */
    public function idCondition(): array;

    /**
     * 返回数据库查询集合对象
     *
     * @return \Leevel\Database\Select
     */
    public function selectReal(): DatabaseSelect;

    /**
     * 返回数据库查询集合对象
     *
     * @return \Leevel\Database\Ddd\Select
     */
    public function select(): Select;

    /**
     * 返回模型实体类的 meta 对象
     *
     * @return \Leevel\Database\Ddd\IMeta
     */
    public function metaConnect(): IMeta;

    /**
     * 返回模型实体类的 meta 对象
     *
     * @param mixed $connect
     *
     * @return \Leevel\Database\Ddd\IMeta
     */
    public static function meta($connect = null): IMeta;
}
