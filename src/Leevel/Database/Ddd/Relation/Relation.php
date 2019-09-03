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

namespace Leevel\Database\Ddd\Relation;

use Closure;
use Leevel\Collection\Collection;
use Leevel\Database\Ddd\IEntity;
use Leevel\Database\Ddd\Select;

/**
 * 关联模型实体基类.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.09.28
 *
 * @version 1.0
 *
 * @method static \Leevel\Database\Ddd\IEntity entity()                                                                                        获取模型实体.
 * @method static \Leevel\Database\Ddd\Select eager(array $relation)                                                                           添加预载入的关联.
 * @method static preLoadResult($result)                                                                                                       尝试解析结果预载.
 * @method static \Leevel\Database\Ddd\IEntity findEntity(int $id, array $column = [])                                                         通过主键查找模型实体.
 * @method static \Leevel\Collection\Collection findMany(array $ids, array $column = [])                                                       根据主键查找模型实体.
 * @method static \Leevel\Database\Ddd\IEntity findOrFail(int $id, array $column = [])                                                         通过主键查找模型实体，未找到则抛出异常.
 * @method static int softDelete(bool $flush = true)                                                                                           从模型实体中软删除数据.
 * @method static int softDestroy(array $ids, bool $flush = true)                                                                              根据主键 ID 删除模型实体.
 * @method static int softRestore(bool $flush = true)                                                                                          恢复软删除的模型实体.
 * @method static \Leevel\Database\Select withoutSoftDeleted()                                                                                 获取不包含软删除的数据.
 * @method static \Leevel\Database\Select onlySoftDeleted()                                                                                    获取只包含软删除的数据.
 * @method static bool softDeleted()                                                                                                           检查模型实体是否已经被软删除了.
 * @method static string deleteAtColumn()                                                                                                      获取软删除字段.
 * @method static pdo($master = false)                                                                                                         返回 Pdo 查询连接.
 * @method static query(string $sql, array $bindParams = [], $master = false, int $fetchType = 5, $fetchArgument = null, array $ctorArgs = []) 查询数据记录.
 * @method static execute(string $sql, array $bindParams = [])                                                                                 执行 sql 语句.
 * @method static transaction(\Closure $action)                                                                                                执行数据库事务
 * @method static void beginTransaction()                                                                                                      启动事务.
 * @method static bool inTransaction()                                                                                                         检查是否处于事务中.
 * @method static void commit()                                                                                                                用于非自动提交状态下面的查询提交.
 * @method static void rollBack()                                                                                                              事务回滚.
 * @method static string lastInsertId(?string $name = null)                                                                                    获取最后插入 ID 或者列.
 * @method static array lastSql()                                                                                                              获取最近一次查询的 sql 语句.
 * @method static int numRows()                                                                                                                返回影响记录.
 * @method static void close()                                                                                                                 关闭数据库.
 * @method static void freePDOStatement()                                                                                                      释放 PDO 预处理查询.
 * @method static void closeConnects()                                                                                                         关闭数据库连接.
 * @method static string normalizeExpression(string $sql, string $tableName)                                                                   sql 表达式格式化.
 * @method static string normalizeTableOrColumn(string $name, ?string $alias = null, ?string $as = null)                                       表或者字段格式化（支持别名）.
 * @method static string normalizeColumn(string $key, string $tableName)                                                                       字段格式化.
 * @method static normalizeColumnValue($value, bool $quotationMark = true)                                                                     字段值格式化.
 * @method static string normalizeSqlType(string $sql)                                                                                         分析 sql 类型数据.
 * @method static int normalizeBindParamType($value)                                                                                           分析绑定参数类型数据.
 * @method static string parseDsn(array $option)                                                                                               dsn 解析.
 * @method static array tableNames(string $dbName, $master = false)                                                                            取得数据库表名列表.
 * @method static array tableColumns(string $tableName, $master = false)                                                                       取得数据库表字段信息.
 * @method static string identifierColumn($name)                                                                                               sql 字段格式化.
 * @method static string limitCount(?int $limitCount = null, ?int $limitOffset = null)                                                         分析 limit.
 * @method static \Leevel\Database\Condition databaseCondition()                                                                               查询对象.
 * @method static \Leevel\Database\IDatabase databaseConnect()                                                                                 返回数据库连接对象.
 * @method static \Leevel\Database\Ddd\Select selfDatabaseSelect()                                                                             占位符返回本对象.
 * @method static \Leevel\Database\Ddd\Select sql(bool $flag = true)                                                                           指定返回 SQL 不做任何操作.
 * @method static \Leevel\Database\Ddd\Select master(bool $master = false)                                                                     设置是否查询主服务器.
 * @method static \Leevel\Database\Ddd\Select fetchArgs(int $fetchStyle, $fetchArgument = null, array $ctorArgs = [])                          设置查询参数.
 * @method static \Leevel\Database\Ddd\Select asClass(string $className, array $args = [])                                                     设置以类返会结果.
 * @method static \Leevel\Database\Ddd\Select asDefault()                                                                                      设置默认形式返回.
 * @method static \Leevel\Database\Ddd\Select asCollection(bool $acollection = true)                                                           设置是否以集合返回.
 * @method static select($data = null, array $bind = [], bool $flag = false)                                                                   原生 sql 查询数据 select.
 * @method static insert($data, array $bind = [], bool $replace = false, bool $flag = false)                                                   插入数据 insert (支持原生 sql).
 * @method static insertAll(array $data, array $bind = [], bool $replace = false, bool $flag = false)                                          批量插入数据 insertAll.
 * @method static update($data, array $bind = [], bool $flag = false)                                                                          更新数据 update (支持原生 sql).
 * @method static updateColumn(string $column, $value, array $bind = [], bool $flag = false)                                                   更新某个字段的值
 * @method static updateIncrease(string $column, int $step = 1, array $bind = [], bool $flag = false)                                          字段递增.
 * @method static updateDecrease(string $column, int $step = 1, array $bind = [], bool $flag = false)                                          字段减少.
 * @method static delete(?string $data = null, array $bind = [], bool $flag = false)                                                           删除数据 delete (支持原生 sql).
 * @method static truncate(bool $flag = false)                                                                                                 清空表重置自增 ID.
 * @method static findOne(bool $flag = false)                                                                                                  返回一条记录.
 * @method static findAll(bool $flag = false)                                                                                                  返回所有记录.
 * @method static find(?int $num = null, bool $flag = false)                                                                                   返回最后几条记录.
 * @method static value(string $field, bool $flag = false)                                                                                     返回一个字段的值
 * @method static pull(string $field, bool $flag = false)                                                                                      返回一个字段的值(别名).
 * @method static array list($fieldValue, ?string $fieldKey = null, bool $flag = false)                                                        返回一列数据.
 * @method static void chunk(int $count, \Closure $chunk)                                                                                      数据分块处理.
 * @method static void each(int $count, \Closure $each)                                                                                        数据分块处理依次回调.
 * @method static findCount(string $field = '*', string $alias = 'row_count', bool $flag = false)                                              总记录数.
 * @method static findAvg(string $field, string $alias = 'avg_value', bool $flag = false)                                                      平均数.
 * @method static findMax(string $field, string $alias = 'max_value', bool $flag = false)                                                      最大值.
 * @method static findMin(string $field, string $alias = 'min_value', bool $flag = false)                                                      最小值.
 * @method static findSum(string $field, string $alias = 'sum_value', bool $flag = false)                                                      合计.
 * @method static array page(int $currentPage, int $perPage = 10, bool $flag = false, bool $withTotal = true, string $column = '*')            分页查询.
 * @method static array pageHtml(int $currentPage, int $perPage = 10, bool $flag = false, string $column = '*', array $option = [])            分页查询. 可以渲染 HTML.
 * @method static array pageMacro(int $currentPage, int $perPage = 10, bool $flag = false, array $option = [])                                 创建一个无限数据的分页查询.
 * @method static array pagePrevNext(int $currentPage, int $perPage = 10, bool $flag = false, array $option = [])                              创建一个只有上下页的分页查询.
 * @method static int pageCount(string $cols = '*')                                                                                            取得分页查询记录数量.
 * @method static string makeSql(bool $withLogicGroup = false)                                                                                 获得查询字符串.
 * @method static \Leevel\Database\Ddd\Select forPage(int $page, int $perPage = 15)                                                            根据分页设置条件.
 * @method static \Leevel\Database\Ddd\Select time(string $type = 'date')                                                                      时间控制语句开始.
 * @method static \Leevel\Database\Ddd\Select endTime()                                                                                        时间控制语句结束.
 * @method static \Leevel\Database\Ddd\Select reset(?string $option = null)                                                                    重置查询条件.
 * @method static \Leevel\Database\Ddd\Select prefix(string $prefix)                                                                           prefix 查询.
 * @method static \Leevel\Database\Ddd\Select table($table, $cols = '*')                                                                       添加一个要查询的表及其要查询的字段.
 * @method static string getAlias()                                                                                                            获取表别名.
 * @method static \Leevel\Database\Ddd\Select columns($cols = '*', ?string $table = null)                                                      添加字段.
 * @method static \Leevel\Database\Ddd\Select setColumns($cols = '*', ?string $table = null)                                                   设置字段.
 * @method static \Leevel\Database\Ddd\Select where(...$cond)                                                                                  where 查询条件.
 * @method static \Leevel\Database\Ddd\Select orWhere(...$cond)                                                                                orWhere 查询条件.
 * @method static \Leevel\Database\Ddd\Select whereRaw(string $raw)                                                                            Where 原生查询.
 * @method static \Leevel\Database\Ddd\Select orWhereRaw(string $raw)                                                                          Where 原生 OR 查询.
 * @method static \Leevel\Database\Ddd\Select whereExists($exists)                                                                             exists 方法支持
 * @method static \Leevel\Database\Ddd\Select whereNotExists($exists)                                                                          not exists 方法支持
 * @method static \Leevel\Database\Ddd\Select whereBetween(...$cond)                                                                           whereBetween 查询条件.
 * @method static \Leevel\Database\Ddd\Select whereNotBetween(...$cond)                                                                        whereNotBetween 查询条件.
 * @method static \Leevel\Database\Ddd\Select whereNull(...$cond)                                                                              whereNull 查询条件.
 * @method static \Leevel\Database\Ddd\Select whereNotNull(...$cond)                                                                           whereNotNull 查询条件.
 * @method static \Leevel\Database\Ddd\Select whereIn(...$cond)                                                                                whereIn 查询条件.
 * @method static \Leevel\Database\Ddd\Select whereNotIn(...$cond)                                                                             whereNotIn 查询条件.
 * @method static \Leevel\Database\Ddd\Select whereLike(...$cond)                                                                              whereLike 查询条件.
 * @method static \Leevel\Database\Ddd\Select whereNotLike(...$cond)                                                                           whereNotLike 查询条件.
 * @method static \Leevel\Database\Ddd\Select whereDate(...$cond)                                                                              whereDate 查询条件.
 * @method static \Leevel\Database\Ddd\Select whereDay(...$cond)                                                                               whereDay 查询条件.
 * @method static \Leevel\Database\Ddd\Select whereMonth(...$cond)                                                                             whereMonth 查询条件.
 * @method static \Leevel\Database\Ddd\Select whereYear(...$cond)                                                                              whereYear 查询条件.
 * @method static \Leevel\Database\Ddd\Select bind($names, $value = null, int $type = 2)                                                       参数绑定支持
 * @method static \Leevel\Database\Ddd\Select forceIndex($indexs, $type = 'FORCE')                                                             index 强制索引（或者忽略索引）.
 * @method static \Leevel\Database\Ddd\Select ignoreIndex($indexs)                                                                             index 忽略索引.
 * @method static \Leevel\Database\Ddd\Select join($table, $cols, ...$cond)                                                                    join 查询.
 * @method static \Leevel\Database\Ddd\Select innerJoin($table, $cols, ...$cond)                                                               innerJoin 查询.
 * @method static \Leevel\Database\Ddd\Select leftJoin($table, $cols, ...$cond)                                                                leftJoin 查询.
 * @method static \Leevel\Database\Ddd\Select rightJoin($table, $cols, ...$cond)                                                               rightJoin 查询.
 * @method static \Leevel\Database\Ddd\Select fullJoin($table, $cols, ...$cond)                                                                fullJoin 查询.
 * @method static \Leevel\Database\Ddd\Select crossJoin($table, $cols, ...$cond)                                                               crossJoin 查询.
 * @method static \Leevel\Database\Ddd\Select naturalJoin($table, $cols, ...$cond)                                                             naturalJoin 查询.
 * @method static \Leevel\Database\Ddd\Select union($selects, string $type = 'UNION')                                                          添加一个 UNION 查询.
 * @method static \Leevel\Database\Ddd\Select unionAll($selects)                                                                               添加一个 UNION ALL 查询.
 * @method static \Leevel\Database\Ddd\Select groupBy($expression)                                                                             指定 GROUP BY 子句.
 * @method static \Leevel\Database\Ddd\Select having(...$cond)                                                                                 添加一个 HAVING 条件 < 参数规范参考 where()方法 >.
 * @method static \Leevel\Database\Ddd\Select orHaving(...$cond)                                                                               orHaving 查询条件.
 * @method static \Leevel\Database\Ddd\Select havingRaw(string $raw)                                                                           Having 原生查询.
 * @method static \Leevel\Database\Ddd\Select orHavingRaw(string $raw)                                                                         Having 原生 OR 查询.
 * @method static \Leevel\Database\Ddd\Select havingBetween(...$cond)                                                                          havingBetween 查询条件.
 * @method static \Leevel\Database\Ddd\Select havingNotBetween(...$cond)                                                                       havingNotBetween 查询条件.
 * @method static \Leevel\Database\Ddd\Select havingNull(...$cond)                                                                             havingNull 查询条件.
 * @method static \Leevel\Database\Ddd\Select havingNotNull(...$cond)                                                                          havingNotNull 查询条件.
 * @method static \Leevel\Database\Ddd\Select havingIn(...$cond)                                                                               havingIn 查询条件.
 * @method static \Leevel\Database\Ddd\Select havingNotIn(...$cond)                                                                            havingNotIn 查询条件.
 * @method static \Leevel\Database\Ddd\Select havingLike(...$cond)                                                                             havingLike 查询条件.
 * @method static \Leevel\Database\Ddd\Select havingNotLike(...$cond)                                                                          havingNotLike 查询条件.
 * @method static \Leevel\Database\Ddd\Select havingDate(...$cond)                                                                             havingDate 查询条件.
 * @method static \Leevel\Database\Ddd\Select havingDay(...$cond)                                                                              havingDay 查询条件.
 * @method static \Leevel\Database\Ddd\Select havingMonth(...$cond)                                                                            havingMonth 查询条件.
 * @method static \Leevel\Database\Ddd\Select havingYear(...$cond)                                                                             havingYear 查询条件.
 * @method static \Leevel\Database\Ddd\Select orderBy($expression, string $orderDefault = 'ASC')                                               添加排序.
 * @method static \Leevel\Database\Ddd\Select latest(string $field = 'create_at')                                                              最近排序数据.
 * @method static \Leevel\Database\Ddd\Select oldest(string $field = 'create_at')                                                              最早排序数据.
 * @method static \Leevel\Database\Ddd\Select distinct(bool $flag = true)                                                                      创建一个 SELECT DISTINCT 查询.
 * @method static \Leevel\Database\Ddd\Select count(string $field = '*', string $alias = 'row_count')                                          总记录数.
 * @method static \Leevel\Database\Ddd\Select avg(string $field, string $alias = 'avg_value')                                                  平均数.
 * @method static \Leevel\Database\Ddd\Select max(string $field, string $alias = 'max_value')                                                  最大值.
 * @method static \Leevel\Database\Ddd\Select min(string $field, string $alias = 'min_value')                                                  最小值.
 * @method static \Leevel\Database\Ddd\Select sum(string $field, string $alias = 'sum_value')                                                  合计
 * @method static \Leevel\Database\Ddd\Select one()                                                                                            指示仅查询第一个符合条件的记录.
 * @method static \Leevel\Database\Ddd\Select all()                                                                                            指示查询所有符合条件的记录.
 * @method static \Leevel\Database\Ddd\Select top(int $count = 30)                                                                             查询几条记录.
 * @method static \Leevel\Database\Ddd\Select limit(int $offset = 0, int $count = 0)                                                           limit 限制条数.
 * @method static \Leevel\Database\Ddd\Select forUpdate(bool $flag = true)                                                                     是否构造一个 FOR UPDATE 查询.
 * @method static \Leevel\Database\Ddd\Select setOption(string $name, $value)                                                                  设置查询参数.
 * @method static array getOption()                                                                                                            返回查询参数.
 * @method static array getBindParams()
 */
