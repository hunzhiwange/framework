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

namespace Tests\Database;

use Tests\Database;
use Tests\TestCase;

/**
 * 数据库单元测试基类.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.09.29
 *
 * @version 1.0
 */
abstract class DatabaseTestCase extends TestCase
{
    use Database;

    protected function setUp()
    {
        $this->clearDatabaseTable();

        $this->metaWithDatabase();
    }

    protected function tearDown()
    {
        $this->clearDatabaseTable();

        $this->metaWithoutDatabase();

        $this->freeDatabaseConnects();
    }

    protected function clearDatabaseTable()
    {
        if (!method_exists($this, 'getDatabaseTable')) {
            return;
        }

        $this->truncateDatabase($this->getDatabaseTable());
    }
}
