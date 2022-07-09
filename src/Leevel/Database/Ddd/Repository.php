<?php

declare(strict_types=1);

namespace Leevel\Database\Ddd;

use Closure;
use Leevel\Collection\Collection;
use Leevel\Database\Page;
use Leevel\Event\IDispatch;

/**
 * 仓储.
 *
 * @method static \Leevel\Database\Ddd\Entity entity()                                                                                                                                     获取实体.
 * @method static \Leevel\Database\Ddd\Select eager(array $relation)                                                                                                                       添加预载入关联查询.
 * @method static mixed preLoadResult(mixed $result)                                                                                                                                       尝试解析结果预载.
 * @method static \Leevel\Database\Ddd\Entity findEntity(null|int|\Closure $idOrCondition = null, array $column = [])                                                                      通过主键或条件查找实体.
 * @method static \Leevel\Collection\Collection findMany(null|array|\Closure $idsOrCondition = null, array $column = [])                                                                   通过主键或条件查找多个实体.
 * @method static \Leevel\Database\Ddd\Entity findOrFail(null|int|\Closure $idOrCondition = null, array $column = [])                                                                      通过主键或条件查找实体，未找到则抛出异常.
 * @method static \Leevel\Database\Ddd\Select withSoftDeleted()                                                                                                                            包含软删除数据的实体查询对象.
 * @method static \Leevel\Database\Ddd\Select onlySoftDeleted()
 * @method static void setCache(?\Leevel\Cache\Manager $cache)                                                                                                                             设置缓存.
 * @method static ?\Leevel\Cache\Manager getCache()                                                                                                                                        获取缓存.
 * @method static \Leevel\Database\Ddd\Select databaseSelect()                                                                                                                             返回查询对象.
 * @method static ?\PDO pdo(bool|int $master = false)                                                                                                                                      返回 PDO 查询连接.
 * @method static mixed query(string $sql, array $bindParams = [], bool|int $master = false, ?string $cacheName = null, ?int $cacheExpire = null, ?\Leevel\Cache\ICache $cache = null)     查询数据记录.
 * @method static array procedure(string $sql, array $bindParams = [], bool|int $master = false, ?string $cacheName = null, ?int $cacheExpire = null, ?\Leevel\Cache\ICache $cache = null) 查询存储过程数据记录.
 * @method static int|string execute(string $sql, array $bindParams = [])                                                                                                                  执行 SQL 语句.
 * @method static \Generator cursor(string $sql, array $bindParams = [], bool|int $master = false)                                                                                         游标查询.
 * @method static \PDOStatement prepare(string $sql, array $bindParams = [], bool|int $master = false)                                                                                     SQL 预处理.
 * @method static mixed transaction(\Closure $action)                                                                                                                                      执行数据库事务.
 * @method static void beginTransaction()                                                                                                                                                  启动事务.
 * @method static bool inTransaction()                                                                                                                                                     检查是否处于事务中.
 * @method static void commit()                                                                                                                                                            用于非自动提交状态下面的查询提交.
 * @method static void rollBack()                                                                                                                                                          事务回滚.
 * @method static string lastInsertId(?string $name = null)                                                                                                                                获取最后插入 ID 或者列.
 * @method static ?string getLastSql()                                                                                                                                                     获取最近一次查询的 SQL 语句.
 * @method static int numRows()                                                                                                                                                            返回影响记录.
 * @method static void close()                                                                                                                                                             关闭数据库.
 * @method static void freePDOStatement()                                                                                                                                                  释放 PDO 预处理查询.
 * @method static void closeConnects()                                                                                                                                                     关闭数据库连接.
 * @method static void releaseConnect()                                                                                                                                                    归还连接到连接池.
 * @method static string getRawSql(string $sql, array $bindParams)                                                                                                                         从 PDO 预处理语句中获取原始 SQL 查询字符串.
 * @method static string parseDsn(array $option)                                                                                                                                           DSN 解析.
 * @method static array getTableNames(string $dbName, bool|int $master = false)                                                                                                            取得数据库表名列表.
 * @method static array getTableColumns(string $tableName, bool|int $master = false)                                                                                                       取得数据库表字段信息.
 * @method static string identifierColumn(mixed $name)                                                                                                                                     SQL 字段格式化.
 * @method static string limitCount(?int $limitCount = null, ?int $limitOffset = null)                                                                                                     分析查询条数.
 * @method static \Leevel\Database\Condition databaseCondition()                                                                                                                           查询对象.
 * @method static \Leevel\Database\IDatabase databaseConnect()                                                                                                                             返回数据库连接对象.
 * @method static \Leevel\Database\Ddd\Select sql(bool $flag = true)                                                                                                                       指定返回 SQL 不做任何操作.
 * @method static \Leevel\Database\Ddd\Select master(bool|int $master = false)                                                                                                             设置是否查询主服务器.
 * @method static \Leevel\Database\Ddd\Select asSome(?\Closure $asSome = null, array $args = [])                                                                                           设置以某种包装返会结果.
 * @method static \Leevel\Database\Ddd\Select asArray(?\Closure $asArray = null)                                                                                                           设置返会结果为数组.
 * @method static \Leevel\Database\Ddd\Select asCollection(bool $asCollection = true)                                                                                                      设置是否以集合返回.
 * @method static mixed select(null|callable|\Leevel\Database\Select|string $data = null, array $bind = [], bool $flag = false)                                                            原生 SQL 查询数据.
 * @method static null|array|int insert(array|string $data, array $bind = [], bool|array $replace = false, bool $flag = false)                                                             插入数据 insert (支持原生 SQL).
 * @method static null|array|int insertAll(array $data, array $bind = [], bool|array $replace = false, bool $flag = false)                                                                 批量插入数据 insertAll.
 * @method static array|int update(array|string $data, array $bind = [], bool $flag = false)                                                                                               更新数据 update (支持原生 SQL).
 * @method static array|int updateColumn(string $column, mixed $value, array $bind = [], bool $flag = false)                                                                               更新某个字段的值
 * @method static array|int updateIncrease(string $column, int $step = 1, array $bind = [], bool $flag = false)                                                                            字段递增.
 * @method static array|int updateDecrease(string $column, int $step = 1, array $bind = [], bool $flag = false)                                                                            字段减少.
 * @method static array|int delete(?string $data = null, array $bind = [], bool $flag = false)                                                                                             删除数据 delete (支持原生 SQL).
 * @method static array|int truncate(bool $flag = false)                                                                                                                                   清空表重置自增 ID.
 * @method static mixed findOne(bool $flag = false)                                                                                                                                        返回一条记录.
 * @method static mixed findAll(bool $flag = false)                                                                                                                                        返回所有记录.
 * @method static mixed find(?int $num = null, bool $flag = false)                                                                                                                         返回最后几条记录.
 * @method static mixed value(string $field, bool $flag = false)                                                                                                                           返回一个字段的值
 * @method static array list(mixed $fieldValue, ?string $fieldKey = null, bool $flag = false)                                                                                              返回一列数据.
 * @method static void chunk(int $count, \Closure $chunk)                                                                                                                                  数据分块处理.
 * @method static void each(int $count, \Closure $each)                                                                                                                                    数据分块处理依次回调.
 * @method static array|int findCount(string $field = '*', string $alias = 'row_count', bool $flag = false)                                                                                总记录数.
 * @method static mixed findAvg(string $field, string $alias = 'avg_value', bool $flag = false)                                                                                            平均数.
 * @method static mixed findMax(string $field, string $alias = 'max_value', bool $flag = false)                                                                                            最大值.
 * @method static mixed findMin(string $field, string $alias = 'min_value', bool $flag = false)                                                                                            最小值.
 * @method static mixed findSum(string $field, string $alias = 'sum_value', bool $flag = false)                                                                                            合计.
 * @method static \Leevel\Database\Page page(int $currentPage, int $perPage = 10, bool $flag = false, string $column = '*', array $option = [])                                            分页查询.
 * @method static \Leevel\Database\Page pageMacro(int $currentPage, int $perPage = 10, bool $flag = false, array $option = [])                                                             创建一个无限数据的分页查询.
 * @method static \Leevel\Database\Page pagePrevNext(int $currentPage, int $perPage = 10, bool $flag = false, array $option = [])                                                          创建一个只有上下页的分页查询.
 * @method static int pageCount(string $cols = '*')                                                                                                                                        取得分页查询记录数量.
 * @method static string makeSql(bool $withLogicGroup = false)                                                                                                                             获得查询字符串.
 * @method static \Leevel\Database\Ddd\Select cache(string $name, ?int $expire = null, ?\Leevel\Cache\ICache $cache = null)                                                                设置查询缓存.
 * @method static \Leevel\Database\Ddd\Select forPage(int $page, int $perPage = 10)                                                                                                        根据分页设置条件.
 * @method static \Leevel\Database\Ddd\Select time(string $type = 'date')                                                                                                                  时间控制语句开始.
 * @method static \Leevel\Database\Ddd\Select endTime()                                                                                                                                    时间控制语句结束.
 * @method static \Leevel\Database\Ddd\Select reset(?string $option = null)                                                                                                                重置查询条件.
 * @method static \Leevel\Database\Ddd\Select comment(string $comment)                                                                                                                     查询注释.
 * @method static \Leevel\Database\Ddd\Select prefix(string $prefix)                                                                                                                       prefix 查询.
 * @method static \Leevel\Database\Ddd\Select table(array|\Closure|\Leevel\Database\Condition|\Leevel\Database\Select|string $table, array|string $cols = '*')                             添加一个要查询的表及其要查询的字段.
 * @method static string getAlias()                                                                                                                                                        获取表别名.
 * @method static \Leevel\Database\Ddd\Select columns(array|string $cols = '*', ?string $table = null)                                                                                     添加字段.
 * @method static \Leevel\Database\Ddd\Select setColumns(array|string $cols = '*', ?string $table = null)                                                                                  设置字段.
 * @method static string raw(string $raw)                                                                                                                                                  原生查询.
 * @method static \Leevel\Database\Ddd\Select where(...$cond)                                                                                                                              where 查询条件.
 * @method static \Leevel\Database\Ddd\Select orWhere(...$cond)                                                                                                                            orWhere 查询条件.
 * @method static \Leevel\Database\Ddd\Select whereRaw(string $raw)                                                                                                                        Where 原生查询.
 * @method static \Leevel\Database\Ddd\Select orWhereRaw(string $raw)                                                                                                                      Where 原生 OR 查询.
 * @method static \Leevel\Database\Ddd\Select whereExists($exists)                                                                                                                         exists 方法支持
 * @method static \Leevel\Database\Ddd\Select whereNotExists($exists)                                                                                                                      not exists 方法支持
 * @method static \Leevel\Database\Ddd\Select whereBetween(...$cond)                                                                                                                       whereBetween 查询条件.
 * @method static \Leevel\Database\Ddd\Select whereNotBetween(...$cond)                                                                                                                    whereNotBetween 查询条件.
 * @method static \Leevel\Database\Ddd\Select whereNull(...$cond)                                                                                                                          whereNull 查询条件.
 * @method static \Leevel\Database\Ddd\Select whereNotNull(...$cond)                                                                                                                       whereNotNull 查询条件.
 * @method static \Leevel\Database\Ddd\Select whereIn(...$cond)                                                                                                                            whereIn 查询条件.
 * @method static \Leevel\Database\Ddd\Select whereNotIn(...$cond)                                                                                                                         whereNotIn 查询条件.
 * @method static \Leevel\Database\Ddd\Select whereLike(...$cond)                                                                                                                          whereLike 查询条件.
 * @method static \Leevel\Database\Ddd\Select whereNotLike(...$cond)                                                                                                                       whereNotLike 查询条件.
 * @method static \Leevel\Database\Ddd\Select whereDate(...$cond)                                                                                                                          whereDate 查询条件.
 * @method static \Leevel\Database\Ddd\Select whereDay(...$cond)                                                                                                                           whereDay 查询条件.
 * @method static \Leevel\Database\Ddd\Select whereMonth(...$cond)                                                                                                                         whereMonth 查询条件.
 * @method static \Leevel\Database\Ddd\Select whereYear(...$cond)                                                                                                                          whereYear 查询条件.
 * @method static \Leevel\Database\Ddd\Select bind(mixed $names, mixed $value = null, ?int $dataType = null)                                                                               参数绑定支持.
 * @method static \Leevel\Database\Ddd\Select forceIndex(array|string $indexs, string $type = 'FORCE')                                                                                     index 强制索引（或者忽略索引）.
 * @method static \Leevel\Database\Ddd\Select ignoreIndex(array|string $indexs)                                                                                                            index 忽略索引.
 * @method static \Leevel\Database\Ddd\Select join(array|\Closure|\Leevel\Database\Condition|\Leevel\Database\Select|string $table, array|string $cols, ...$cond)                          join 查询.
 * @method static \Leevel\Database\Ddd\Select innerJoin(array|\Closure|\Leevel\Database\Condition|\Leevel\Database\Select|string $table, array|string $cols, ...$cond)                     innerJoin 查询.
 * @method static \Leevel\Database\Ddd\Select leftJoin(array|\Closure|\Leevel\Database\Condition|\Leevel\Database\Select|string $table, array|string $cols, ...$cond)                      leftJoin 查询.
 * @method static \Leevel\Database\Ddd\Select rightJoin(array|\Closure|\Leevel\Database\Condition|\Leevel\Database\Select|string $table, array|string $cols, ...$cond)                     rightJoin 查询.
 * @method static \Leevel\Database\Ddd\Select fullJoin(array|\Closure|\Leevel\Database\Condition|\Leevel\Database\Select|string $table, array|string $cols, ...$cond)                      fullJoin 查询.
 * @method static \Leevel\Database\Ddd\Select crossJoin(array|\Closure|\Leevel\Database\Condition|\Leevel\Database\Select|string $table, array|string $cols, ...$cond)                     crossJoin 查询.
 * @method static \Leevel\Database\Ddd\Select naturalJoin(array|\Closure|\Leevel\Database\Condition|\Leevel\Database\Select|string $table, array|string $cols, ...$cond)                   naturalJoin 查询.
 * @method static \Leevel\Database\Ddd\Select union(\Leevel\Database\Select|\Leevel\Database\Condition|array|callable|string $selects, string $type = 'UNION')                             添加一个 UNION 查询.
 * @method static \Leevel\Database\Ddd\Select unionAll(\Leevel\Database\Select|\Leevel\Database\Condition|array|callable|string $selects)                                                  添加一个 UNION ALL 查询.
 * @method static \Leevel\Database\Ddd\Select groupBy(array|string $expression)                                                                                                            指定 GROUP BY 子句.
 * @method static \Leevel\Database\Ddd\Select having(...$cond)                                                                                                                             添加一个 HAVING 条件.
 * @method static \Leevel\Database\Ddd\Select orHaving(...$cond)                                                                                                                           orHaving 查询条件.
 * @method static \Leevel\Database\Ddd\Select havingRaw(string $raw)                                                                                                                       having 原生查询.
 * @method static \Leevel\Database\Ddd\Select orHavingRaw(string $raw)                                                                                                                     having 原生 OR 查询.
 * @method static \Leevel\Database\Ddd\Select havingBetween(...$cond)                                                                                                                      havingBetween 查询条件.
 * @method static \Leevel\Database\Ddd\Select havingNotBetween(...$cond)                                                                                                                   havingNotBetween 查询条件.
 * @method static \Leevel\Database\Ddd\Select havingNull(...$cond)                                                                                                                         havingNull 查询条件.
 * @method static \Leevel\Database\Ddd\Select havingNotNull(...$cond)                                                                                                                      havingNotNull 查询条件.
 * @method static \Leevel\Database\Ddd\Select havingIn(...$cond)                                                                                                                           havingIn 查询条件.
 * @method static \Leevel\Database\Ddd\Select havingNotIn(...$cond)                                                                                                                        havingNotIn 查询条件.
 * @method static \Leevel\Database\Ddd\Select havingLike(...$cond)                                                                                                                         havingLike 查询条件.
 * @method static \Leevel\Database\Ddd\Select havingNotLike(...$cond)                                                                                                                      havingNotLike 查询条件.
 * @method static \Leevel\Database\Ddd\Select havingDate(...$cond)                                                                                                                         havingDate 查询条件.
 * @method static \Leevel\Database\Ddd\Select havingDay(...$cond)                                                                                                                          havingDay 查询条件.
 * @method static \Leevel\Database\Ddd\Select havingMonth(...$cond)                                                                                                                        havingMonth 查询条件.
 * @method static \Leevel\Database\Ddd\Select havingYear(...$cond)                                                                                                                         havingYear 查询条件.
 * @method static \Leevel\Database\Ddd\Select orderBy(array|string $expression, string $orderDefault = 'ASC')                                                                              添加排序.
 * @method static \Leevel\Database\Ddd\Select latest(string $field = 'create_at')                                                                                                          最近排序数据.
 * @method static \Leevel\Database\Ddd\Select oldest(string $field = 'create_at')                                                                                                          最早排序数据.
 * @method static \Leevel\Database\Ddd\Select distinct(bool $flag = true)                                                                                                                  创建一个 SELECT DISTINCT 查询.
 * @method static \Leevel\Database\Ddd\Select count(string $field = '*', string $alias = 'row_count')                                                                                      总记录数.
 * @method static \Leevel\Database\Ddd\Select avg(string $field, string $alias = 'avg_value')                                                                                              平均数.
 * @method static \Leevel\Database\Ddd\Select max(string $field, string $alias = 'max_value')                                                                                              最大值.
 * @method static \Leevel\Database\Ddd\Select min(string $field, string $alias = 'min_value')                                                                                              最小值.
 * @method static \Leevel\Database\Ddd\Select sum(string $field, string $alias = 'sum_value')                                                                                              合计
 * @method static \Leevel\Database\Ddd\Select one()                                                                                                                                        指示仅查询第一个符合条件的记录.
 * @method static \Leevel\Database\Ddd\Select all()                                                                                                                                        指示查询所有符合条件的记录.
 * @method static \Leevel\Database\Ddd\Select top(int $count = 30)                                                                                                                         查询几条记录.
 * @method static \Leevel\Database\Ddd\Select limit(int $offset = 0, int $count = 0)                                                                                                       limit 限制条数.
 * @method static \Leevel\Database\Ddd\Select forUpdate(bool $flag = true)                                                                                                                 排它锁 FOR UPDATE 查询.
 * @method static \Leevel\Database\Ddd\Select lockShare(bool $flag = true)                                                                                                                 共享锁 LOCK SHARE 查询.
 * @method static array getBindParams()                                                                                                                                                    返回参数绑定.
 * @method static void resetBindParams(array $bindParams = [])                                                                                                                             重置参数绑定.
 * @method static void setBindParamsPrefix(string $bindParamsPrefix)                                                                                                                       设置参数绑定前缀.
 * @method static \Leevel\Database\Ddd\Select if(mixed $value = false)                                                                                                                     条件语句 if.
 * @method static \Leevel\Database\Ddd\Select elif(mixed $value = false)                                                                                                                   条件语句 elif.
 * @method static \Leevel\Database\Ddd\Select else()                                                                                                                                       条件语句 else.
 * @method static \Leevel\Database\Ddd\Select fi()                                                                                                                                         条件语句 fi.
 * @method static \Leevel\Database\Ddd\Select setFlowControl(bool $inFlowControl, bool $isFlowControlTrue)                                                                                 设置当前条件表达式状态.
 * @method static bool checkFlowControl()                                                                                                                                                  验证一下条件表达式是否通过.
 */
