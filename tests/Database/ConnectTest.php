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

use Exception;
use PDO;
use Tests\Database\Query\Query;
use Tests\TestCase;
use Throwable;

/**
 * connect test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.10.06
 *
 * @version 1.0
 */
class ConnectTest extends TestCase
{
    use Query;

    protected function setUp()
    {
        $this->truncate('guestbook');
    }

    protected function tearDown()
    {
        $this->setUp();
    }

    public function testBaseUse()
    {
        $connect = $this->createConnectTest();

        $sql = <<<'eot'
[
    "INSERT INTO `guestbook` (`guestbook`.`name`,`guestbook`.`content`) VALUES (:name,:content)",
    {
        "name": [
            "小鸭子",
            2
        ],
        "content": [
            "吃饭饭",
            2
        ]
    }
]
eot;

        $data = ['name' => '小鸭子', 'content' => '吃饭饭'];

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->sql()->

                table('guestbook')->

                insert($data)
            )
        );

        // 写入数据
        $this->assertSame('1', $connect->
        table('guestbook')->
        insert($data));

        $this->assertSame(1, $connect->table('guestbook')->findCount());

        $insertData = $connect->table('guestbook')->where('id', 1)->findOne();

        $this->assertSame('1', $insertData->id);
        $this->assertSame('小鸭子', $insertData->name);
        $this->assertSame('吃饭饭', $insertData->content);
        $this->assertContains(date('Y-m-d'), $insertData->create_at);

        $this->truncate('guestbook');
    }

    public function testQuery()
    {
        $connect = $this->createConnectTest();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        $this->assertSame('1', $connect->
        table('guestbook')->
        insert($data));

        $insertData = $connect->query('select * from guestbook where id=?', [1]);
        $insertData = (array) $insertData[0];

        $this->assertSame('1', $insertData['id']);
        $this->assertSame('tom', $insertData['name']);
        $this->assertSame('I love movie.', $insertData['content']);
        $this->assertContains(date('Y-m-d'), $insertData['create_at']);

        $this->truncate('guestbook');
    }

    public function testExecute()
    {
        $connect = $this->createConnectTest();

        $this->assertSame('1', $connect->execute('insert into guestbook (name, content) values (?, ?)', ['小鸭子', '喜欢游泳']));
        $insertData = $connect->query('select * from guestbook where id=?', [1]);
        $insertData = (array) $insertData[0];

        $this->assertSame('1', $insertData['id']);
        $this->assertSame('小鸭子', $insertData['name']);
        $this->assertSame('喜欢游泳', $insertData['content']);
        $this->assertContains(date('Y-m-d'), $insertData['create_at']);

        $this->truncate('guestbook');
    }

    public function testQueryOnlyAllowedSelect()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The query method only allows select and procedure SQL statements.'
        );

        $connect = $this->createConnectTest();

        $connect->query('insert into guestbook (name, content) values (?, ?)', ['小鸭子', '喜欢游泳']);
    }

    public function testExecuteNotAllowedSelect()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The query method not allows select and procedure SQL statements.'
        );

        $connect = $this->createConnectTest();

        $connect->execute('select * from guestbook where id=?', [1]);
    }

    public function testSelect()
    {
        $connect = $this->createConnectTest();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        $this->assertSame('1', $connect->
        table('guestbook')->
        insert($data));

        $insertData = $connect->select('select * from guestbook where id = ?', [1]);
        $insertData = (array) $insertData[0];

        $this->assertSame('1', $insertData['id']);
        $this->assertSame('tom', $insertData['name']);
        $this->assertSame('I love movie.', $insertData['content']);
        $this->assertContains(date('Y-m-d'), $insertData['create_at']);

        $this->truncate('guestbook');
    }

    public function testSelectWithBind()
    {
        $connect = $this->createConnectTest();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        $this->assertSame('1', $connect->
        table('guestbook')->
        insert($data));

        $insertData = $connect->select('select * from guestbook where id = :id', ['id' => 1]);
        $insertData = (array) $insertData[0];

        $this->assertSame('1', $insertData['id']);
        $this->assertSame('tom', $insertData['name']);
        $this->assertSame('I love movie.', $insertData['content']);
        $this->assertContains(date('Y-m-d'), $insertData['create_at']);

        $this->truncate('guestbook');
    }

    public function testInsert()
    {
        $connect = $this->createConnectTest();

        $this->assertSame('1', $connect->insert('insert into guestbook (name, content) values (?, ?)', ['tom', 'I love movie.']));

        $insertData = $connect->select('select * from guestbook where id = :id', ['id' => 1]);
        $insertData = (array) $insertData[0];

        $this->assertSame('1', $insertData['id']);
        $this->assertSame('tom', $insertData['name']);
        $this->assertSame('I love movie.', $insertData['content']);
        $this->assertContains(date('Y-m-d'), $insertData['create_at']);

        $this->truncate('guestbook');
    }

    public function testUpdate()
    {
        $connect = $this->createConnectTest();

        $this->assertSame('1', $connect->insert('insert into guestbook (name, content) values (?, ?)', ['tom', 'I love movie.']));

        $insertData = $connect->select('select * from guestbook where id = :id', ['id' => 1]);
        $insertData = (array) $insertData[0];

        $this->assertSame('1', $insertData['id']);
        $this->assertSame('tom', $insertData['name']);
        $this->assertSame('I love movie.', $insertData['content']);
        $this->assertContains(date('Y-m-d'), $insertData['create_at']);

        $this->assertSame(1, $connect->update('update guestbook set name = "小牛" where id = ?', [1]));

        $insertData = $connect->select('select * from guestbook where id = :id', ['id' => 1]);
        $insertData = (array) $insertData[0];

        $this->assertSame('1', $insertData['id']);
        $this->assertSame('小牛', $insertData['name']);
        $this->assertSame('I love movie.', $insertData['content']);
        $this->assertContains(date('Y-m-d'), $insertData['create_at']);

        $this->truncate('guestbook');
    }

    public function testDelete()
    {
        $connect = $this->createConnectTest();

        $this->assertSame('1', $connect->insert('insert into guestbook (name, content) values (?, ?)', ['tom', 'I love movie.']));

        $insertData = $connect->select('select * from guestbook where id = :id', ['id' => 1]);
        $insertData = (array) $insertData[0];

        $this->assertSame('1', $insertData['id']);
        $this->assertSame('tom', $insertData['name']);
        $this->assertSame('I love movie.', $insertData['content']);
        $this->assertContains(date('Y-m-d'), $insertData['create_at']);

        $this->assertSame(1, $connect->delete('delete from guestbook where id = ?', [1]));

        $this->assertSame(0, $connect->table('guestbook')->findCount());

        $this->truncate('guestbook');
    }

    public function testTransaction()
    {
        $connect = $this->createConnectTest();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 1; $n++) {
            $connect->

            table('guestbook')->

            insert($data);
        }

        $this->assertSame(2, $connect->table('guestbook')->findCount());

        $connect->transaction(function ($connect) {
            $connect->table('guestbook')->where('id', 1)->delete();

            $this->assertSame(1, $connect->table('guestbook')->findCount());

            $connect->table('guestbook')->where('id', 2)->delete();

            $this->assertSame(0, $connect->table('guestbook')->findCount());
        });

        $this->assertSame(0, $connect->table('guestbook')->findCount());

        $this->truncate('guestbook');
    }

    public function testTransactionRollback()
    {
        $connect = $this->createConnectTest();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 1; $n++) {
            $connect->

            table('guestbook')->

            insert($data);
        }

        $this->assertSame(2, $connect->table('guestbook')->findCount());

        $this->assertFalse($connect->inTransaction());

        try {
            $connect->transaction(function ($connect) {
                $connect->table('guestbook')->where('id', 1)->delete();

                $this->assertSame(1, $connect->table('guestbook')->findCount());

                $this->assertTrue($connect->inTransaction());

                throw new Exception('Will rollback');
                $connect->table('guestbook')->where('id', 2)->delete();
            });
        } catch (Throwable $e) {
            $this->assertSame('Will rollback', $e->getMessage());
        }

        $this->assertFalse($connect->inTransaction());

        $this->assertSame(2, $connect->table('guestbook')->findCount());

        $this->truncate('guestbook');
    }

    public function testTransactionByCustom()
    {
        $connect = $this->createConnectTest();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 1; $n++) {
            $connect->

            table('guestbook')->

            insert($data);
        }

        $this->assertSame(2, $connect->table('guestbook')->findCount());

        $connect->beginTransaction();

        $connect->table('guestbook')->where('id', 1)->delete();

        $this->assertSame(1, $connect->table('guestbook')->findCount());

        $connect->table('guestbook')->where('id', 2)->delete();

        $this->assertSame(0, $connect->table('guestbook')->findCount());

        $connect->commit();

        $this->assertSame(0, $connect->table('guestbook')->findCount());

        $this->truncate('guestbook');
    }

    public function testTransactionRollbackByCustom()
    {
        $connect = $this->createConnectTest();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 1; $n++) {
            $connect->

            table('guestbook')->

            insert($data);
        }

        $this->assertSame(2, $connect->table('guestbook')->findCount());

        $this->assertFalse($connect->inTransaction());

        try {
            $connect->beginTransaction();

            $connect->table('guestbook')->where('id', 1)->delete();

            $this->assertSame(1, $connect->table('guestbook')->findCount());

            $this->assertTrue($connect->inTransaction());

            throw new Exception('Will rollback');
            $connect->table('guestbook')->where('id', 2)->delete();

            $connect->commit();
        } catch (Throwable $e) {
            $this->assertSame('Will rollback', $e->getMessage());

            $connect->rollBack();
        }

        $this->assertFalse($connect->inTransaction());

        $this->assertSame(2, $connect->table('guestbook')->findCount());

        $this->truncate('guestbook');
    }

    public function testCallProcedure()
    {
        $connect = $this->createConnectTest();

        $sqlProcedure = <<<'eot'
DROP PROCEDURE IF EXISTS `test_procedure`;
CREATE PROCEDURE `test_procedure`(IN min INT)
    BEGIN
    SELECT `name` FROM `guestbook` WHERE id > min;
    SELECT `content` FROM `guestbook` WHERE id > min+1;
    END;
eot;

        $connect->execute($sqlProcedure);

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 1; $n++) {
            $connect->

            table('guestbook')->

            insert($data);
        }

        $result = $connect->query('CALL test_procedure(0)');

        $sql = <<<'eot'
