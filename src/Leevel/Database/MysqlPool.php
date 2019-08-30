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

namespace Leevel\Database;

use Leevel\Database\Mysql\MysqlPool as MysqlPools;

/**
 * MySQL pool 缓存.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.07.23
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
class MysqlPool implements IDatabase
{
    use Proxy;

    /**
     * MySQL 连接池.
     *
     * @var \Leevel\Database\Mysql\MysqlPool
     */
    protected $mysqlPool;

    /**
     * 构造函数.
     *
     * @param \Leevel\Database\Mysql\MysqlPool $mysqlPool
     */
    public function __construct(MysqlPools $mysqlPool)
    {
        $this->mysqlPool = $mysqlPool;
    }

    /**
     * 代理.
     *
     * @return \Leevel\Database\IDatabase
     */
    public function proxy(): IDatabase
    {
        /** @var \Leevel\Database\IDatabase $mysql */
        $mysql = $this->mysqlPool->borrowConnection();

        return $mysql;
    }
}
