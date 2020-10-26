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

namespace Leevel\Database;

use Closure;
use Generator;
use Leevel\Cache\Manager as CacheManager;
use Leevel\Database\Mysql\MysqlPool as MysqlPools;
use PDOStatement;

/**
 * MySQL pool.
 *
 * @codeCoverageIgnore
 *
 * @method static \Leevel\Database\Condition databaseCondition()                                                                                查询对象.
 * @method static \Leevel\Database\IDatabase databaseConnect()                                                                                  返回数据库连接对象.
 * @method static \Leevel\Database\Select sql(bool $flag = true)                                                                                指定返回 SQL 不做任何操作.
 * @method static \Leevel\Database\Select master($master = false)                                                                               设置是否查询主服务器.
 * @method static \Leevel\Database\Select asSome(?\Closure $asSome = null, array $args = [])                                                    设置以某种包装返会结果.
 * @method static \Leevel\Database\Select asArray(?\Closure $asArray = null)                                                                    设置返会结果为数组.
 * @method static \Leevel\Database\Select asCollection(bool $asCollection = true)                                                               设置是否以集合返回.
 * @method static mixed select($data = null, array $bind = [], bool $flag = false)                                                              原生 SQL 查询数据.
 * @method static mixed insert($data, array $bind = [], bool $replace = false, bool $flag = false)                                              插入数据 insert (支持原生 SQL).
 * @method static mixed insertAll(array $data, array $bind = [], bool $replace = false, bool $flag = false)                                     批量插入数据 insertAll.
 * @method static mixed update($data, array $bind = [], bool $flag = false)                                                                     更新数据 update (支持原生 SQL).
 * @method static mixed updateColumn(string $column, $value, array $bind = [], bool $flag = false)                                              更新某个字段的值
 * @method static mixed updateIncrease(string $column, int $step = 1, array $bind = [], bool $flag = false)                                     字段递增.
 * @method static mixed updateDecrease(string $column, int $step = 1, array $bind = [], bool $flag = false)                                     字段减少.
 * @method static mixed delete(?string $data = null, array $bind = [], bool $flag = false)                                                      删除数据 delete (支持原生 SQL).
 * @method static mixed truncate(bool $flag = false)                                                                                            清空表重置自增 ID.
 * @method static mixed findOne(bool $flag = false)                                                                                             返回一条记录.
 * @method static mixed findAll(bool $flag = false)                                                                                             返回所有记录.
 * @method static mixed find(?int $num = null, bool $flag = false)                                                                              返回最后几条记录.
 * @method static mixed value(string $field, bool $flag = false)                                                                                返回一个字段的值
 * @method static array list($fieldValue, ?string $fieldKey = null, bool $flag = false)                                                         返回一列数据.
 * @method static void chunk(int $count, \Closure $chunk)                                                                                       数据分块处理.
 * @method static void each(int $count, \Closure $each)                                                                                         数据分块处理依次回调.
 * @method static mixed findCount(string $field = '*', string $alias = 'row_count', bool $flag = false)                                         总记录数.
 * @method static mixed findAvg(string $field, string $alias = 'avg_value', bool $flag = false)                                                 平均数.
 * @method static mixed findMax(string $field, string $alias = 'max_value', bool $flag = false)                                                 最大值.
 * @method static mixed findMin(string $field, string $alias = 'min_value', bool $flag = false)                                                 最小值.
 * @method static mixed findSum(string $field, string $alias = 'sum_value', bool $flag = false)                                                 合计.
 * @method static \Leevel\Database\Page page(int $currentPage, int $perPage = 10, bool $flag = false, string $column = '*', array $option = []) 分页查询.
 * @method static \Leevel\Database\Page pageMacro(int $currentPage, int $perPage = 10, bool $flag = false, array $option = [])                  创建一个无限数据的分页查询.
 * @method static \Leevel\Database\Page pagePrevNext(int $currentPage, int $perPage = 10, bool $flag = false, array $option = [])               创建一个只有上下页的分页查询.
 * @method static int pageCount(string $cols = '*')                                                                                             取得分页查询记录数量.
 * @method static string makeSql(bool $withLogicGroup = false)                                                                                  获得查询字符串.
 * @method static \Leevel\Database\Select cache(string $name, ?int $expire = null, ?string $connect = null)                                     设置查询缓存.
 * @method static \Leevel\Database\Select forPage(int $page, int $perPage = 10)                                                                 根据分页设置条件.
 * @method static \Leevel\Database\Select time(string $type = 'date')                                                                           时间控制语句开始.
 * @method static \Leevel\Database\Select endTime()                                                                                             时间控制语句结束.
 * @method static \Leevel\Database\Select reset(?string $option = null)                                                                         重置查询条件.
 * @method static \Leevel\Database\Select comment(string $comment)                                                                              查询注释.
 * @method static \Leevel\Database\Select prefix(string $prefix)                                                                                prefix 查询.
 * @method static \Leevel\Database\Select table($table, $cols = '*')                                                                            添加一个要查询的表及其要查询的字段.
 * @method static string getAlias()                                                                                                             获取表别名.
 * @method static \Leevel\Database\Select columns($cols = '*', ?string $table = null)                                                           添加字段.
 * @method static \Leevel\Database\Select setColumns($cols = '*', ?string $table = null)                                                        设置字段.
 * @method static string raw(string $raw)                                                                                                       原生查询.
 * @method static \Leevel\Database\Select where(...$cond)                                                                                       where 查询条件.
 * @method static \Leevel\Database\Select orWhere(...$cond)                                                                                     orWhere 查询条件.
 * @method static \Leevel\Database\Select whereRaw(string $raw)                                                                                 Where 原生查询.
 * @method static \Leevel\Database\Select orWhereRaw(string $raw)                                                                               Where 原生 OR 查询.
 * @method static \Leevel\Database\Select whereExists($exists)                                                                                  exists 方法支持
 * @method static \Leevel\Database\Select whereNotExists($exists)                                                                               not exists 方法支持
 * @method static \Leevel\Database\Select whereBetween(...$cond)                                                                                whereBetween 查询条件.
 * @method static \Leevel\Database\Select whereNotBetween(...$cond)                                                                             whereNotBetween 查询条件.
 * @method static \Leevel\Database\Select whereNull(...$cond)                                                                                   whereNull 查询条件.
 * @method static \Leevel\Database\Select whereNotNull(...$cond)                                                                                whereNotNull 查询条件.
 * @method static \Leevel\Database\Select whereIn(...$cond)                                                                                     whereIn 查询条件.
 * @method static \Leevel\Database\Select whereNotIn(...$cond)                                                                                  whereNotIn 查询条件.
 * @method static \Leevel\Database\Select whereLike(...$cond)                                                                                   whereLike 查询条件.
 * @method static \Leevel\Database\Select whereNotLike(...$cond)                                                                                whereNotLike 查询条件.
 * @method static \Leevel\Database\Select whereDate(...$cond)                                                                                   whereDate 查询条件.
 * @method static \Leevel\Database\Select whereDay(...$cond)                                                                                    whereDay 查询条件.
 * @method static \Leevel\Database\Select whereMonth(...$cond)                                                                                  whereMonth 查询条件.
 * @method static \Leevel\Database\Select whereYear(...$cond)                                                                                   whereYear 查询条件.
 * @method static \Leevel\Database\Select bind($names, $value = null, ?int $dataType = null)                                                    参数绑定支持.
 * @method static \Leevel\Database\Select forceIndex($indexs, $type = 'FORCE')                                                                  index 强制索引（或者忽略索引）.
 * @method static \Leevel\Database\Select ignoreIndex($indexs)                                                                                  index 忽略索引.
 * @method static \Leevel\Database\Select join($table, $cols, ...$cond)                                                                         join 查询.
 * @method static \Leevel\Database\Select innerJoin($table, $cols, ...$cond)                                                                    innerJoin 查询.
 * @method static \Leevel\Database\Select leftJoin($table, $cols, ...$cond)                                                                     leftJoin 查询.
 * @method static \Leevel\Database\Select rightJoin($table, $cols, ...$cond)                                                                    rightJoin 查询.
 * @method static \Leevel\Database\Select fullJoin($table, $cols, ...$cond)                                                                     fullJoin 查询.
 * @method static \Leevel\Database\Select crossJoin($table, $cols, ...$cond)                                                                    crossJoin 查询.
 * @method static \Leevel\Database\Select naturalJoin($table, $cols, ...$cond)                                                                  naturalJoin 查询.
 * @method static \Leevel\Database\Select union($selects, string $type = 'UNION')                                                               添加一个 UNION 查询.
 * @method static \Leevel\Database\Select unionAll($selects)                                                                                    添加一个 UNION ALL 查询.
 * @method static \Leevel\Database\Select groupBy($expression)                                                                                  指定 GROUP BY 子句.
 * @method static \Leevel\Database\Select having(...$cond)                                                                                      添加一个 HAVING 条件.
 * @method static \Leevel\Database\Select orHaving(...$cond)                                                                                    orHaving 查询条件.
 * @method static \Leevel\Database\Select havingRaw(string $raw)                                                                                having 原生查询.
 * @method static \Leevel\Database\Select orHavingRaw(string $raw)                                                                              having 原生 OR 查询.
 * @method static \Leevel\Database\Select havingBetween(...$cond)                                                                               havingBetween 查询条件.
 * @method static \Leevel\Database\Select havingNotBetween(...$cond)                                                                            havingNotBetween 查询条件.
 * @method static \Leevel\Database\Select havingNull(...$cond)                                                                                  havingNull 查询条件.
 * @method static \Leevel\Database\Select havingNotNull(...$cond)                                                                               havingNotNull 查询条件.
 * @method static \Leevel\Database\Select havingIn(...$cond)                                                                                    havingIn 查询条件.
 * @method static \Leevel\Database\Select havingNotIn(...$cond)                                                                                 havingNotIn 查询条件.
 * @method static \Leevel\Database\Select havingLike(...$cond)                                                                                  havingLike 查询条件.
 * @method static \Leevel\Database\Select havingNotLike(...$cond)                                                                               havingNotLike 查询条件.
 * @method static \Leevel\Database\Select havingDate(...$cond)                                                                                  havingDate 查询条件.
 * @method static \Leevel\Database\Select havingDay(...$cond)                                                                                   havingDay 查询条件.
 * @method static \Leevel\Database\Select havingMonth(...$cond)                                                                                 havingMonth 查询条件.
 * @method static \Leevel\Database\Select havingYear(...$cond)                                                                                  havingYear 查询条件.
 * @method static \Leevel\Database\Select orderBy($expression, string $orderDefault = 'ASC')                                                    添加排序.
 * @method static \Leevel\Database\Select latest(string $field = 'create_at')                                                                   最近排序数据.
 * @method static \Leevel\Database\Select oldest(string $field = 'create_at')                                                                   最早排序数据.
 * @method static \Leevel\Database\Select distinct(bool $flag = true)                                                                           创建一个 SELECT DISTINCT 查询.
 * @method static \Leevel\Database\Select count(string $field = '*', string $alias = 'row_count')                                               总记录数.
 * @method static \Leevel\Database\Select avg(string $field, string $alias = 'avg_value')                                                       平均数.
 * @method static \Leevel\Database\Select max(string $field, string $alias = 'max_value')                                                       最大值.
 * @method static \Leevel\Database\Select min(string $field, string $alias = 'min_value')                                                       最小值.
 * @method static \Leevel\Database\Select sum(string $field, string $alias = 'sum_value')                                                       合计
 * @method static \Leevel\Database\Select one()                                                                                                 指示仅查询第一个符合条件的记录.
 * @method static \Leevel\Database\Select all()                                                                                                 指示查询所有符合条件的记录.
 * @method static \Leevel\Database\Select top(int $count = 30)                                                                                  查询几条记录.
 * @method static \Leevel\Database\Select limit(int $offset = 0, int $count = 0)                                                                limit 限制条数.
 * @method static \Leevel\Database\Select forUpdate(bool $flag = true)                                                                          排它锁 FOR UPDATE 查询.
 * @method static \Leevel\Database\Select lockShare(bool $flag = true)                                                                          共享锁 LOCK SHARE 查询.
 * @method static array getBindParams()                                                                                                         返回参数绑定.                                                                                                          返回参数绑定.
 * @method static void resetBindParams(array $bindParams = [])                                                                                  重置参数绑定.
 * @method static void setBindParamsPrefix(string $bindParamsPrefix)                                                                            设置参数绑定前缀.
 */
