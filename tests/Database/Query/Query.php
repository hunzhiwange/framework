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

namespace Tests\Database\Query;

use Leevel\Cache\ICache;
use Leevel\Database\Mysql;
use Leevel\Log\ILog;

/**
 * query trait.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.10
 *
 * @version 1.0
 */
trait Query
{
    protected function createConnect(array $option = [])
    {
        $log = $this->createMock(ILog::class);

        $cache = $this->createMock(ICache::class);

        return new Mysql($log, $cache, $option);
    }

    protected function createConnectTest()
    {
        return $this->createConnect([
            'driver'             => 'mysql',
            'readwrite_separate' => false,
            'distributed'        => false,
            'master'             => [
                'host'     => '127.0.0.1',
                'port'     => '3306',
                'name'     => 'test',
                'user'     => 'root',
                'password' => '123456',
                'charset'  => 'utf8',
                'options'  => [
                    12 => false,
                ],
            ],
            'slave' => [
            ],
            'fetch' => 5,
            'log'   => true,
        ]);
    }

    protected function truncate(string $table)
    {
        $connect = $this->createConnectTest();

        $sql = <<<'eot'
[
    "TRUNCATE TABLE `%s`",
    []
]
eot;

        $this->assertSame(
            sprintf($sql, $table),
            $this->varJson(
                $connect->sql()->

                table($table)->

                truncate()
            )
        );

        // 清理表数据
        $connect->

        table($table)->

        truncate();
    }
}
