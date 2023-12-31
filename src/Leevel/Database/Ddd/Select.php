<?php

declare(strict_types=1);

namespace Leevel\Database\Ddd;

use Leevel\Database\Ddd\Relation\Relation;
use Leevel\Database\Page;
use Leevel\Database\Select as DatabaseSelect;

/**
 * 实体查询.
 *
 * @method static void                                                                   setCache(?\Leevel\Cache\Manager $cache)                                                                                                                            设置缓存.
 * @method static ?\Leevel\Cache\Manager                                                 getCache()                                                                                                                                                         获取缓存.
 * @method static \Leevel\Database\Ddd\Select                                            databaseSelect()                                                                                                                                                   返回查询对象.
 * @method static ?\PDO                                                                  pdo(bool|int $master = false)                                                                                                                                      返回 PDO 查询连接.
 * @method static mixed                                                                  query(string $sql, array $bindParams = [], bool|int $master = false, ?string $cacheName = null, ?int $cacheExpire = null, ?\Leevel\Cache\ICache $cache = null)     查询数据记录.
 * @method static array                                                                  procedure(string $sql, array $bindParams = [], bool|int $master = false, ?string $cacheName = null, ?int $cacheExpire = null, ?\Leevel\Cache\ICache $cache = null) 查询存储过程数据记录.
 * @method static int|string                                                             execute(string $sql, array $bindParams = [])                                                                                                                       执行 SQL 语句.
 * @method static \Generator                                                             cursor(string $sql, array $bindParams = [], bool|int $master = false)                                                                                              游标查询.
 * @method static \PDOStatement                                                          prepare(string $sql, array $bindParams = [], bool|int $master = false)                                                                                             SQL 预处理.
 * @method static mixed                                                                  transaction(\Closure $action)                                                                                                                                      执行数据库事务.
 * @method static void                                                                   beginTransaction()                                                                                                                                                 启动事务.
 * @method static bool                                                                   inTransaction()                                                                                                                                                    检查是否处于事务中.
 * @method static void                                                                   commit()                                                                                                                                                           用于非自动提交状态下面的查询提交.
 * @method static void                                                                   rollBack()                                                                                                                                                         事务回滚.
 * @method static string                                                                 lastInsertId(?string $name = null)                                                                                                                                 获取最后插入 ID 或者列.
 * @method static string                                                                 getLastSql()                                                                                                                                                       获取最近一次查询的 SQL 语句.
 * @method static void                                                                   setRealLastSql(array $realLastSql)                                                                                                                                 设置最近一次真实查询的 SQL 语句.
 * @method static array                                                                  getRealLastSql()                                                                                                                                                   获取最近一次真实查询的 SQL 语句.
 * @method static int                                                                    numRows()                                                                                                                                                          返回影响记录.
 * @method static void                                                                   close()                                                                                                                                                            关闭数据库.
 * @method static void                                                                   freePDOStatement()                                                                                                                                                 释放 PDO 预处理查询.
 * @method static void                                                                   closeConnects()                                                                                                                                                    关闭数据库连接.
 * @method static string                                                                 getRawSql(string $sql, array $bindParams)                                                                                                                          从 PDO 预处理语句中获取原始 SQL 查询字符串.
 * @method static string                                                                 parseDsn(array $option)                                                                                                                                            DSN 解析.
 * @method static array                                                                  getTableNames(string $dbName, bool|int $master = false)                                                                                                            取得数据库表名列表.
 * @method static array                                                                  getTableColumns(string $tableName, bool|int $master = false)                                                                                                       取得数据库表字段信息.
 * @method static string                                                                 identifierColumn(string $name)                                                                                                                                     SQL 字段格式化.
 * @method static string                                                                 limitCount(?int $limitCount = null, ?int $limitOffset = null)                                                                                                      分析查询条数.
 * @method static \Leevel\Database\Condition                                             databaseCondition()                                                                                                                                                查询对象.
 * @method static \Leevel\Database\IDatabase                                             databaseConnect()                                                                                                                                                  返回数据库连接对象.
 * @method static \Leevel\Database\Ddd\Select                                            master(bool|int $master = false)                                                                                                                                   设置是否查询主服务器.
 * @method static \Leevel\Database\Ddd\Select                                            asSome(?\Closure $asSome = null, array $args = [])                                                                                                                 设置以某种包装返会结果.
 * @method static \Leevel\Database\Ddd\Select                                            asArray(?\Closure $asArray = null)                                                                                                                                 设置返会结果为数组.
 * @method static \Leevel\Database\Ddd\Select                                            asCollection(bool $asCollection = true, array $valueTypes = [])                                                                                                    设置是否以集合返回.
 * @method static mixed                                                                  select(null|callable|\Leevel\Database\Select|string $data = null, array $bind = [])                                                                                原生 SQL 查询数据.
 * @method static int|string                                                             insert(array|string $data, array $bind = [], array|bool $replace = false)                                                                                          插入数据 insert (支持原生 SQL).
 * @method static int|string                                                             insertAll(array $data, array $bind = [], array|bool $replace = false)                                                                                              批量插入数据 insertAll.
 * @method static int                                                                    update(array|string $data, array $bind = [])                                                                                                                       更新数据 update (支持原生 SQL).
 * @method static int                                                                    updateColumn(string $column, mixed $value, array $bind = [])                                                                                                       更新某个字段的值
 * @method static int                                                                    updateIncrease(string $column, int $step = 1, array $bind = [])                                                                                                    字段递增.
 * @method static int                                                                    updateDecrease(string $column, int $step = 1, array $bind = [])                                                                                                    字段减少.
 * @method static int                                                                    delete(?string $data = null, array $bind = [])                                                                                                                     删除数据 delete (支持原生 SQL).
 * @method static array|int                                                              truncate()                                                                                                                                                         清空表重置自增 ID.
 * @method static mixed                                                                  findOne()                                                                                                                                                          返回一条记录.
 * @method static \Leevel\Database\Ddd\EntityCollection|\Leevel\Support\Collection|array findAll()                                                                                                                                                          返回所有记录.
 * @method static array                                                                  findArray()                                                                                                                                                        以数组返回所有记录.
 * @method static array                                                                  findAsArray()                                                                                                                                                      以数组返回所有记录（每一项也为数组）.
 * @method static \Leevel\Database\Ddd\EntityCollection|\Leevel\Support\Collection       findCollection()                                                                                                                                                   以集合返回所有记录.
 * @method static \Leevel\Database\Ddd\EntityCollection|\Leevel\Support\Collection|array find(?int $num = null)                                                                                                                                             返回最后几条记录.
 * @method static mixed                                                                  value(string $field)                                                                                                                                               返回一个字段的值
 * @method static array                                                                  list(mixed $fieldValue, ?string $fieldKey = null)                                                                                                                  返回一列数据.
 * @method static void                                                                   chunk(int $count, \Closure $chunk)                                                                                                                                 数据分块处理.
 * @method static void                                                                   each(int $count, \Closure $each)                                                                                                                                   数据分块处理依次回调.
 * @method static int                                                                    findCount(string $field = '*', string $alias = 'row_count')                                                                                                        总记录数.
 * @method static mixed                                                                  findAvg(string $field, string $alias = 'avg_value')                                                                                                                平均数.
 * @method static mixed                                                                  findMax(string $field, string $alias = 'max_value')                                                                                                                最大值.
 * @method static mixed                                                                  findMin(string $field, string $alias = 'min_value')                                                                                                                最小值.
 * @method static mixed                                                                  findSum(string $field, string $alias = 'sum_value')                                                                                                                合计.
 * @method static \Leevel\Database\Page                                                  page(int $currentPage, int $perPage = 10, string $column = '*', array $option = [])                                                                                分页查询.
 * @method static \Leevel\Database\Page                                                  pageMacro(int $currentPage, int $perPage = 10, array $option = [])                                                                                                 创建一个无限数据的分页查询.
 * @method static \Leevel\Database\Page                                                  pagePrevNext(int $currentPage, int $perPage = 10, array $option = [])                                                                                              创建一个只有上下页的分页查询.
 * @method static int                                                                    pageCount(string $cols = '*')                                                                                                                                      取得分页查询记录数量.
 * @method static string                                                                 makeSql(bool $withLogicGroup = false)                                                                                                                              获得查询字符串.
 * @method static \Leevel\Database\Ddd\Select                                            cache(string $name, ?int $expire = null, ?\Leevel\Cache\ICache $cache = null)                                                                                      设置查询缓存.
 * @method static \Leevel\Database\Ddd\Select                                            forPage(int $page, int $perPage = 10)                                                                                                                              根据分页设置条件.
 * @method static \Leevel\Database\Ddd\Select                                            time(string $type = 'date')                                                                                                                                        时间控制语句开始.
 * @method static \Leevel\Database\Ddd\Select                                            endTime()                                                                                                                                                          时间控制语句结束.
 * @method static \Leevel\Database\Ddd\Select                                            reset(?string $option = null)                                                                                                                                      重置查询条件.
 * @method static \Leevel\Database\Ddd\Select                                            comment(string $comment)                                                                                                                                           查询注释.
 * @method static \Leevel\Database\Ddd\Select                                            prefix(string $prefix)                                                                                                                                             prefix 查询.
 * @method static \Leevel\Database\Ddd\Select                                            table(array|\Closure|\Leevel\Database\Condition|\Leevel\Database\Select|string $table, array|string $cols = '*')                                                   添加一个要查询的表及其要查询的字段.
 * @method static string                                                                 getAlias()                                                                                                                                                         获取表别名.
 * @method static \Leevel\Database\Ddd\Select                                            columns(array|string $cols = '*', ?string $table = null)                                                                                                           添加字段.
 * @method static \Leevel\Database\Ddd\Select                                            setColumns(array|string $cols = '*', ?string $table = null)                                                                                                        设置字段.
 * @method static \Leevel\Database\Ddd\Select                                            field(array|string $cols = '*', ?string $table = null)                                                                                                             设置字段别名方法.
 * @method static string                                                                 raw(string $raw)                                                                                                                                                   原生查询.
 * @method static \Leevel\Database\Ddd\Select                                            middlewares(string ...$middlewares)                                                                                                                                查询中间件.
 * @method static array                                                                  registerMiddlewares(array $middlewares, bool $force = false)                                                                                                       注册查询中间件.
 * @method static \Leevel\Database\Ddd\Select                                            where(...$cond)                                                                                                                                                    where 查询条件.
 * @method static \Leevel\Database\Ddd\Select                                            orWhere(...$cond)                                                                                                                                                  orWhere 查询条件.
 * @method static \Leevel\Database\Ddd\Select                                            whereRaw(string $raw)                                                                                                                                              Where 原生查询.
 * @method static \Leevel\Database\Ddd\Select                                            orWhereRaw(string $raw)                                                                                                                                            Where 原生 OR 查询.
 * @method static \Leevel\Database\Ddd\Select                                            whereExists($exists)                                                                                                                                               exists 方法支持
 * @method static \Leevel\Database\Ddd\Select                                            whereNotExists($exists)                                                                                                                                            not exists 方法支持
 * @method static \Leevel\Database\Ddd\Select                                            whereBetween(...$cond)                                                                                                                                             whereBetween 查询条件.
 * @method static \Leevel\Database\Ddd\Select                                            whereNotBetween(...$cond)                                                                                                                                          whereNotBetween 查询条件.
 * @method static \Leevel\Database\Ddd\Select                                            whereNull(...$cond)                                                                                                                                                whereNull 查询条件.
 * @method static \Leevel\Database\Ddd\Select                                            whereNotNull(...$cond)                                                                                                                                             whereNotNull 查询条件.
 * @method static \Leevel\Database\Ddd\Select                                            whereIn(...$cond)                                                                                                                                                  whereIn 查询条件.
 * @method static \Leevel\Database\Ddd\Select                                            whereNotIn(...$cond)                                                                                                                                               whereNotIn 查询条件.
 * @method static \Leevel\Database\Ddd\Select                                            whereLike(...$cond)                                                                                                                                                whereLike 查询条件.
 * @method static \Leevel\Database\Ddd\Select                                            whereNotLike(...$cond)                                                                                                                                             whereNotLike 查询条件.
 * @method static \Leevel\Database\Ddd\Select                                            whereDate(...$cond)                                                                                                                                                whereDate 查询条件.
 * @method static \Leevel\Database\Ddd\Select                                            whereDay(...$cond)                                                                                                                                                 whereDay 查询条件.
 * @method static \Leevel\Database\Ddd\Select                                            whereMonth(...$cond)                                                                                                                                               whereMonth 查询条件.
 * @method static \Leevel\Database\Ddd\Select                                            whereYear(...$cond)                                                                                                                                                whereYear 查询条件.
 * @method static \Leevel\Database\Ddd\Select                                            bind(mixed $names, mixed $value = null, ?int $dataType = null)                                                                                                     参数绑定支持.
 * @method static \Leevel\Database\Ddd\Select                                            forceIndex(array|string $indexs, string $type = 'FORCE')                                                                                                           index 强制索引（或者忽略索引）.
 * @method static \Leevel\Database\Ddd\Select                                            ignoreIndex(array|string $indexs)                                                                                                                                  index 忽略索引.
 * @method static \Leevel\Database\Ddd\Select                                            join(array|\Closure|\Leevel\Database\Condition|\Leevel\Database\Select|string $table, array|string $cols, ...$cond)                                                join 查询.
 * @method static \Leevel\Database\Ddd\Select                                            innerJoin(array|\Closure|\Leevel\Database\Condition|\Leevel\Database\Select|string $table, array|string $cols, ...$cond)                                           innerJoin 查询.
 * @method static \Leevel\Database\Ddd\Select                                            leftJoin(array|\Closure|\Leevel\Database\Condition|\Leevel\Database\Select|string $table, array|string $cols, ...$cond)                                            leftJoin 查询.
 * @method static \Leevel\Database\Ddd\Select                                            rightJoin(array|\Closure|\Leevel\Database\Condition|\Leevel\Database\Select|string $table, array|string $cols, ...$cond)                                           rightJoin 查询.
 * @method static \Leevel\Database\Ddd\Select                                            fullJoin(array|\Closure|\Leevel\Database\Condition|\Leevel\Database\Select|string $table, array|string $cols, ...$cond)                                            fullJoin 查询.
 * @method static \Leevel\Database\Ddd\Select                                            crossJoin(array|\Closure|\Leevel\Database\Condition|\Leevel\Database\Select|string $table, array|string $cols, ...$cond)                                           crossJoin 查询.
 * @method static \Leevel\Database\Ddd\Select                                            naturalJoin(array|\Closure|\Leevel\Database\Condition|\Leevel\Database\Select|string $table, array|string $cols, ...$cond)                                         naturalJoin 查询.
 * @method static \Leevel\Database\Ddd\Select                                            union(array|callable|\Leevel\Database\Condition|\Leevel\Database\Select|string $selects, string $type = 'UNION')                                                   添加一个 UNION 查询.
 * @method static \Leevel\Database\Ddd\Select                                            unionAll(array|callable|\Leevel\Database\Condition|\Leevel\Database\Select|string $selects)                                                                        添加一个 UNION ALL 查询.
 * @method static \Leevel\Database\Ddd\Select                                            groupBy(array|string $expression)                                                                                                                                  指定 GROUP BY 子句.
 * @method static \Leevel\Database\Ddd\Select                                            having(...$cond)                                                                                                                                                   添加一个 HAVING 条件.
 * @method static \Leevel\Database\Ddd\Select                                            orHaving(...$cond)                                                                                                                                                 orHaving 查询条件.
 * @method static \Leevel\Database\Ddd\Select                                            havingRaw(string $raw)                                                                                                                                             having 原生查询.
 * @method static \Leevel\Database\Ddd\Select                                            orHavingRaw(string $raw)                                                                                                                                           having 原生 OR 查询.
 * @method static \Leevel\Database\Ddd\Select                                            havingBetween(...$cond)                                                                                                                                            havingBetween 查询条件.
 * @method static \Leevel\Database\Ddd\Select                                            havingNotBetween(...$cond)                                                                                                                                         havingNotBetween 查询条件.
 * @method static \Leevel\Database\Ddd\Select                                            havingNull(...$cond)                                                                                                                                               havingNull 查询条件.
 * @method static \Leevel\Database\Ddd\Select                                            havingNotNull(...$cond)                                                                                                                                            havingNotNull 查询条件.
 * @method static \Leevel\Database\Ddd\Select                                            havingIn(...$cond)                                                                                                                                                 havingIn 查询条件.
 * @method static \Leevel\Database\Ddd\Select                                            havingNotIn(...$cond)                                                                                                                                              havingNotIn 查询条件.
 * @method static \Leevel\Database\Ddd\Select                                            havingLike(...$cond)                                                                                                                                               havingLike 查询条件.
 * @method static \Leevel\Database\Ddd\Select                                            havingNotLike(...$cond)                                                                                                                                            havingNotLike 查询条件.
 * @method static \Leevel\Database\Ddd\Select                                            havingDate(...$cond)                                                                                                                                               havingDate 查询条件.
 * @method static \Leevel\Database\Ddd\Select                                            havingDay(...$cond)                                                                                                                                                havingDay 查询条件.
 * @method static \Leevel\Database\Ddd\Select                                            havingMonth(...$cond)                                                                                                                                              havingMonth 查询条件.
 * @method static \Leevel\Database\Ddd\Select                                            havingYear(...$cond)                                                                                                                                               havingYear 查询条件.
 * @method static \Leevel\Database\Ddd\Select                                            orderBy(array|string $expression, string $orderDefault = 'ASC')                                                                                                    添加排序.
 * @method static \Leevel\Database\Ddd\Select                                            latest(string $field = 'create_at')                                                                                                                                最近排序数据.
 * @method static \Leevel\Database\Ddd\Select                                            oldest(string $field = 'create_at')                                                                                                                                最早排序数据.
 * @method static \Leevel\Database\Ddd\Select                                            distinct(bool $flag = true)                                                                                                                                        创建一个 SELECT DISTINCT 查询.
 * @method static \Leevel\Database\Ddd\Select                                            count(string $field = '*', string $alias = 'row_count')                                                                                                            总记录数.
 * @method static \Leevel\Database\Ddd\Select                                            avg(string $field, string $alias = 'avg_value')                                                                                                                    平均数.
 * @method static \Leevel\Database\Ddd\Select                                            max(string $field, string $alias = 'max_value')                                                                                                                    最大值.
 * @method static \Leevel\Database\Ddd\Select                                            min(string $field, string $alias = 'min_value')                                                                                                                    最小值.
 * @method static \Leevel\Database\Ddd\Select                                            sum(string $field, string $alias = 'sum_value')                                                                                                                    合计
 * @method static \Leevel\Database\Ddd\Select                                            one()                                                                                                                                                              指示仅查询第一个符合条件的记录.
 * @method static \Leevel\Database\Ddd\Select                                            all()                                                                                                                                                              指示查询所有符合条件的记录.
 * @method static \Leevel\Database\Ddd\Select                                            top(int $count = 30)                                                                                                                                               查询几条记录.
 * @method static \Leevel\Database\Ddd\Select                                            limit(int $offset = 0, int $count = 0)                                                                                                                             limit 限制条数.
 * @method static \Leevel\Database\Ddd\Select                                            forUpdate(bool $flag = true)                                                                                                                                       排它锁 FOR UPDATE 查询.
 * @method static \Leevel\Database\Ddd\Select                                            lockShare(bool $flag = true)                                                                                                                                       共享锁 LOCK SHARE 查询.
 * @method static array                                                                  getBindParams()                                                                                                                                                    返回参数绑定.                                                                                                    返回参数绑定.
 * @method static void                                                                   resetBindParams(array $bindParams = [])                                                                                                                            重置参数绑定.
 * @method static void                                                                   setBindParamsPrefix(string $bindParamsPrefix)                                                                                                                      设置参数绑定前缀.
 * @method static \Leevel\Database\Ddd\Select                                            if(mixed $value = false)                                                                                                                                           条件语句 if.
 * @method static \Leevel\Database\Ddd\Select                                            elif(mixed $value = false)                                                                                                                                         条件语句 elif.
 * @method static \Leevel\Database\Ddd\Select                                            else()                                                                                                                                                             条件语句 else.
 * @method static \Leevel\Database\Ddd\Select                                            fi()                                                                                                                                                               条件语句 fi.
 * @method static \Leevel\Database\Ddd\Select                                            setFlowControl(bool $inFlowControl, bool $isFlowControlTrue)                                                                                                       设置当前条件表达式状态.
 * @method static bool                                                                   checkFlowControl()                                                                                                                                                 验证一下条件表达式是否通过.
 */