class Repository
{
    /**
     * 批量插入数据事件.
     */
    public const INSERT_ALL_EVENT = 'database.repository.insertall';

    /**
     * 查询初始化回调.
     */
    protected ?Closure $selectBoot = null;

    /**
     * 批量插入回调.
     */
    protected ?Closure $insertAllBoot = null;

    /**
     * 构造函数.
     */
    public function __construct(protected Entity $entity, protected ?IDispatch $dispatch = null)
    {
    }

    /**
     * 实现魔术方法 __call.
     */
    public function __call(string $method, array $args): mixed
    {
        return $this->select()->{$method}(...$args);
    }

    /**
     * 批量插入回调.
     */
    public function insertAllBoot(Closure $boot): void
    {
        $this->insertAllBoot = $boot;
    }
    
    /**
     * 批量插入数据 insertAll.
     */
    public function insertAll(array $data, array $bind = [], bool|array $replace = false, bool $flag = false): null|array|int
    {
        if ($this->dispatch) {
            $this->dispatch->handle(
                self::INSERT_ALL_EVENT,
                $this,
            );
        }

        if ($this->insertAllBoot) {
            $insertAllBoot = $this->insertAllBoot;
            $insertAllBoot($data, $bind, $replace, $flag);
        }

        return $this->select()->insertAll($data, $bind, $replace, $flag);
    }

