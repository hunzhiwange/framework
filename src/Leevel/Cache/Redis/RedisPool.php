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

namespace Leevel\Cache\Redis;

use Leevel\Protocol\Pool\IConnection;
use Leevel\Protocol\Pool\Pool;

/**
 * Redis 连接池.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.07.07
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
class RedisPool extends Pool
{
    /**
     * Redis 连接.
     *
     * @var \Leevel\Protocol\Pool\IConnection
     */
    protected $connection;

    /**
     * 构造函数.
     *
     * @param \Leevel\Protocol\Pool\IConnection $connection
     * @param array                             $option
     */
    public function __construct(IConnection $connection, array $option = [])
    {
        parent::__construct($option);

        $this->connection = $connection;
    }

    /**
     * 创建连接.
     *
     * @return \Leevel\Protocol\Pool\IConnection
     */
    protected function createConnection(): IConnection
    {
        return $this->connection;
    }
}