class Select
{
    /**
     * 查询.
     */
    protected DatabaseSelect $select;

    /**
     * 关联预载入.
     */
    protected array $preLoads = [];

    /**
     * 是否执行预载入查询.
     */
    protected static bool $preLoadsResult = true;

    /**
     * 构造函数.
     */
    public function __construct(protected Entity $entity, int $softDeletedType = Entity::WITHOUT_SOFT_DELETED)
    {
        $this->initSelect($softDeletedType);
    }

    /**
     * 实现魔术方法 __call.
     */
    public function __call(string $method, array $args): mixed
    {
        $result = $this->select->{$method}(...$args);

        return $this->normalizeSelectResult($result);
    }

    /**
     * 获取实体.
     */
    public function entity(): Entity
    {
        return $this->entity;
    }

    /**
     * 获取不执行预载入的查询结果.
     */
    public static function withoutPreLoadsResult(\Closure $call): mixed
    {
        $old = static::$preLoadsResult;
        static::$preLoadsResult = false;

        try {
            $result = $call();
            static::$preLoadsResult = $old;
        } catch (\Throwable $e) {
            static::$preLoadsResult = $old;

            throw $e;
        }

        return $result;
    }

    /**
     * 包含软删除数据的实体查询对象.
     *
     * - 获取包含软删除的数据.
     * - 会覆盖查询条件，需要首先调用.
     *
     * @todo remove this(注意关联模型)
     */
    public function withSoftDeleted(): self
    {
        $this->initSelect(Entity::WITH_SOFT_DELETED);

        return $this;
    }

