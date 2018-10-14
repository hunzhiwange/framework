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

use Leevel\Database\Manager as DatabaseManager;

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
     * @var array
     */
    protected static $databaseManager;

    /**
     * meta 对象实例.
     *
     * @var array
     */
    protected static $instances = [];

    /**
     * 数据库仓储.
     *
     * @var \Leevel\Database\IConnect
     */
    protected $connects;

    /**
     * 元对象表.
     *
     * @var string
     */
    protected $table;

    /**
     * 表连接.
     *
     * @var mixed
     */
    protected $tableConnect;

    /**
     * 构造函数
     * 禁止直接访问构造函数，只能通过 instance 生成对象
     *
     * @param string $strTabe
     * @param mixed  $tableConnect
     * @param mixed  $table
     */
    protected function __construct($table, $tableConnect = null)
    {
        $this->table = $table;
        $this->tableConnect = $tableConnect;

        $this->initialization($table);
    }

    /**
     * 返回数据库元对象
     *
     * @param string $table
     * @param mixed  $tableConnect
     *
     * @return $this
     */
    public static function instance($table, $tableConnect = null)
    {
        $unique = static::getUnique($table, $tableConnect);

        if (!isset(static::$instances[$unique])) {
            return static::$instances[$unique] = new static($table, $tableConnect);
        }

        return static::$instances[$unique];
    }

    /**
     * 设置数据库管理对象
     *
     * @param null|\Leevel\Database\Manager $databaseManager
     */
    public static function setDatabaseManager(DatabaseManager $databaseManager = null)
    {
        static::$databaseManager = $databaseManager;
    }

    /**
     * 新增并返回数据.
     *
     * @param array $saveData
     *
     * @return array
     */
    public function insert(array $saveData)
    {
        return $this->connect->
        table($this->table)->

        insert($saveData);
    }

    /**
     * 更新并返回数据.
     *
     * @param array $condition
     * @param array $saveData
     *
     * @return int
     */
    public function update(array $condition, array $saveData)
    {
        return $this->connect->
        table($this->table)->

        where($condition)->

        update($saveData);
    }

    /**
     * 返回数据库仓储.
     *
     * @return \Leevel\Database\IDatabase
     */
    public function getConnect()
    {
        return $this->connect;
    }

    /**
     * 返回查询.
     *
     * @var \Leevel\Database\IConnect
     */
    public function getSelect()
    {
        return $this->connect->table($this->table);
    }

    /**
     * 初始化元对象
     *
     * @param string $table
     */
    protected function initialization($table)
    {
        if (!static::$databaseManager) {
        }

        $this->connect = static::$databaseManager->connect($this->tableConnect);
    }

    /**
     * 取得唯一值
     *
     * @param string $table
     * @param mixed  $tableConnect
     * @param mixed  $table
     *
     * @return string
     */
    protected static function getUnique($table, $tableConnect = null)
    {
        return $table.'.'.md5(serialize($tableConnect));
    }
}
