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

namespace Leevel\Database\Ddd\Relation;

use Closure;
use Exception;
use Leevel\Collection\Collection;
use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Select;

/**
 * 关联实体基类.
 *
 * @method static \Leevel\Database\Ddd\Entity entity()                                                                                                                     获取实体.
 * @method static \Leevel\Database\Ddd\Select eager(array $relation)                                                                                                       添加预载入关联查询.
 * @method static mixed preLoadResult($result)                                                                                                                             尝试解析结果预载.
 * @method static \Leevel\Database\Ddd\Entity findEntity(int $id, array $column = [])                                                                                      通过主键查找实体.
 * @method static \Leevel\Collection\Collection findMany(array $ids, array $column = [])                                                                                   通过主键查找多个实体.
 * @method static \Leevel\Database\Ddd\Entity findOrFail(int $id, array $column = [])                                                                                      通过主键查找实体，未找到则抛出异常.
 * @method static \Leevel\Database\Ddd\Select withSoftDeleted()                                                                                                            包含软删除数据的实体查询对象.
 * @method static \Leevel\Database\Ddd\Select onlySoftDeleted()                                                                                                            仅仅包含软删除数据的实体查询对象.
 * @method static void setCache(?\Leevel\Cache\Manager $cache)                                                                                                             设置缓存管理.
 * @method static ?\Leevel\Cache\Manager getCache()                                                                                                                        获取缓存管理.
 * @method static \Leevel\Database\Ddd\Select databaseSelect()                                                                                                             返回查询对象.
 * @method static mixed pdo($master = false)                                                                                                                               返回 PDO 查询连接.
 * @method static mixed query(string $sql, array $bindParams = [], $master = false, ?string $cacheName = null, ?int $cacheExpire = null, ?string $cacheConnect = null)     查询数据记录.
 * @method static array procedure(string $sql, array $bindParams = [], $master = false, ?string $cacheName = null, ?int $cacheExpire = null, ?string $cacheConnect = null) 查询存储过程数据记录.
 * @method static mixed execute(string $sql, array $bindParams = [])                                                                                                       执行 SQL 语句.
 * @method static \Generator cursor(string $sql, array $bindParams = [], $master = false)                                                                                  游标查询.
 * @method static \PDOStatement prepare(string $sql, array $bindParams = [], $master = false)                                                                              SQL 预处理.
 * @method static mixed transaction(\Closure $action)                                                                                                                      执行数据库事务.
 * @method static void beginTransaction()                                                                                                                                  启动事务.
 * @method static bool inTransaction()                                                                                                                                     检查是否处于事务中.
 * @method static void commit()                                                                                                                                            用于非自动提交状态下面的查询提交.
 * @method static void rollBack()                                                                                                                                          事务回滚.
 * @method static string lastInsertId(?string $name = null)                                                                                                                获取最后插入 ID 或者列.
 * @method static ?string getLastSql()                                                                                                                                     获取最近一次查询的 SQL 语句.
 * @method static int numRows()                                                                                                                                            返回影响记录.
 * @method static void close()                                                                                                                                             关闭数据库.
 * @method static void freePDOStatement()                                                                                                                                  释放 PDO 预处理查询.
 * @method static void closeConnects()                                                                                                                                     关闭数据库连接.
 * @method static string getRawSql(string $sql, array $bindParams)                                                                                                         从 PDO 预处理语句中获取原始 SQL 查询字符串.
 * @method static string parseDsn(array $option)                                                                                                                           DSN 解析.
 * @method static array getTableNames(string $dbName, $master = false)                                                                                                     取得数据库表名列表.
 * @method static array getTableColumns(string $tableName, $master = false)                                                                                                取得数据库表字段信息.
 * @method static string identifierColumn($name)                                                                                                                           SQL 字段格式化.
 * @method static string limitCount(?int $limitCount = null, ?int $limitOffset = null)                                                                                     分析查询条数.
 * @method static \Leevel\Database\Condition databaseCondition()                                                                                                           查询对象.
 * @method static \Leevel\Database\IDatabase databaseConnect()                                                                                                             返回数据库连接对象.
 * @method static \Leevel\Database\Ddd\Select sql(bool $flag = true)                                                                                                       指定返回 SQL 不做任何操作.
 * @method static \Leevel\Database\Ddd\Select master($master = false)                                                                                                      设置是否查询主服务器.
 * @method static \Leevel\Database\Ddd\Select asSome(?\Closure $asSome = null, array $args = [])                                                                           设置以某种包装返会结果.
 * @method static \Leevel\Database\Ddd\Select asArray(?\Closure $asArray = null)                                                                                           设置返会结果为数组.
 * @method static \Leevel\Database\Ddd\Select asCollection(bool $asCollection = true)                                                                                      设置是否以集合返回.
 * @method static mixed select(null|callable|\Leevel\Database\Select|string $data = null, array $bind = [], bool $flag = false)                                                                                         原生 SQL 查询数据.
 * @method static mixed insert($data, array $bind = [], bool $replace = false, bool $flag = false)                                                                         插入数据 insert (支持原生 SQL).
 * @method static mixed insertAll(array $data, array $bind = [], bool $replace = false, bool $flag = false)                                                                批量插入数据 insertAll.
 * @method static mixed update($data, array $bind = [], bool $flag = false)                                                                                                更新数据 update (支持原生 SQL).
 * @method static mixed updateColumn(string $column, $value, array $bind = [], bool $flag = false)                                                                         更新某个字段的值
 * @method static mixed updateIncrease(string $column, int $step = 1, array $bind = [], bool $flag = false)                                                                字段递增.
 * @method static mixed updateDecrease(string $column, int $step = 1, array $bind = [], bool $flag = false)                                                                字段减少.
 * @method static mixed delete(?string $data = null, array $bind = [], bool $flag = false)                                                                                 删除数据 delete (支持原生 SQL).
 * @method static mixed truncate(bool $flag = false)                                                                                                                       清空表重置自增 ID.
 * @method static mixed findOne(bool $flag = false)                                                                                                                        返回一条记录.
 * @method static mixed findAll(bool $flag = false)                                                                                                                        返回所有记录.
 * @method static mixed find(?int $num = null, bool $flag = false)                                                                                                         返回最后几条记录.
 * @method static mixed value(string $field, bool $flag = false)                                                                                                           返回一个字段的值
 * @method static array list($fieldValue, ?string $fieldKey = null, bool $flag = false)                                                                                    返回一列数据.
 * @method static void chunk(int $count, \Closure $chunk)                                                                                                                  数据分块处理.
 * @method static void each(int $count, \Closure $each)                                                                                                                    数据分块处理依次回调.
 * @method static mixed findCount(string $field = '*', string $alias = 'row_count', bool $flag = false)                                                                    总记录数.
 * @method static mixed findAvg(string $field, string $alias = 'avg_value', bool $flag = false)                                                                            平均数.
 * @method static mixed findMax(string $field, string $alias = 'max_value', bool $flag = false)                                                                            最大值.
 * @method static mixed findMin(string $field, string $alias = 'min_value', bool $flag = false)                                                                            最小值.
 * @method static mixed findSum(string $field, string $alias = 'sum_value', bool $flag = false)                                                                            合计.
 * @method static \Leevel\Database\Page page(int $currentPage, int $perPage = 10, bool $flag = false, string $column = '*', array $option = [])                            分页查询.
 * @method static \Leevel\Database\Page pageMacro(int $currentPage, int $perPage = 10, bool $flag = false, array $option = [])                                             创建一个无限数据的分页查询.
 * @method static \Leevel\Database\Page pagePrevNext(int $currentPage, int $perPage = 10, bool $flag = false, array $option = [])                                          创建一个只有上下页的分页查询.
 * @method static int pageCount(string $cols = '*')                                                                                                                        取得分页查询记录数量.
 * @method static string makeSql(bool $withLogicGroup = false)                                                                                                             获得查询字符串.
 * @method static \Leevel\Database\Ddd\Select cache(string $name, ?int $expire = null, ?string $connect = null)                                                            设置查询缓存.
 * @method static \Leevel\Database\Ddd\Select forPage(int $page, int $perPage = 10)                                                                                        根据分页设置条件.
 * @method static \Leevel\Database\Ddd\Select time(string $type = 'date')                                                                                                  时间控制语句开始.
 * @method static \Leevel\Database\Ddd\Select endTime()                                                                                                                    时间控制语句结束.
 * @method static \Leevel\Database\Ddd\Select reset(?string $option = null)                                                                                                重置查询条件.
 * @method static \Leevel\Database\Ddd\Select comment(string $comment)                                                                                                     查询注释.
 * @method static \Leevel\Database\Ddd\Select prefix(string $prefix)                                                                                                       prefix 查询.
 * @method static \Leevel\Database\Ddd\Select table($table, $cols = '*')                                                                                                   添加一个要查询的表及其要查询的字段.
 * @method static string getAlias()                                                                                                                                        获取表别名.
 * @method static \Leevel\Database\Ddd\Select columns($cols = '*', ?string $table = null)                                                                                  添加字段.
 * @method static \Leevel\Database\Ddd\Select setColumns($cols = '*', ?string $table = null)                                                                               设置字段.
 * @method static string raw(string $raw)                                                                                                                                  原生查询.
 * @method static \Leevel\Database\Ddd\Select where(...$cond)                                                                                                              where 查询条件.
 * @method static \Leevel\Database\Ddd\Select orWhere(...$cond)                                                                                                            orWhere 查询条件.
 * @method static \Leevel\Database\Ddd\Select whereRaw(string $raw)                                                                                                        Where 原生查询.
 * @method static \Leevel\Database\Ddd\Select orWhereRaw(string $raw)                                                                                                      Where 原生 OR 查询.
 * @method static \Leevel\Database\Ddd\Select whereExists($exists)                                                                                                         exists 方法支持
 * @method static \Leevel\Database\Ddd\Select whereNotExists($exists)                                                                                                      not exists 方法支持
 * @method static \Leevel\Database\Ddd\Select whereBetween(...$cond)                                                                                                       whereBetween 查询条件.
 * @method static \Leevel\Database\Ddd\Select whereNotBetween(...$cond)                                                                                                    whereNotBetween 查询条件.
 * @method static \Leevel\Database\Ddd\Select whereNull(...$cond)                                                                                                          whereNull 查询条件.
 * @method static \Leevel\Database\Ddd\Select whereNotNull(...$cond)                                                                                                       whereNotNull 查询条件.
 * @method static \Leevel\Database\Ddd\Select whereIn(...$cond)                                                                                                            whereIn 查询条件.
 * @method static \Leevel\Database\Ddd\Select whereNotIn(...$cond)                                                                                                         whereNotIn 查询条件.
 * @method static \Leevel\Database\Ddd\Select whereLike(...$cond)                                                                                                          whereLike 查询条件.
 * @method static \Leevel\Database\Ddd\Select whereNotLike(...$cond)                                                                                                       whereNotLike 查询条件.
 * @method static \Leevel\Database\Ddd\Select whereDate(...$cond)                                                                                                          whereDate 查询条件.
 * @method static \Leevel\Database\Ddd\Select whereDay(...$cond)                                                                                                           whereDay 查询条件.
 * @method static \Leevel\Database\Ddd\Select whereMonth(...$cond)                                                                                                         whereMonth 查询条件.
 * @method static \Leevel\Database\Ddd\Select whereYear(...$cond)                                                                                                          whereYear 查询条件.
 * @method static \Leevel\Database\Ddd\Select bind($names, $value = null, ?int $dataType = null)                                                                           参数绑定支持.
 * @method static \Leevel\Database\Ddd\Select forceIndex($indexs, $type = 'FORCE')                                                                                         index 强制索引（或者忽略索引）.
 * @method static \Leevel\Database\Ddd\Select ignoreIndex($indexs)                                                                                                         index 忽略索引.
 * @method static \Leevel\Database\Ddd\Select join($table, $cols, ...$cond)                                                                                                join 查询.
 * @method static \Leevel\Database\Ddd\Select innerJoin($table, $cols, ...$cond)                                                                                           innerJoin 查询.
 * @method static \Leevel\Database\Ddd\Select leftJoin($table, $cols, ...$cond)                                                                                            leftJoin 查询.
 * @method static \Leevel\Database\Ddd\Select rightJoin($table, $cols, ...$cond)                                                                                           rightJoin 查询.
 * @method static \Leevel\Database\Ddd\Select fullJoin($table, $cols, ...$cond)                                                                                            fullJoin 查询.
 * @method static \Leevel\Database\Ddd\Select crossJoin($table, $cols, ...$cond)                                                                                           crossJoin 查询.
 * @method static \Leevel\Database\Ddd\Select naturalJoin($table, $cols, ...$cond)                                                                                         naturalJoin 查询.
 * @method static \Leevel\Database\Ddd\Select union($selects, string $type = 'UNION')                                                                                      添加一个 UNION 查询.
 * @method static \Leevel\Database\Ddd\Select unionAll($selects)                                                                                                           添加一个 UNION ALL 查询.
 * @method static \Leevel\Database\Ddd\Select groupBy($expression)                                                                                                         指定 GROUP BY 子句.
 * @method static \Leevel\Database\Ddd\Select having(...$cond)                                                                                                             添加一个 HAVING 条件.
 * @method static \Leevel\Database\Ddd\Select orHaving(...$cond)                                                                                                           orHaving 查询条件.
 * @method static \Leevel\Database\Ddd\Select havingRaw(string $raw)                                                                                                       having 原生查询.
 * @method static \Leevel\Database\Ddd\Select orHavingRaw(string $raw)                                                                                                     having 原生 OR 查询.
 * @method static \Leevel\Database\Ddd\Select havingBetween(...$cond)                                                                                                      havingBetween 查询条件.
 * @method static \Leevel\Database\Ddd\Select havingNotBetween(...$cond)                                                                                                   havingNotBetween 查询条件.
 * @method static \Leevel\Database\Ddd\Select havingNull(...$cond)                                                                                                         havingNull 查询条件.
 * @method static \Leevel\Database\Ddd\Select havingNotNull(...$cond)                                                                                                      havingNotNull 查询条件.
 * @method static \Leevel\Database\Ddd\Select havingIn(...$cond)                                                                                                           havingIn 查询条件.
 * @method static \Leevel\Database\Ddd\Select havingNotIn(...$cond)                                                                                                        havingNotIn 查询条件.
 * @method static \Leevel\Database\Ddd\Select havingLike(...$cond)                                                                                                         havingLike 查询条件.
 * @method static \Leevel\Database\Ddd\Select havingNotLike(...$cond)                                                                                                      havingNotLike 查询条件.
 * @method static \Leevel\Database\Ddd\Select havingDate(...$cond)                                                                                                         havingDate 查询条件.
 * @method static \Leevel\Database\Ddd\Select havingDay(...$cond)                                                                                                          havingDay 查询条件.
 * @method static \Leevel\Database\Ddd\Select havingMonth(...$cond)                                                                                                        havingMonth 查询条件.
 * @method static \Leevel\Database\Ddd\Select havingYear(...$cond)                                                                                                         havingYear 查询条件.
 * @method static \Leevel\Database\Ddd\Select orderBy($expression, string $orderDefault = 'ASC')                                                                           添加排序.
 * @method static \Leevel\Database\Ddd\Select latest(string $field = 'create_at')                                                                                          最近排序数据.
 * @method static \Leevel\Database\Ddd\Select oldest(string $field = 'create_at')                                                                                          最早排序数据.
 * @method static \Leevel\Database\Ddd\Select distinct(bool $flag = true)                                                                                                  创建一个 SELECT DISTINCT 查询.
 * @method static \Leevel\Database\Ddd\Select count(string $field = '*', string $alias = 'row_count')                                                                      总记录数.
 * @method static \Leevel\Database\Ddd\Select avg(string $field, string $alias = 'avg_value')                                                                              平均数.
 * @method static \Leevel\Database\Ddd\Select max(string $field, string $alias = 'max_value')                                                                              最大值.
 * @method static \Leevel\Database\Ddd\Select min(string $field, string $alias = 'min_value')                                                                              最小值.
 * @method static \Leevel\Database\Ddd\Select sum(string $field, string $alias = 'sum_value')                                                                              合计
 * @method static \Leevel\Database\Ddd\Select one()                                                                                                                        指示仅查询第一个符合条件的记录.
 * @method static \Leevel\Database\Ddd\Select all()                                                                                                                        指示查询所有符合条件的记录.
 * @method static \Leevel\Database\Ddd\Select top(int $count = 30)                                                                                                         查询几条记录.
 * @method static \Leevel\Database\Ddd\Select limit(int $offset = 0, int $count = 0)                                                                                       limit 限制条数.
 * @method static \Leevel\Database\Ddd\Select forUpdate(bool $flag = true)                                                                                                 排它锁 FOR UPDATE 查询.
 * @method static \Leevel\Database\Ddd\Select lockShare(bool $flag = true)                                                                                                 共享锁 LOCK SHARE 查询.
 * @method static array getBindParams()                                                                                                                                    返回参数绑定.
 * @method static void resetBindParams(array $bindParams = [])                                                                                                             重置参数绑定.
 * @method static void setBindParamsPrefix(string $bindParamsPrefix)                                                                                                       设置参数绑定前缀.
 */