abstract class Relation
{
    /**
     * 查询对象
     *
     * @var \Leevel\Database\Ddd\Select
     */
    protected $select;

    /**
     * 关联目标模型实体.
     *
     * @var \Leevel\Database\Ddd\IEntity
     */
    protected $targetEntity;

    /**
     * 源模型实体.
     *
     * @var \Leevel\Database\Ddd\IEntity
     */
    protected $sourceEntity;

    /**
     * 目标关联字段.
     *
     * @var string
     */
    protected $targetKey;

    /**
     * 源关联字段.
     *
     * @var string
     */
    protected $sourceKey;

    /**
     * 是否初始化查询.
     *
     * @var bool
     */
    protected static $relationCondition = true;

    /**
     * 源数据为空.
     *
     * @var bool
     */
    protected $emptySourceData = false;

    /**
     * 构造函数.
     *
     * @param \Leevel\Database\Ddd\IEntity $targetEntity
     * @param \Leevel\Database\Ddd\IEntity $sourceEntity
     * @param string                       $targetKey
     * @param string                       $sourceKey
     */
    public function __construct(IEntity $targetEntity, IEntity $sourceEntity, string $targetKey, string $sourceKey)
    {
        $this->targetEntity = $targetEntity;
        $this->sourceEntity = $sourceEntity;
        $this->targetKey = $targetKey;
        $this->sourceKey = $sourceKey;

        $this->getSelectFromEntity();
        $this->addRelationCondition();
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
        $select = $this->select->{$method}(...$args);

        if ($this->getSelect() === $select) {
            return $this;
        }

        return $select;
    }

