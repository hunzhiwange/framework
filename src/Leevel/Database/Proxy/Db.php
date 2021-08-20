<?php

declare(strict_types=1);

namespace Leevel\Database\Proxy;

use Leevel\Database\Manager;
use Leevel\Di\Container;

/**
 * 代理 database.
 *
 * @method static void setCache(?\Leevel\Cache\Manager $cache)                                                                                                             设置缓存.
 * @method static ?\Leevel\Cache\Manager getCache()                                                                                                                        获取缓存.
 * @method static \Leevel\Database\Select databaseSelect()                                                                                                                 返回查询对象.
 * @method static ?\PDO pdo($master = false)                                                                                                                               返回 PDO 查询连接.
 * @method static mixed query(string $sql, array $bindParams = [], $master = false, ?string $cacheName = null, ?int $cacheExpire = null, ?\Leevel\Cache\ICache $cache = null)     查询数据记录.
 * @method static array procedure(string $sql, array $bindParams = [], $master = false, ?string $cacheName = null, ?int $cacheExpire = null, ?\Leevel\Cache\ICache $cache = null) 查询存储过程数据记录.
 * @method static int|string execute(string $sql, array $bindParams = [])                                                                                                       执行 SQL 语句.
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
 * @method static void releaseConnect()                                                                                                                                     归还连接到连接池.
 * @method static string getRawSql(string $sql, array $bindParams)                                                                                                         从 PDO 预处理语句中获取原始 SQL 查询字符串.
 * @method static string parseDsn(array $option)                                                                                                                           DSN 解析.
 * @method static array getTableNames(string $dbName, $master = false)                                                                                                     取得数据库表名列表.
 * @method static array getTableColumns(string $tableName, $master = false)                                                                                                取得数据库表字段信息.
 * @method static string identifierColumn($name)                                                                                                                           SQL 字段格式化.
 * @method static string limitCount(?int $limitCount = null, ?int $limitOffset = null)                                                                                     分析查询条数.
 * @method static \Leevel\Database\Condition databaseCondition()                                                                                                           查询对象.
 * @method static \Leevel\Database\IDatabase databaseConnect()                                                                                                             返回数据库连接对象.
 * @method static \Leevel\Database\Select sql(bool $flag = true)                                                                                                           指定返回 SQL 不做任何操作.
 * @method static \Leevel\Database\Select master(bool $master = false)                                                                                                     设置是否查询主服务器.
 * @method static \Leevel\Database\Select asSome(?\Closure $asSome = null, array $args = [])                                                                               设置以某种包装返会结果.
 * @method static \Leevel\Database\Select asArray(?\Closure $asArray = null)                                                                                               设置返会结果为数组.
 * @method static \Leevel\Database\Select asCollection(bool $asCollection = true)                                                                                          设置是否以集合返回.
 * @method static mixed select(null|callable|\Leevel\Database\Select|string $data = null, array $bind = [], bool $flag = false)                                                                                         原生 SQL 查询数据.
 * @method static null|array|int insert(array|string $data, array $bind = [], bool $replace = false, bool $flag = false)                                                                         插入数据 insert (支持原生 SQL).
 * @method static null|array|int insertAll(array $data, array $bind = [], bool $replace = false, bool $flag = false)                                                                批量插入数据 insertAll.
 * @method static array|int update(array|string $data, array $bind = [], bool $flag = false)                                                                                                更新数据 update (支持原生 SQL).
 * @method static array|int updateColumn(string $column, mixed $value, array $bind = [], bool $flag = false)                                                                         更新某个字段的值
 * @method static array|int updateIncrease(string $column, int $step = 1, array $bind = [], bool $flag = false)                                                                字段递增.
 * @method static array|int updateDecrease(string $column, int $step = 1, array $bind = [], bool $flag = false)                                                                字段减少.
 * @method static array|int delete(?string $data = null, array $bind = [], bool $flag = false)                                                                                 删除数据 delete (支持原生 SQL).
 * @method static array|int truncate(bool $flag = false)                                                                                                                       清空表重置自增 ID.
 * @method static mixed findOne(bool $flag = false)                                                                                                                        返回一条记录.
 * @method static mixed findAll(bool $flag = false)                                                                                                                        返回所有记录.
 * @method static mixed find(?int $num = null, bool $flag = false)                                                                                                         返回最后几条记录.
 * @method static mixed value(string $field, bool $flag = false)                                                                                                           返回一个字段的值
 * @method static array list(mixed $fieldValue, ?string $fieldKey = null, bool $flag = false)                                                                                    返回一列数据.
 * @method static void chunk(int $count, \Closure $chunk)                                                                                                                  数据分块处理.
 * @method static void each(int $count, \Closure $each)                                                                                                                    数据分块处理依次回调.
 * @method static array|int findCount(string $field = '*', string $alias = 'row_count', bool $flag = false)                                                                    总记录数.
 * @method static mixed findAvg(string $field, string $alias = 'avg_value', bool $flag = false)                                                                            平均数.
 * @method static mixed findMax(string $field, string $alias = 'max_value', bool $flag = false)                                                                            最大值.
 * @method static mixed findMin(string $field, string $alias = 'min_value', bool $flag = false)                                                                            最小值.
 * @method static mixed findSum(string $field, string $alias = 'sum_value', bool $flag = false)                                                                            合计.
 * @method static \Leevel\Database\Page page(int $currentPage, int $perPage = 10, bool $flag = false, string $column = '*', array $option = [])                            分页查询.
 * @method static \Leevel\Database\Page pageMacro(int $currentPage, int $perPage = 10, bool $flag = false, array $option = [])                                             创建一个无限数据的分页查询.
 * @method static \Leevel\Database\Page pagePrevNext(int $currentPage, int $perPage = 10, bool $flag = false, array $option = [])                                          创建一个只有上下页的分页查询.
 * @method static int pageCount(string $cols = '*')                                                                                                                        取得分页查询记录数量.
 * @method static string makeSql(bool $withLogicGroup = false)                                                                                                             获得查询字符串.
 * @method static \Leevel\Database\Select cache(string $name, ?int $expire = null, ?\Leevel\Cache\ICache $cache = null)                                                                设置查询缓存.
 * @method static \Leevel\Database\Select forPage(int $page, int $perPage = 10)                                                                                            根据分页设置条件.
 * @method static \Leevel\Database\Select time(string $type = 'date')                                                                                                      时间控制语句开始.
 * @method static \Leevel\Database\Select endTime()                                                                                                                        时间控制语句结束.
 * @method static \Leevel\Database\Select reset(?string $option = null)                                                                                                    重置查询条件.
 * @method static \Leevel\Database\Select comment(string $comment)                                                                                                         查询注释.
 * @method static \Leevel\Database\Select prefix(string $prefix)                                                                                                           prefix 查询.
 * @method static \Leevel\Database\Select table(array|\Closure|\Leevel\Database\Condition|\Leevel\Database\Select|string $table, array|string $cols = '*')                                                                                                       添加一个要查询的表及其要查询的字段.
 * @method static string getAlias()                                                                                                                                        获取表别名.
 * @method static \Leevel\Database\Select columns(array|string $cols = '*', ?string $table = null)                                                                                      添加字段.
 * @method static \Leevel\Database\Select setColumns(array|string $cols = '*', ?string $table = null)                                                                                   设置字段.
 * @method static string raw(string $raw)                                                                                                                                  原生查询.
 * @method static \Leevel\Database\Select where(...$cond)                                                                                                                  where 查询条件.
 * @method static \Leevel\Database\Select orWhere(...$cond)                                                                                                                orWhere 查询条件.
 * @method static \Leevel\Database\Select whereRaw(string $raw)                                                                                                            Where 原生查询.
 * @method static \Leevel\Database\Select orWhereRaw(string $raw)                                                                                                          Where 原生 OR 查询.
 * @method static \Leevel\Database\Select whereExists($exists)                                                                                                             exists 方法支持
 * @method static \Leevel\Database\Select whereNotExists($exists)                                                                                                          not exists 方法支持
 * @method static \Leevel\Database\Select whereBetween(...$cond)                                                                                                           whereBetween 查询条件.
 * @method static \Leevel\Database\Select whereNotBetween(...$cond)                                                                                                        whereNotBetween 查询条件.
 * @method static \Leevel\Database\Select whereNull(...$cond)                                                                                                              whereNull 查询条件.
 * @method static \Leevel\Database\Select whereNotNull(...$cond)                                                                                                           whereNotNull 查询条件.
 * @method static \Leevel\Database\Select whereIn(...$cond)                                                                                                                whereIn 查询条件.
 * @method static \Leevel\Database\Select whereNotIn(...$cond)                                                                                                             whereNotIn 查询条件.
 * @method static \Leevel\Database\Select whereLike(...$cond)                                                                                                              whereLike 查询条件.
 * @method static \Leevel\Database\Select whereNotLike(...$cond)                                                                                                           whereNotLike 查询条件.
 * @method static \Leevel\Database\Select whereDate(...$cond)                                                                                                              whereDate 查询条件.
 * @method static \Leevel\Database\Select whereDay(...$cond)                                                                                                               whereDay 查询条件.
 * @method static \Leevel\Database\Select whereMonth(...$cond)                                                                                                             whereMonth 查询条件.
 * @method static \Leevel\Database\Select whereYear(...$cond)                                                                                                              whereYear 查询条件.
 * @method static \Leevel\Database\Select bind($names, $value = null, ?int $dataType = null)                                                                               参数绑定支持.
 * @method static \Leevel\Database\Select forceIndex(array|string $indexs, string $type = 'FORCE')                                                                                             index 强制索引（或者忽略索引）.
 * @method static \Leevel\Database\Select ignoreIndex(array|string $indexs)                                                                                                             index 忽略索引.
 * @method static \Leevel\Database\Select join(array|\Closure|\Leevel\Database\Condition|\Leevel\Database\Select|string $table, array|string $cols, ...$cond)                                                                                                    join 查询.
 * @method static \Leevel\Database\Select innerJoin(array|\Closure|\Leevel\Database\Condition|\Leevel\Database\Select|string $table, array|string $cols, ...$cond)                                                                                               innerJoin 查询.
 * @method static \Leevel\Database\Select leftJoin(array|\Closure|\Leevel\Database\Condition|\Leevel\Database\Select|string $table, array|string $cols, ...$cond)                                                                                                leftJoin 查询.
 * @method static \Leevel\Database\Select rightJoin(array|\Closure|\Leevel\Database\Condition|\Leevel\Database\Select|string $table, array|string $cols, ...$cond)                                                                                               rightJoin 查询.
 * @method static \Leevel\Database\Select fullJoin(array|\Closure|\Leevel\Database\Condition|\Leevel\Database\Select|string $table, array|string $cols, ...$cond)                                                                                                fullJoin 查询.
 * @method static \Leevel\Database\Select crossJoin(array|\Closure|\Leevel\Database\Condition|\Leevel\Database\Select|string $table, array|string $cols, ...$cond)                                                                                               crossJoin 查询.
 * @method static \Leevel\Database\Select naturalJoin(array|\Closure|\Leevel\Database\Condition|\Leevel\Database\Select|string $table, array|string $cols, ...$cond)                                                                                             naturalJoin 查询.
 * @method static \Leevel\Database\Select union($selects, string $type = 'UNION')                                                                                          添加一个 UNION 查询.
 * @method static \Leevel\Database\Select unionAll($selects)                                                                                                               添加一个 UNION ALL 查询.
 * @method static \Leevel\Database\Select groupBy($expression)                                                                                                             指定 GROUP BY 子句.
 * @method static \Leevel\Database\Select having(...$cond)                                                                                                                 添加一个 HAVING 条件.
 * @method static \Leevel\Database\Select orHaving(...$cond)                                                                                                               orHaving 查询条件.
 * @method static \Leevel\Database\Select havingRaw(string $raw)                                                                                                           having 原生查询.
 * @method static \Leevel\Database\Select orHavingRaw(string $raw)                                                                                                         having 原生 OR 查询.
 * @method static \Leevel\Database\Select havingBetween(...$cond)                                                                                                          havingBetween 查询条件.
 * @method static \Leevel\Database\Select havingNotBetween(...$cond)                                                                                                       havingNotBetween 查询条件.
 * @method static \Leevel\Database\Select havingNull(...$cond)                                                                                                             havingNull 查询条件.
 * @method static \Leevel\Database\Select havingNotNull(...$cond)                                                                                                          havingNotNull 查询条件.
 * @method static \Leevel\Database\Select havingIn(...$cond)                                                                                                               havingIn 查询条件.
 * @method static \Leevel\Database\Select havingNotIn(...$cond)                                                                                                            havingNotIn 查询条件.
 * @method static \Leevel\Database\Select havingLike(...$cond)                                                                                                             havingLike 查询条件.
 * @method static \Leevel\Database\Select havingNotLike(...$cond)                                                                                                          havingNotLike 查询条件.
 * @method static \Leevel\Database\Select havingDate(...$cond)                                                                                                             havingDate 查询条件.
 * @method static \Leevel\Database\Select havingDay(...$cond)                                                                                                              havingDay 查询条件.
 * @method static \Leevel\Database\Select havingMonth(...$cond)                                                                                                            havingMonth 查询条件.
 * @method static \Leevel\Database\Select havingYear(...$cond)                                                                                                             havingYear 查询条件.
 * @method static \Leevel\Database\Select orderBy($expression, string $orderDefault = 'ASC')                                                                               添加排序.
 * @method static \Leevel\Database\Select latest(string $field = 'create_at')                                                                                              最近排序数据.
 * @method static \Leevel\Database\Select oldest(string $field = 'create_at')                                                                                              最早排序数据.
 * @method static \Leevel\Database\Select distinct(bool $flag = true)                                                                                                      创建一个 SELECT DISTINCT 查询.
 * @method static \Leevel\Database\Select count(string $field = '*', string $alias = 'row_count')                                                                          总记录数.
 * @method static \Leevel\Database\Select avg(string $field, string $alias = 'avg_value')                                                                                  平均数.
 * @method static \Leevel\Database\Select max(string $field, string $alias = 'max_value')                                                                                  最大值.
 * @method static \Leevel\Database\Select min(string $field, string $alias = 'min_value')                                                                                  最小值.
 * @method static \Leevel\Database\Select sum(string $field, string $alias = 'sum_value')                                                                                  合计
 * @method static \Leevel\Database\Select one()                                                                                                                            指示仅查询第一个符合条件的记录.
 * @method static \Leevel\Database\Select all()                                                                                                                            指示查询所有符合条件的记录.
 * @method static \Leevel\Database\Select top(int $count = 30)                                                                                                             查询几条记录.
 * @method static \Leevel\Database\Select limit(int $offset = 0, int $count = 0)                                                                                           limit 限制条数.
 * @method static \Leevel\Database\Select forUpdate(bool $flag = true)                                                                                                     排它锁 FOR UPDATE 查询.
 * @method static \Leevel\Database\Select lockShare(bool $flag = true)                                                                                                     共享锁 LOCK SHARE 查询.
 * @method static array getBindParams()                                                                                                                                    返回参数绑定.
 * @method static void resetBindParams(array $bindParams = [])                                                                                                             重置参数绑定.
 * @method static void setBindParamsPrefix(string $bindParamsPrefix)                                                                                                       设置参数绑定前缀.
 * @method static \Leevel\Di\IContainer container() 返回 IOC 容器. 
 * @method static \Leevel\Database\IDatabase connect(?string $connect = null, bool $newConnect = false) 连接并返回连接对象. 
 * @method static \Leevel\Database\IDatabase reconnect(?string $connect = null) 重新连接. 
 * @method static void disconnect(?string $connect = null) 删除连接. 
 * @method static array getConnects() 取回所有连接. 
 * @method static string getDefaultConnect() 返回默认连接. 
 * @method static void setDefaultConnect(string $name) 设置默认连接. 
 * @method static mixed getContainerOption(?string $name = null) 获取容器配置值. 
 * @method static void setContainerOption(string $name, mixed $value) 设置容器配置值. 
 * @method static void extend(string $connect, \Closure $callback) 扩展自定义连接. 
 * @method static array normalizeConnectOption(string $connect) 整理连接配置. 
 */
class Db
{
    /**
     * 实现魔术方法 __callStatic.
     */
    public static function __callStatic(string $method, array $args): mixed
    {
        return self::proxy()->{$method}(...$args);
    }

    /**
     * 代理服务.
     */
    public static function proxy(): Manager
    {
        return Container::singletons()->make('databases');
    }
}