class MysqlPool implements IDatabase
{
    /**
     * MySQL 连接池.
     *
     * @var \Leevel\Database\Mysql\MysqlPool
     */
    protected MysqlPools $mysqlPool;

    /**
     * 构造函数.
     */
    public function __construct(MysqlPools $mysqlPool)
    {
        $this->mysqlPool = $mysqlPool;
    }

    /**
     * call.
     *
     * @return mixed
     */
    public function __call(string $method, array $args): mixed
    {
        return $this->proxy()->{$method}(...$args);
    }

    /**
     * {@inheritdoc}
     */
    public function setCache(?CacheManager $cache): void
    {
        $this->proxy()->setCache($cache);
    }

    /**
     * {@inheritdoc}
     */
    public function getCache(): ?CacheManager
    {
        return $this->proxy()->getCache();
    }

    /**
     * {@inheritdoc}
     */
    public function databaseSelect(): Select
    {
        return $this->proxy()->databaseSelect();
    }

    /**
     * {@inheritdoc}
     */
    public function pdo($master = false): mixed
    {
        return $this->proxy()->pdo($master);
    }

    /**
     * {@inheritdoc}
     */
    public function query(string $sql, array $bindParams = [], $master = false, ?string $cacheName = null, ?int $cacheExpire = null, ?string $cacheConnect = null): mixed
    {
        return $this->proxy()->query($sql, $bindParams, $master, $cacheName, $cacheExpire, $cacheConnect);
    }