    /**
     * 返回查询.
     *
     * @return \Leevel\Database\Ddd\Select
     */
    public function getSelect(): Select
    {
        return $this->select;
    }

    /**
     * 取得预载入关联模型实体.
     *
     * @return mixed
     */
    public function getPreLoad()
    {
        return $this->targetEntity
            ->selectForEntity()
            ->preLoadResult(
                $this->findAll()
            );
    }

    /**
     * 取得关联目标模型实体.
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function getTargetEntity(): IEntity
    {
        return $this->targetEntity;
    }

    /**
     * 取得源模型实体.
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function getSourceEntity(): IEntity
    {
        return $this->sourceEntity;
    }

    /**
     * 取得目标字段.
     *
     * @return string
     */
    public function getTargetKey(): string
    {
        return $this->targetKey;
    }

    /**
     * 取得源字段.
     *
     * @return string
     */
    public function getSourceKey(): string
    {
        return $this->sourceKey;
    }

    /**
     * 获取不带关联条件的关联对象.
     *
     * @param \Closure $returnRelation
     *
     * @return \Leevel\Database\Ddd\Relation\Relation
     */
    public static function withoutRelationCondition(Closure $returnRelation): self
    {
        $old = static::$relationCondition;
        static::$relationCondition = false;
        $relation = call_user_func($returnRelation);
        static::$relationCondition = $old;

        return $relation;
    }