    /**
     * 仅仅包含软删除数据的实体查询对象.
     *
     * - 获取只包含软删除的数据.
     * - 会覆盖查询条件，需要首先调用.
     *
     * @todo remove this(注意关联模型)
     */
    public function onlySoftDeleted(): self
    {
        $this->initSelect(Entity::ONLY_SOFT_DELETED);

        return $this;
    }

    /**
     * 添加预载入关联查询.
     */
    public function eager(array $relation): self
    {
        $this->preLoads = array_merge(
            $this->preLoads,
            $this->parseWithRelation($relation)
        );

        return $this;
    }

    /**
     * 尝试解析结果预载.
     */
    public function preLoadResult(mixed $result): mixed
    {
        [$result, $type, $collectionType] = $this->conversionToEntitys($result);
        if ($type) {
            $result = $this->preLoadRelation($result);
            if ('entity' === $type) {
                $result = reset($result);
            } elseif ('collection' === $type) {
                $result = new EntityCollection($result, $collectionType[0] ?? null);
            }
        }

        return $result;
    }

    /**
     * 通过主键或条件查找实体.
     */
    public function findEntity(null|int|string|array|\Closure $idOrCondition = null, array $column = ['*']): Entity
    {
        $result = $this->select
            ->if(\is_int($idOrCondition) || \is_string($idOrCondition))
            ->where($this->entity->singlePrimaryKey(), '=', $idOrCondition)
            ->elif($idOrCondition instanceof \Closure || \is_array($idOrCondition))
            ->where($idOrCondition)
            ->fi()
            ->setColumns($column)
            ->findOne()
        ;

        // @phpstan-ignore-next-line
        return $this->normalizeSelectResult($result);
    }