abstract class Relation
{
    /**
     * 查询对象
     */
    protected Select $select;

    /**
     * 关联目标实体.
     */
    protected Entity $targetEntity;

    /**
     * 源实体.
     */
    protected Entity $sourceEntity;

    /**
     * 目标关联字段.
    */
    protected string $targetKey;

    /**
     * 源关联字段.
    */
    protected string $sourceKey;

    /**
     * 是否初始化关联查询条件.
    */
    protected static bool $relationCondition = true;

    /**
     * 源数据为空.
    */
    protected bool $emptySourceData = false;

    /**
     * 构造函数.
     */
    public function __construct(Entity $targetEntity, Entity $sourceEntity, string $targetKey, string $sourceKey, ?Closure $scope = null)
    {
        $this->targetEntity = $targetEntity;
        $this->sourceEntity = $sourceEntity;
        $this->targetKey = $targetKey;
        $this->sourceKey = $sourceKey;
        $this->getSelectFromEntity();
        $this->scope($scope);
        $this->addRelationCondition();
    }

    /**
     * call.
     */
    public function __call(string $method, array $args): mixed
    {
        $select = $this->select->{$method}(...$args);
        if ($this->getSelect() === $select) {
            return $this;
        }

        return $select;
    }

    /**
     * 返回查询.
     */
    public function getSelect(): Select
    {
        return $this->select;
    }

