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

namespace Leevel\Leevel\Testing;

use Leevel\Database\Facade\Db;

/**
 * 数据库助手方法.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.11.24
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
trait Database
{
    /**
     * 清理数据表.
     *
     * @param array $tables
     */
    protected function truncateDatabase(array $tables): void
    {
        if (!$tables) {
            return;
        }

        foreach ($tables as $table) {
            $sql = <<<'eot'
[
    "TRUNCATE TABLE `%s`",
    []
]
eot;
            $this->assertSame(
                sprintf($sql, $table),
                $this->varJson(
                    Db::sql()->
                    table($table)->
                    truncate()
                )
            );

            Db::table($table)->

            truncate();
        }
    }
}
