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

namespace Tests\Database;

use Leevel\Database\Database;
use Tests\Database\Query\Query;
use Tests\TestCase;

/**
 * database test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.10.10
 *
 * @version 1.0
 */
class DatabaseTest extends TestCase
{
    use Query;

    protected function setUp()
    {
        $this->truncate('guest_book');
    }

    protected function tearDown()
    {
        $this->setUp();
    }

    public function testBaseUse()
    {
        $connect = $this->createConnectTest();

        $database = new Database($connect);

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        $this->assertSame('1', $database->
        table('guest_book')->
        insert($data));

        $result = $database->table('guest_book', 'name,content')->

        where('id', 1)->

        findOne();

        $this->assertSame('tom', $result->name);
        $this->assertSame('I love movie.', $result->content);

        $this->truncate('guest_book');
    }
}