    /**
     * 通过主键或条件查找多个实体.
     */
    public function findMany(null|array|\Closure $idsOrCondition = null, array $column = ['*']): EntityCollection
    {
        // @todo 需要删除掉，这里设计很别扭，空直接抛出异常
        if (\is_array($idsOrCondition) && empty($idsOrCondition)) {
            return $this->entity->collection();
        }

        $result = $this->select
            ->if(\is_array($idsOrCondition))
            ->whereIn($this->entity->singlePrimaryKey(), $idsOrCondition)
            ->elif($idsOrCondition instanceof \Closure)
            ->where($idsOrCondition)
            ->fi()
            ->setColumns($column)
            ->findAll()
        ;

        // @phpstan-ignore-next-line
        return $this->normalizeSelectResult($result);
    }

    /**
     * 通过主键或条件查找实体，未找到则抛出异常.
     */
    public function findOrFail(null|int|string|array|\Closure $idOrCondition = null, array $column = ['*']): Entity
    {
        // 没有查询主键字段，自动补上主键自动
        $singlePrimaryKey = $this->entity->singlePrimaryKey();
        if (!\in_array('*', $column, true) && !\in_array($singlePrimaryKey, $column, true)) {
            $column[] = $singlePrimaryKey;
        }

        $result = $this->findEntity($idOrCondition, $column);
        if (null !== $result->prop($singlePrimaryKey)) {
            return $result;
        }

        throw (new EntityNotFoundException())->setEntity($this->entity::class);
    }

