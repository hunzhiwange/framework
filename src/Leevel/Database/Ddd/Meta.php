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

namespace Leevel\Database\Ddd;

use Closure;
use InvalidArgumentException;
use Leevel\Database\IDatabase;
use Leevel\Database\Manager as DatabaseManager;
use Leevel\Database\Select as DatabaseSelect;

/**
 * 数据库元对象
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.04.27
 *
 * @version 1.0
 */
class Meta implements IMeta
{
    /**
     * Database 管理.
     *
     * @var \Leevel\Database\Manager
     */
    protected static ?DatabaseManager $resolveDatabase = null;

    /**
     * Database 管理.
     *
     * @var \Closure
     */
    protected static ?Closure $databaseResolver = null;

    /**
     * meta 对象实例.
     *
     * @var \Leevel\Database\Ddd\IMeta[]
     */
    protected static array $instances = [];

    /**
     * 元对象表.
     *
     * @var string
     */
    protected string $table;

    /**
     * 数据库连接.
     *
     * @var \Leevel\Database\IDatabase
     */
    protected IDatabase $databaseConnect;

    /**
     * 构造函数
     * 禁止直接访问构造函数，只能通过 instance 生成对象
     */
    protected function __construct(string $table)
    {
        $this->table = $table;
    }

    /**
     * 返回数据库元对象
     *
     * @return \Leevel\Database\Ddd\IMeta
     */
    public static function instance(string $table): IMeta
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
    public static function resolveDatabase(): DatabaseManager
    {
        if (static::$resolveDatabase) {
            return static::$resolveDatabase;
        }

        if (!static::$databaseResolver &&
            static::lazyloadPlaceholder() && !static::$databaseResolver) {
            $e = 'Database resolver was not set.';

            throw new InvalidArgumentException($e);
        }

        $databaseResolver = static::$databaseResolver;

        return static::$resolveDatabase = $databaseResolver();
    }

    /**
     * 设置数据库管理对象.
     */
    public static function setDatabaseResolver(?Closure $databaseResolver = null): void
    {
        static::$databaseResolver = $databaseResolver;

        if (null === $databaseResolver) {
            static::$resolveDatabase = null;
        }
    }

    /**
     * 设置数据库元对象连接.
     *
     * @param null|mixed $databaseConnect
     *
     * @return \Leevel\Database\Ddd\IMeta
     */
    public function setDatabaseConnect($databaseConnect = null): IMeta
    {
        $this->databaseConnect = self::resolveDatabase()->connect($databaseConnect);

        return $this;
    }

    /**
     * 新增数据并返回上一次插入 ID.
     *
     * @return mixed
     */
    public function insert(array $saveData)
    {
        return $this->select()->insert($saveData);
    }

    /**
     * 更新数据并返回影响行数.
     */
    public function update(array $condition, array $saveData): int
    {
        return $this->select()
            ->where($condition)
            ->update($saveData);
    }

    /**
     * 删除数据并返回影响行数.
     */
    public function delete(array $condition): int
    {
        return $this->select()
            ->where($condition)
            ->delete();
    }

    /**
     * 返回查询.
     *
     * @var \Leevel\Database\Select
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
