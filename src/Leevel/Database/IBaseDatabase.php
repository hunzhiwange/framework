<?php

declare(strict_types=1);

namespace Leevel\Database;

use Leevel\Cache\ICache;
use Leevel\Di\IContainer;
use Leevel\Server\Pool\IConnection;

/**
 * 数据库接口.
 */
interface IBaseDatabase extends IConnection
{
    /**
     * 断线重连尝试次数.
     */
    public const int RECONNECT_MAX = 2;

    /**
     * SQL 日志事件.
     */
    public const string SQL_EVENT = 'database.sql';

    /**
     * 获取IOC容器.
     */
    public function getContainer(): IContainer;

    /**
     * 设置缓存.
     */
    public function setCache(?ICache $cache): void;

    /**
     * 获取缓存.
     */
    public function getCache(): ?ICache;

    /**
     * 返回查询对象.
     */
    public function databaseSelect(): ISelect;

    /**
     * 返回 PDO 查询连接.
     */
    public function getPdo(bool $master = false): \PDO;

    /**
     * 查询数据记录.
     */
    public function query(string $sql, array $bindParams = [], bool $master = false, ?string $cacheName = null, ?int $cacheExpire = null, ?ICache $cache = null): mixed; /** @codeCoverageIgnore */

    /**
     * 查询存储过程数据记录.
     */
    public function procedure(string $sql, array $bindParams = [], bool $master = false, ?string $cacheName = null, ?int $cacheExpire = null, ?ICache $cache = null): array; /** @codeCoverageIgnore */

    /**
     * 执行 SQL 语句.
     */
    public function execute(string $sql, array $bindParams = [], bool $master = false, bool $insert = false): int|string; /** @codeCoverageIgnore */

    /**
     * 游标查询.
     */
    public function cursor(string $sql, array $bindParams = [], bool $master = false): \Generator; /** @codeCoverageIgnore */

    /**
     * SQL 预处理.
     *
     * - 记录 SQL 日志
     * - 支持重连
     */
    public function prepare(string $sql, array $bindParams = [], bool $master = false): \PDOStatement; /** @codeCoverageIgnore */

    /**
     * 执行数据库事务.
     */
    public function transaction(\Closure $action): mixed;

    /**
     * 启动事务.
     */
    public function beginTransaction(): void;

    /**
     * 检查是否处于事务中.
     */
    public function inTransaction(): bool;

    /**
     * 用于非自动提交状态下面的查询提交.
     */
    public function commit(): void;

    /**
     * 事务回滚.
     */
    public function rollBack(): void;

    /**
     * 设置是否启用部分事务.
     */
    public function setSavepoints(bool $savepoints): void;

    /**
     * 获取是否启用部分事务.
     */
    public function hasSavepoints(): bool;

    /**
     * 获取最近一次查询的 SQL 语句.
     */
    public function getLastSql(): string;

    /**
     * 设置最近一次真实查询的 SQL 语句.
     */
    public function setRealLastSql(array $realLastSql): void;

    /**
     * 获取最近一次真实查询的 SQL 语句.
     */
    public function getRealLastSql(): array;

    /**
     * 关闭数据库.
     */
    public function close(): void;

    /**
     * 从 PDO 预处理语句中获取原始 SQL 查询字符串.
     *
     * - This method borrows heavily from the pdo-debug package and is part of the pdo-debug package.
     *
     * @see https://github.com/panique/pdo-debug/blob/master/pdo-debug.php
     * @see https://stackoverflow.com/questions/210564/getting-raw-sql-query-string-from-pdo-prepared-statements
     * @see http://php.net/manual/en/pdo.constants.php
     */
    public static function getRawSql(string $sql, array $bindParams): string;

    /**
     * DSN 解析.
     */
    public function parseDsn(array $config): string;

    /**
     * 取得数据库表名列表.
     */
    public function getTableNames(string $dbName, bool $master = false): array;

    /**
     * 取得数据库表字段信息.
     */
    public function getTableColumns(string $tableName, bool $master = false): array;

    /**
     * 取得数据库表唯一索引信息.
     */
    public function getUniqueIndex(string $tableName, bool $master = false): array;

    /**
     * SQL 字段格式化.
     */
    public function identifierColumn(string $name): string;

    /**
     * 分析查询条数.
     */
    public function limitCount(?int $limitCount = null, ?int $limitOffset = null): string;
}