    /**
     * 取得所有记录.
     */
    public function findAll(null|Closure|ISpecification $condition = null): Collection
    {
        $select = $this
            ->select()
            ->databaseSelect();

        if ($condition) {
            $this->normalizeCondition($condition, $select);
        }

        return $select->findAll();
    }

    /**
     * 返回一列数据.
     */
    public function findList(null|Closure|ISpecification $condition, mixed $fieldValue, ?string $fieldKey = null): array
    {
        $select = $this
            ->select()
            ->databaseSelect();

        if ($condition) {
            $this->normalizeCondition($condition, $select);
        }

        return $select->list($fieldValue, $fieldKey);
    }

    /**
     * 取得记录数量.
     */
    public function findCount(null|Closure|ISpecification $condition = null, string $field = '*'): int
    {
        $select = $this
            ->select()
            ->databaseSelect();

        if ($condition) {
            $this->normalizeCondition($condition, $select);
        }

        return $select->findCount($field);
    }

    /**
     * 分页查询.
     *
     * - 可以渲染 HTML.
     */
    public function findPage(int $currentPage, int $perPage = 10, null|Closure|ISpecification $condition = null, bool $flag = false, string $column = '*', array $option = []): Page
    {
        $select = $this
            ->select()
            ->databaseSelect();

        if ($condition) {
            $this->normalizeCondition($condition, $select);
        }

        return $select->page($currentPage, $perPage, $flag, $column, $option);
    }