    /**
     * 取得预载入关联实体.
     */
    public function getPreLoad(): mixed
    {
        return $this->getSelect()->preLoadResult($this->findAll());
    }

    /**
     * 取得关联目标实体.
     */
    public function getTargetEntity(): Entity
    {
        return $this->targetEntity;
    }

    /**
     * 取得源实体.
     */
    public function getSourceEntity(): Entity
    {
        return $this->sourceEntity;
    }

    /**
     * 取得目标字段.
     */
    public function getTargetKey(): string
    {
        return $this->targetKey;
    }

    /**
     * 取得源字段.
     */
    public function getSourceKey(): string
    {
        return $this->sourceKey;
    }

    /**
     * 获取不带关联条件的关联对象.
     */
    public static function withoutRelationCondition(Closure $call): self
    {
        $old = static::$relationCondition;
        static::$relationCondition = false;

        try {
            $relation = $call();
            static::$relationCondition = $old;
        } catch (Exception $e) {
            static::$relationCondition = $old;

            throw $e;
        }

        return $relation;
    }

    /**
     * 取回源实体对应数据.
     */
    public function getSourceValue(): mixed
    {
        return $this->sourceEntity->prop($this->sourceKey);
    }

    /**
     * 关联基础查询条件.
     */
    abstract public function addRelationCondition(): void;