[
    [
        {
            "name": "tom"
        },
        {
            "name": "tom"
        }
    ],
    [
        {
            "content": "I love movie."
        }
    ]
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $result
            )
        );

        $connect->execute('DROP PROCEDURE IF EXISTS `test_procedure`');
        $this->truncate('guestbook');
    }

    public function testPdo()
    {
        $connect = $this->createConnectTest();

        $this->assertNull($connect->pdo(0));
        $this->assertInstanceof(PDO::class, $connect->pdo(true));
        $this->assertInstanceof(PDO::class, $connect->pdo(0));
        $this->assertNull($connect->pdo(5));

        $connect->closeDatabase();
    }

    public function testQueryException()
    {
        $this->expectException(\PDOException::class);
        $this->expectExceptionMessage(
            '(1146)Table \'test.db_not_found\' doesn\'t exist'
        );

        $connect = $this->createConnectTest();

        $connect->query('SELECT * FROM db_not_found where id = 1;');
    }

    public function testBeginTransactionWithCreateSavepoint()
    {
        $connect = $this->createConnectTest();

        $connect->setSavepoints(true);

        $connect->beginTransaction();

        $connect->

        table('guestbook')->

        insert(['name' => 'tom']); // `tom` will not rollBack

        $connect->beginTransaction();
        $this->assertSame('SAVEPOINT trans2', $connect->lastSql()[0]);

        $connect->

        table('guestbook')->

        insert(['name' => 'jerry']);

        $connect->rollBack();
        $this->assertSame('ROLLBACK TO SAVEPOINT trans2', $connect->lastSql()[0]);
        $connect->commit();

        $book = $connect->table('guestbook')->where('id', 1)->findOne();

        $this->assertSame(1, $connect->table('guestbook')->findCount());
        $this->assertSame('tom', $book->name);

        $this->truncate('guestbook');
    }

    public function testCommitWithoutActiveTransaction()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'There was no active transaction.'
        );

        $connect = $this->createConnectTest();

        $connect->commit();
    }

    public function testCommitButIsRollbackOnly()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Commit failed for rollback only.'
        );

        $connect = $this->createConnectTest();

        $connect->beginTransaction();
        $connect->beginTransaction();
        $connect->rollBack();
        $connect->commit();
    }

    public function testCommitWithReleaseSavepoint()
    {
        $connect = $this->createConnectTest();

        $connect->setSavepoints(true);

        $connect->beginTransaction();

        $connect->

        table('guestbook')->

        insert(['name' => 'tom']);

        $connect->beginTransaction();
        $this->assertSame('SAVEPOINT trans2', $connect->lastSql()[0]);

        $connect->

        table('guestbook')->

        insert(['name' => 'jerry']);

        $connect->commit();
        $this->assertSame('RELEASE SAVEPOINT trans2', $connect->lastSql()[0]);
        $connect->commit();

        $book = $connect->table('guestbook')->where('id', 1)->findOne();
        $book2 = $connect->table('guestbook')->where('id', 2)->findOne();

        $this->assertSame(2, $connect->table('guestbook')->findCount());
        $this->assertSame('tom', $book->name);
        $this->assertSame('jerry', $book2->name);

        $this->truncate('guestbook');
    }

    public function testRollBackWithoutActiveTransaction()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'There was no active transaction.'
        );

        $connect = $this->createConnectTest();

        $connect->rollBack();
    }

    public function testNumRows()
    {
        $connect = $this->createConnectTest();

        $this->assertSame(0, $connect->numRows());

        $connect->

        table('guestbook')->

        insert(['name' => 'jerry']);

        $this->assertSame(1, $connect->numRows());

        $connect->

        table('guestbook')->

        where('id', 1)->

        update(['name' => 'jerry']);

        $this->assertSame(0, $connect->numRows());

        $connect->

        table('guestbook')->

        where('id', 1)->

        update(['name' => 'tom']);

        $this->assertSame(1, $connect->numRows());

        $this->truncate('guestbook');
    }
}