    /**
     * 创建一个无限数据的分页查询.
     */
    public function findPageMacro(int $currentPage, int $perPage = 10, null|Closure|ISpecification $condition = null, bool $flag = false, array $option = []): Page
    {
        $select = $this
            ->select()
            ->databaseSelect();

        if ($condition) {
            $this->normalizeCondition($condition, $select);
        }

        return $select->pageMacro($currentPage, $perPage, $flag, $option);
    }

    /**
     * 创建一个只有上下页的分页查询.
     */
    public function findPagePrevNext(int $currentPage, int $perPage = 10, null|Closure|ISpecification $condition = null, bool $flag = false, array $option = []): Page
    {
        $select = $this
            ->select()
            ->databaseSelect();

        if ($condition) {
            $this->normalizeCondition($condition, $select);
        }

        return $select->pagePrevNext($currentPage, $perPage, $flag, $option);
    }

    /**
     * 条件查询器.
     */
    public function condition(Closure|ISpecification $condition): Select
    {
        $select = $this
            ->select()
            ->databaseSelect();
        $this->normalizeCondition($condition, $select);

        return $select;
    }

    /**
     * 查询初始化回调.
     */
    public function selectBoot(Closure $boot): static
    {
        $repository = clone $this;
        $repository->selectBoot = $boot;

        return $repository;
    }