    /**
     * {@inheritdoc}
     */
    public function procedure(string $sql, array $bindParams = [], $master = false, ?string $cacheName = null, ?int $cacheExpire = null, ?string $cacheConnect = null): array
    {
        return $this->proxy()->procedure($sql, $bindParams, $master, $cacheName, $cacheExpire, $cacheConnect);
    }

    /**
     * {@inheritdoc}
     */
    public function execute(string $sql, array $bindParams = [])
    {
        return $this->proxy()->execute($sql, $bindParams);
    }

    /**
     * {@inheritdoc}
     */
    public function cursor(string $sql, array $bindParams = [], $master = false): Generator
    {
        return $this->proxy()->cursor($sql, $bindParams, $master);
    }

    /**
     * {@inheritdoc}
     */
    public function prepare(string $sql, array $bindParams = [], $master = false): PDOStatement
    {
        return $this->proxy()->prepare($sql, $bindParams, $master);
    }

    /**
     * {@inheritdoc}
     */
    public function transaction(Closure $action): mixed
    {
        return $this->proxy()->transaction($action);
    }

    /**
     * {@inheritdoc}
     */
    public function beginTransaction(): void
    {
        $this->proxy()->beginTransaction();
    }

    /**
     * {@inheritdoc}
     */
    public function inTransaction(): bool
    {
        return $this->proxy()->inTransaction();
    }