    /**
     * 初始化查询.
     */
    protected function initSelect(int $softDeletedType): void
    {
        $this->select = $this->entity->selectCollection($softDeletedType);
    }

    /**
     * 预载入实体.
     */
    protected function preLoadRelation(array $entities): array
    {
        foreach ($this->preLoads as $name => $relationScope) {
            if (!str_contains($name, '.')) {
                $entities = $this->loadRelation($entities, $name, $relationScope);
            }
        }

        return $entities;
    }

    /**
     * 取得关联实体.
     */
    protected function getRelation(string $name, null|array|string|\Closure $relationScope = null): Relation
    {
        $relation = Relation::withoutRelationCondition(function () use ($name, $relationScope): Relation {
            return $this->entity->relation($name, $relationScope);
        });

        $nested = $this->nestedRelation($name);
        if (\count($nested) > 0) {
            $relation->getSelect()->eager($nested);
        }

        return $relation;
    }

    /**
     * 尝试取得嵌套关联.
     */
    protected function nestedRelation(string $relation): array
    {
        $nested = [];
        foreach ($this->preLoads as $name => $relationScope) {
            if ($this->isNested($name, $relation)) {
                $nested[substr($name, \strlen($relation.'.'))] = $relationScope;
            }
        }

        return $nested;
    }