    /**
     * 返回基础查询.
     * @todo 删除此特性
     */
    public function select(): Select
    {
        if ($this->selectBoot) {
            $selectBoot = $this->selectBoot;

            return $selectBoot($this->entity);
        }

        return $this->entity->select();
    }

    /**
     * 新增实体.
     */
    public function createEntity(Entity $entity): mixed
    {
        return $entity->create()->flush();
    }

    /**
     * 更新实体.
     */
    public function updateEntity(Entity $entity): mixed
    {
        return $entity->update()->flush();
    }

    /**
     * 替换实体.
     */
    public function replaceEntity(Entity $entity): mixed
    {
        return $entity->replace()->flush();
    }

    /**
     * 响应删除.
     */
    public function deleteEntity(Entity $entity, bool $forceDelete = false): mixed
    {
        return $entity->delete($forceDelete)->flush();
    }

    /**
     * 强制删除实体.
     */
    public function forceDeleteEntity(Entity $entity): mixed
    {
        return $entity->delete(true)->flush();
    }

    /**
     * 从数据库重新读取当前对象的属性.
     */
    public function refreshEntity(Entity $entity): void
    {
        $entity->refresh();
    }

    /**
     * 返回实体.
     */
    public function entity(): Entity
    {
        return $this->entity;
    }

    /**
     * 处理查询条件.
     */
    protected function normalizeCondition(Closure|ISpecification $condition, Select $select): void
    {
        if ($condition instanceof ISpecification) {
            $this->normalizeSpec($select, $condition);

            return;
        }

        $condition($select, $this->entity);
    }

    /**
     * 处理规约查询.
     */
    protected function normalizeSpec(Select $select, ISpecification $spec): void
    {
        if ($spec instanceof ISpecification && $spec->isSatisfiedBy($this->entity)) {
            $spec->handle($select, $this->entity);
        }
    }
}
