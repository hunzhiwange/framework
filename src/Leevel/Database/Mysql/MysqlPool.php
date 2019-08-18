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

namespace Leevel\Database\Mysql;

use Leevel\Database\Manager;
use Leevel\Protocol\Pool\IConnection;
use Leevel\Protocol\Pool\Pool;

/**
 * MySQL 连接池.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.07.23
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
class MysqlPool extends Pool
{
    /**
     * 数据库管理.
     *
     * @var \Leevel\Database\Manager
     */
    protected $manager;

    /**
     * MySQL 连接.
     *
     * @var string
     */
    protected $mysqlConnect;

    /**
     * 构造函数.
     *
     * @param \Leevel\Database\Manager $manager
     * @param string                   $mysqlConnect
     * @param array                    $option
     */
    public function __construct(Manager $manager, string $mysqlConnect, array $option = [])
    {
        parent::__construct($option);

        $this->manager = $manager;
        $this->mysqlConnect = $mysqlConnect;
    }

    /**
     * 创建连接.
     *
     * @return \Leevel\Protocol\Pool\IConnection
     */
    protected function createConnection(): IConnection
    {
        if ($this->manager->inTransactionConnection()) {
            return $this->manager->getTransactionConnection();
        }

        /** @var \Leevel\Protocol\Pool\IConnection $mysql */
        $mysql = $this->manager->connect($this->mysqlConnect, true);
        $mysql->setRelease(true);

        return $mysql;
    }
}