    /**
     * 设置预载入关联查询条件.
     */
    abstract public function preLoadCondition(array $entitys): void;

    /**
     * 匹配关联查询数据到实体 HasMany.
     */
    abstract public function matchPreLoad(array $entitys, collection $result, string $relation): array;

    /**
     * 查询关联对象
     */
    abstract public function sourceQuery(): mixed;

    /**
     * 准备关联查询条件.
     */
    protected function prepareRelationCondition(Closure $call): void
    {
        if (!static::$relationCondition) {
            return;
        }

        if (!$sourceValue = $this->getSourceValue()) {
            $this->emptySourceData = true;
        } else {
            $this->emptySourceData = false;
            $call($sourceValue);
        }
    }

    /**
     * 返回实体的主键.
     */
    protected function getEntityKey(array $entitys, ?string $key = null): array
    {
        $entitys = array_map(function ($entity) use ($key) {
            return $key ? $entity->prop($key) : $entity->singleId();
        }, $entitys);

        return array_unique(array_values($entitys));
    }

    /**
     * 从实体返回查询.
     */
    protected function getSelectFromEntity(): void
    {
        $this->select = $this->targetEntity->select();
    }

    /**
     * 关联查询作用域.
     */
    protected function scope(?Closure $scope = null): void
    {
        if (!$scope) {
            return;
        }

        $scope($this);
    }
}
