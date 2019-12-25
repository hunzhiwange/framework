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
use Leevel\Database\Manager as DatabaseManager;
use Leevel\Database\Select as DatabaseSelect;

/**
 * 数据库元对象接口.
 */
interface IMeta
{
    /**
     * 返回数据库元对象
     *
     * @return \Leevel\Database\Ddd\IMeta
     */
    public static function instance(string $table): self;

    /**
     * 返回数据库管理对象.
     *
     * @throws \InvalidArgumentException
     */
    public static function resolveDatabase(): DatabaseManager;

    /**
     * 设置数据库管理对象.
     */
    public static function setDatabaseResolver(?Closure $databaseResolver = null): void;

    /**
     * 设置数据库元对象连接.
     *
     * @param null|mixed $databaseConnect
     *
     * @return \Leevel\Database\Ddd\IMeta
     */
    public function setDatabaseConnect($databaseConnect = null): self;

    /**
     * 插入数据 insert (支持原生 sql).
     *
     * @param array|string $data
     *
     * @return null|int
     */
    public function insert($data, array $bind = [], bool $replace = false);

    /**
     * 更新数据并返回影响行数.
     */
    public function update(array $condition, array $saveData): int;

    /**
     * 删除数据并返回影响行数.
     */
    public function delete(array $condition): int;

    /**
     * 返回查询.
     *
     * @var \Leevel\Database\Select
     */
    public function select(): DatabaseSelect;
}