    /**
     * {@inheritdoc}
     */
    public function commit(): void
    {
        $this->proxy()->commit();
    }

    /**
     * {@inheritdoc}
     */
    public function rollBack(): void
    {
        $this->proxy()->rollBack();
    }

    /**
     * {@inheritdoc}
     */
    public function setSavepoints(bool $savepoints): void
    {
        $this->proxy()->setSavepoints($savepoints);
    }

    /**
     * {@inheritdoc}
     */
    public function hasSavepoints(): bool
    {
        return $this->proxy()->hasSavepoints();
    }

    /**
     * {@inheritdoc}
     */
    public function lastInsertId(?string $name = null): string
    {
        return $this->proxy()->lastInsertId($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getLastSql(): ?string
    {
        return $this->proxy()->getLastSql();
    }

    /**
     * {@inheritdoc}
     */
    public function numRows(): int
    {
        return $this->proxy()->numRows();
    }

    /**
     * {@inheritdoc}
     */
    public function close(): void
    {
        $this->proxy()->close();
    }

    /**
     * {@inheritdoc}
     */
    public function freePDOStatement(): void
    {
        $this->proxy()->freePDOStatement();
    }

    /**
     * {@inheritdoc}
     */
    public function closeConnects(): void
    {
        $this->proxy()->closeConnects();
    }

    /**
     * {@inheritdoc}
     */
    public function release(): void
    {
        $this->proxy()->release();
    }

    /**
     * {@inheritdoc}
     */
    public static function getRawSql(string $sql, array $bindParams): string
    {
        return Database::getRawSql($sql, $bindParams);
    }

    /**
     * {@inheritdoc}
     */
    public function parseDsn(array $option): string
    {
        return $this->proxy()->parseDsn($option);
    }

    /**
     * {@inheritdoc}
     */
    public function getTableNames(string $dbName, $master = false): array
    {
        return $this->proxy()->getTableNames($dbName, $master);
    }

    /**
     * {@inheritdoc}
     */
    public function getTableColumns(string $tableName, $master = false): array
    {
        return $this->proxy()->getTableColumns($tableName, $master);
    }

    /**
     * {@inheritdoc}
     */
    public function identifierColumn(mixed $name): string
    {
        return $this->proxy()->identifierColumn($name);
    }

    /**
     * {@inheritdoc}
     */
    public function limitCount(?int $limitCount = null, ?int $limitOffset = null): string
    {
        return $this->proxy()->limitCount($limitCount, $limitOffset);
    }

    /**
     * 代理.
     *
     * @return \Leevel\Database\IDatabase
     */
    protected function proxy(): IDatabase
    {
        /** @var \Leevel\Database\IDatabase $mysql */
        $mysql = $this->mysqlPool->borrowConnection();

        return $mysql;
    }
}
