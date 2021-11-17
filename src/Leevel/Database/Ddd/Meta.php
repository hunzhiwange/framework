<?php

declare(strict_types=1);

namespace Leevel\Database\Ddd;

use Closure;
use InvalidArgumentException;
use Leevel\Database\IDatabase;
use Leevel\Database\Manager as DatabaseManager;
use Leevel\Database\Select as DatabaseSelect;

/**
 * 数据库元对象
 */
class Meta
{
    /**
     * 数据库管理器.
     */
    protected static ?DatabaseManager $resolvedDatabase = null;

    /**
     * 数据库管理器的解析器.
     */
    protected static ?Closure $databaseResolver = null;

    /**
     * 元对象实例.
     */
    protected static array $instances = [];

    /**
     * 元对象表.
     */
    protected string $table;

    /**
     * 数据库连接.
     */
    protected IDatabase $databaseConnect;

    /**
     * 构造函数.
     *
     * - 禁止直接访问构造函数，只能通过 instance 生成对象
     */
    protected function __construct(string $table)
    {
        $this->table = $table;
    }

    /**
     * 返回数据库元对象.
     */
    public static function instance(string $table): self
    {
        if (!isset(static::$instances[$table])) {
            return static::$instances[$table] = new static($table);
        }

        return static::$instances[$table];
    }

    /**
     * 返回数据库管理对象.
     *
     * @throws \InvalidArgumentException
     */
    public static function resolvedDatabase(): DatabaseManager
    {
        if (static::$resolvedDatabase) {
            return static::$resolvedDatabase;
        }

        if (!static::$databaseResolver &&
            static::lazyloadPlaceholder() && !static::$databaseResolver) {
            $e = 'Database resolver was not set.';

            throw new InvalidArgumentException($e);
        }

        $databaseResolver = static::$databaseResolver;

        return static::$resolvedDatabase = $databaseResolver();
    }

    /**
     * 设置数据库管理对象.
     */
    public static function setDatabaseResolver(?Closure $databaseResolver = null): void
    {
        static::$databaseResolver = $databaseResolver;
        if (null === $databaseResolver) {
            static::$resolvedDatabase = null;
        }
    }

    /**
     * 设置数据库元对象连接.
     */
    public function setDatabaseConnect(?string $databaseConnect = null): self
    {
        $this->databaseConnect = self::resolvedDatabase()->connect($databaseConnect);

        return $this;
    }

    /**
     * 插入数据 insert (支持原生 SQL).
     */
    public function insert(array|string $data, array $bind = [], bool|array $replace = false): ?int
    {
        return $this->select()->insert($data, $bind, $replace);
    }

    /**
     * 更新数据并返回影响行数.
     */
    public function update(array $condition, array $saveData): int
    {
        return $this->select()
            ->where($condition)
            ->limit(1)
            ->update($saveData);
    }

    /**
     * 删除数据并返回影响行数.
     */
    public function delete(array $condition): int
    {
        return $this->select()
            ->where($condition)
            ->limit(1)
            ->delete();
    }

    /**
     * 返回查询.
     */
    public function select(): DatabaseSelect
    {
        return $this->databaseConnect->table($this->table);
    }

    /**
     * 延迟载入占位符.
     */
    protected static function lazyloadPlaceholder(): bool
    {
        return Lazyload::placeholder();
    }
}