    /**
     * 判断是否存在嵌套关联.
     */
    protected function isNested(string $name, string $relation): bool
    {
        return str_contains($name, '.') && str_starts_with($name, $relation.'.');
    }

    /**
     * 格式化预载入关联.
     */
    protected function parseWithRelation(array $relation): array
    {
        $data = [];
        foreach ($relation as $name => $relationScope) {
            if (\is_int($name)) {
                [$name, $relationScope] = [$relationScope, null];
            }

            $data = $this->parseNestedWith($name, $data);
            $data[$name] = $relationScope;
        }

        return $data;
    }

    /**
     * 解析嵌套关联.
     */
    protected function parseNestedWith(string $name, array $result): array
    {
        $progress = [];
        foreach (explode('.', $name) as $segment) {
            $progress[] = $segment;
            if (!isset($result[$last = implode('.', $progress)])) {
                $result[$last] = null;
            }
        }

        return $result;
    }

    /**
     * 转换结果到实体类型.
     */
    protected function conversionToEntitys(mixed $result): array
    {
        $type = '';
        $collectionType = [];
        if ($result instanceof EntityCollection) {
            $data = [];
            foreach ($result as $entity) {
                $data[] = $entity;
            }
            $collectionType = $result->getValueTypes();
            $result = $data;
            $type = 'collection';
        } elseif ($result instanceof Entity) {
            $result = [$result];
            $type = 'entity';
        }

        return [$result, $type, $collectionType];
    }

    /**
     * 关联数据设置到实体上.
     */
    protected function loadRelation(array $entities, string $name, null|array|string|\Closure $relationScope = null): array
    {
        $relation = $this->getRelation($name, $relationScope);
        $relation->preLoadCondition($entities);

        return $relation->matchPreLoad($entities, $relation->getPreLoad(), $name);
    }

    /**
     * 整理查询结果.
     */
    protected function normalizeSelectResult(mixed $result): mixed
    {
        if ($result instanceof DatabaseSelect) {
            return $this;
        }

        if (false === static::$preLoadsResult || !$this->preLoads) {
            return $result;
        }

        if ($result instanceof Page) {
            $result->setData($this->preLoadResult($result->getData()));

            return $result;
        }

        return $this->preLoadResult($result);
    }
}