    /**
     * 关联基础查询条件.
     */
    abstract public function addRelationCondition(): void;

    /**
     * 设置预载入关联查询条件.
     *
     * @param \Leevel\Database\Ddd\IEntity[] $entitys
     */
    abstract public function preLoadCondition(array $entitys): void;

    /**
     * 匹配关联查询数据到模型实体 HasMany.
     *
     * @param \Leevel\Database\Ddd\IEntity[] $entitys
     * @param \Leevel\Collection\Collection  $result
     * @param string                         $relation
     *
     * @return array
     */
    abstract public function matchPreLoad(array $entitys, collection $result, string $relation): array;

    /**
     * 查询关联对象
     *
     * @return mixed
     */
    abstract public function sourceQuery();

    /**
     * 返回模型实体的主键.
     *
     * @param \Leevel\Database\Ddd\IEntity[] $entitys
     * @param null|string                    $key
     *
     * @return array
     */
    protected function getEntityKey(array $entitys, ?string $key = null): array
    {
        return array_unique(
            array_values(
                array_map(function ($entity) use ($key) {
                    return $key ?
                        $entity->prop($key) :
                        $entity->singleId();
                }, $entitys)
            )
        );
    }

    /**
     * 从模型实体返回查询.
     */
    protected function getSelectFromEntity(): void
    {
        $this->select = $this->targetEntity->select();
    }
}
