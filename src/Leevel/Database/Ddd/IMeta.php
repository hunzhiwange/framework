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

namespace Leevel\Database\Ddd;

use Closure;
use Leevel\Database\Manager as DatabaseManager;
use Leevel\Database\Select as DatabaseSelect;

/**
 * 数据库元对象接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.05
 *
 * @version 1.0
 */
interface IMeta
{
    /**
     * 返回数据库元对象
     *
     * @param string $table
     *
     * @return \Leevel\Database\Ddd\IMeta
     */
    public static function instance(string $table): self;

    /**
     * 返回数据库管理对象.
     *
     * @return \Leevel\Database\Manager
     */
    public static function resolveDatabase(): DatabaseManager;

    /**
     * 设置数据库管理对象.
     *
     * @param null|\Closure $databaseResolver
     */
    public static function setDatabaseResolver(?Closure $databaseResolver = null): void;

    /**
     * 返回数据库元对象连接.
     *
     * @param mixed $connect
     *
     * @return \Leevel\Database\Ddd\IMeta
     */
    public function setConnect($connect = null): self;

    /**
     * 新增数据并返回上一次插入 ID.
     *
     * @param array $saveData
     *
     * @return mixed
     */
    public function insert(array $saveData);

    /**
     * 更新数据并返回影响行数.
     *
     * @param array $condition
     * @param array $saveData
     *
     * @return int
     */
    public function update(array $condition, array $saveData): int;

    /**
     * 删除数据并返回影响行数.
     *
     * @param array $condition
     *
     * @return int
     */
    public function delete(array $condition): int;

    /**
     * 返回查询.
     *
     * @var \Leevel\Database\Select
     */
    public function select(): DatabaseSelect;
}
