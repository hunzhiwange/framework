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

namespace Tests\Database;

use Exception;
use Leevel\Database\IDatabase;
use Leevel\Database\Mysql;
use Leevel\Database\Select;
use PDO;
use PDOException;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\MysqlNeedReconnectMock;
use Throwable;

/**
 * @api(
 *     zh-CN:title="数据库连接",
 *     path="database/database",
 *     description="",
 * )
 */
class DatabaseTest extends TestCase
{
    /**
     * @api(
     *     title="基本使用",
     *     description="",
     *     note="",
     * )
     */
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnect();

        $database = $connect;

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        $this->assertSame(
            1,
            $database
                ->table('guest_book')
                ->insert($data),
        );

        $result = $database
            ->table('guest_book', 'name,content')
            ->where('id', 1)
            ->findOne();

        $this->assertSame('tom', $result->name);
        $this->assertSame('I love movie.', $result->content);
    }

    public function testBaseUse2(): void
    {
        $connect = $this->createDatabaseConnect();

        $sql = <<<'eot'
            [
                "INSERT INTO `guest_book` (`guest_book`.`name`,`guest_book`.`content`) VALUES (:pdonamedparameter_name,:pdonamedparameter_content)",
                {
                    "pdonamedparameter_name": [
                        "小鸭子"
                    ],
                    "pdonamedparameter_content": [
                        "吃饭饭"
                    ]
                },
                false
            ]
            eot;

        $data = ['name' => '小鸭子', 'content' => '吃饭饭'];

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->sql()
                    ->table('guest_book')
                    ->insert($data)
            )
        );

        $this->truncateDatabase(['guest_book']);

        // 写入数据
        $this->assertSame(
            1,
            $connect
                ->table('guest_book')
                ->insert($data),
        );

        $this->assertSame(1, $connect->table('guest_book')->findCount());

        $insertData = $connect
            ->table('guest_book')
            ->where('id', 1)
            ->findOne();

        $this->assertSame(1, $insertData->id);
        $this->assertSame('小鸭子', $insertData->name);
        $this->assertSame('吃饭饭', $insertData->content);
        $this->assertStringContainsString(date('Y-m'), $insertData->create_at);
    }

    /**
     * @api(
     *     title="query 查询数据记录",
     *     description="",
     *     note="",
     * )
     */
    public function testQuery(): void
    {
        $connect = $this->createDatabaseConnect();
        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        $this->assertSame(
            1,
            $connect
                ->table('guest_book')
                ->insert($data),
        );

        $insertData = $connect->query('select * from guest_book where id=?', [1]);
        $insertData = (array) $insertData[0];

        $this->assertSame(1, $insertData['id']);
        $this->assertSame('tom', $insertData['name']);
        $this->assertSame('I love movie.', $insertData['content']);
        $this->assertStringContainsString(date('Y-m'), $insertData['create_at']);
    }

    public function testQueryFailed(): void
    {
        $this->expectException(\PDOException::class);
        $this->expectExceptionMessage(
            'SQLSTATE[42S22]: Column not found: 1054 Unknown column \'id_not_found\' in \'where clause\''
        );

        $connect = $this->createDatabaseConnect();
        $connect->query('select * from guest_book where id_not_found=?', [1]);
    }

    public function testQueryFailedAndNeedReconnect(): void
    {
        $this->expectException(\PDOException::class);
        $this->expectExceptionMessage(
            'SQLSTATE[42S22]: Column not found: 1054 Unknown column \'id_not_found\' in \'where clause\''
        );

        $connect = $this->createDatabaseConnect(null, MysqlNeedReconnectMock::class);
        $connect->query('select * from guest_book where id_not_found=?', [1]);
    }

    /**
     * @api(
     *     title="execute 执行 SQL 语句",
     *     description="",
     *     note="",
     * )
     */
    public function testExecute(): void
    {
        $connect = $this->createDatabaseConnect();

        $this->assertSame(1, $connect->execute('insert into guest_book (name, content) values (?, ?)', ['小鸭子', '喜欢游泳']));
        $insertData = $connect->query('select * from guest_book where id=?', [1]);
        $insertData = (array) $insertData[0];

        $this->assertSame(1, $insertData['id']);
        $this->assertSame('小鸭子', $insertData['name']);
        $this->assertSame('喜欢游泳', $insertData['content']);
        $this->assertStringContainsString(date('Y-m'), $insertData['create_at']);
    }

    public function testExecuteFailed(): void
    {
        $this->expectException(\PDOException::class);
        $this->expectExceptionMessage(
            'SQLSTATE[21S01]: Insert value list does not match column list: 1136 Column count doesn\'t match value count at row 1'
        );

        $connect = $this->createDatabaseConnect();
        $connect->execute('insert into guest_book (name, content) values (?, ?, ?)', ['小鸭子', '喜欢游泳']);
    }

    public function testExecuteFailedAndNeedReconnect(): void
    {
        $this->expectException(\PDOException::class);
        $this->expectExceptionMessage(
            'SQLSTATE[21S01]: Insert value list does not match column list: 1136 Column count doesn\'t match value count at row 1'
        );

        $connect = $this->createDatabaseConnect(null, MysqlNeedReconnectMock::class);
        $connect->execute('insert into guest_book (name, content) values (?, ?, ?)', ['小鸭子', '喜欢游泳']);
    }

    public function testQueryOnlyAllowedSelect(): void
    {
        $connect = $this->createDatabaseConnect();
        // 由用户自己保证使用 query,procedure 还是 execute，系统不加限制，减少底层设计复杂度
        $result = $connect->query('insert into guest_book (name, content) values (?, ?)', ['小鸭子', '喜欢游泳']);
        $this->assertSame([], $result);
    }

    public function testExecuteNotAllowedSelect(): void
    {
        $connect = $this->createDatabaseConnect();
        // 由用户自己保证使用 query,procedure 还是 execute，系统不加限制，减少底层设计复杂度
        $result = $connect->execute('select * from guest_book where id=?', [1]);
        $this->assertSame(0, $result);
    }

    /**
     * @api(
     *     title="select 原生 SQL 查询数据",
     *     description="",
     *     note="",
     * )
     */
    public function testSelect(): void
    {
        $connect = $this->createDatabaseConnect();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        $this->assertSame(
            1,
            $connect
                ->table('guest_book')
                ->insert($data),
        );

        $insertData = $connect->select('select * from guest_book where id = ?', [1]);
        $insertData = (array) $insertData[0];

        $this->assertSame(1, $insertData['id']);
        $this->assertSame('tom', $insertData['name']);
        $this->assertSame('I love movie.', $insertData['content']);
        $this->assertStringContainsString(date('Y-m'), $insertData['create_at']);
    }

    /**
     * @api(
     *     title="select 原生 SQL 查询数据支持参数绑定",
     *     description="",
     *     note="",
     * )
     */
    public function testSelectWithBind(): void
    {
        $connect = $this->createDatabaseConnect();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        $this->assertSame(
            1,
            $connect
                ->table('guest_book')
                ->insert($data),
        );

        $insertData = $connect->select('select * from guest_book where id = :id', ['id' => 1]);
        $insertData = (array) $insertData[0];

        $this->assertSame(1, $insertData['id']);
        $this->assertSame('tom', $insertData['name']);
        $this->assertSame('I love movie.', $insertData['content']);
        $this->assertStringContainsString(date('Y-m'), $insertData['create_at']);
    }

    /**
     * @api(
     *     title="insert 插入数据 insert (支持原生 SQL)",
     *     description="",
     *     note="",
     * )
     */
    public function testInsert(): void
    {
        $connect = $this->createDatabaseConnect();

        $this->assertSame(1, $connect->insert('insert into guest_book (name, content) values (?, ?)', ['tom', 'I love movie.']));

        $insertData = $connect->select('select * from guest_book where id = :id', ['id' => 1]);
        $insertData = (array) $insertData[0];

        $this->assertSame(1, $insertData['id']);
        $this->assertSame('tom', $insertData['name']);
        $this->assertSame('I love movie.', $insertData['content']);
        $this->assertStringContainsString(date('Y-m'), $insertData['create_at']);
    }

    /**
     * @api(
     *     title="update 更新数据 update (支持原生 SQL)",
     *     description="",
     *     note="",
     * )
     */
    public function testUpdate(): void
    {
        $connect = $this->createDatabaseConnect();

        $this->assertSame(1, $connect->insert('insert into guest_book (name, content) values (?, ?)', ['tom', 'I love movie.']));

        $insertData = $connect->select('select * from guest_book where id = :id', ['id' => 1]);
        $insertData = (array) $insertData[0];

        $this->assertSame(1, $insertData['id']);
        $this->assertSame('tom', $insertData['name']);
        $this->assertSame('I love movie.', $insertData['content']);
        $this->assertStringContainsString(date('Y-m'), $insertData['create_at']);

        $this->assertSame(1, $connect->update('update guest_book set name = "小牛" where id = ?', [1]));

        $insertData = $connect->select('select * from guest_book where id = :id', ['id' => 1]);
        $insertData = (array) $insertData[0];

        $this->assertSame(1, $insertData['id']);
        $this->assertSame('小牛', $insertData['name']);
        $this->assertSame('I love movie.', $insertData['content']);
        $this->assertStringContainsString(date('Y-m'), $insertData['create_at']);
    }

    /**
     * @api(
     *     title="delete 删除数据 delete (支持原生 SQL)",
     *     description="",
     *     note="",
     * )
     */
    public function testDelete(): void
    {
        $connect = $this->createDatabaseConnect();

        $this->assertSame(1, $connect->insert('insert into guest_book (name, content) values (?, ?)', ['tom', 'I love movie.']));

        $insertData = $connect->select('select * from guest_book where id = :id', ['id' => 1]);
        $insertData = (array) $insertData[0];

        $this->assertSame(1, $insertData['id']);
        $this->assertSame('tom', $insertData['name']);
        $this->assertSame('I love movie.', $insertData['content']);
        $this->assertStringContainsString(date('Y-m'), $insertData['create_at']);

        $this->assertSame(1, $connect->delete('delete from guest_book where id = ?', [1]));
        $this->assertSame(0, $connect->table('guest_book')->findCount());
    }

    /**
     * @api(
     *     title="transaction 执行数据库事务",
     *     description="",
     *     note="",
     * )
     */
    public function testTransaction(): void
    {
        $connect = $this->createDatabaseConnect();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 1; $n++) {
            $connect
                ->table('guest_book')
                ->insert($data);
        }

        $this->assertSame(2, $connect->table('guest_book')->findCount());

        $connect->transaction(function ($connect) {
            $connect
                ->table('guest_book')
                ->where('id', 1)
                ->delete();

            $this->assertSame(1, $connect->table('guest_book')->findCount());

            $connect
                ->table('guest_book')
                ->where('id', 2)
                ->delete();

            $this->assertSame(0, $connect->table('guest_book')->findCount());
        });

        $this->assertSame(0, $connect->table('guest_book')->findCount());
    }

    /**
     * @api(
     *     title="transaction 执行数据库事务回滚例子",
     *     description="",
     *     note="",
     * )
     */
    public function testTransactionRollback(): void
    {
        $connect = $this->createDatabaseConnect();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 1; $n++) {
            $connect
                ->table('guest_book')
                ->insert($data);
        }

        $this->assertSame(2, $connect->table('guest_book')->findCount());

        $this->assertFalse($connect->inTransaction());

        try {
            $connect->transaction(function ($connect) {
                $connect->table('guest_book')->where('id', 1)->delete();

                $this->assertSame(1, $connect->table('guest_book')->findCount());

                $this->assertTrue($connect->inTransaction());

                throw new Exception('Will rollback');
                $connect->table('guest_book')->where('id', 2)->delete();
            });
        } catch (Throwable $e) {
            $this->assertSame('Will rollback', $e->getMessage());
        }

        $this->assertFalse($connect->inTransaction());

        $this->assertSame(2, $connect->table('guest_book')->findCount());
    }

    /**
     * @api(
     *     title="beginTransaction.commit 启动事务和用于非自动提交状态下面的查询提交",
     *     description="",
     *     note="",
     * )
     */
    public function testTransactionByCustom(): void
    {
        $connect = $this->createDatabaseConnect();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 1; $n++) {
            $connect
                ->table('guest_book')
                ->insert($data);
        }

        $this->assertSame(2, $connect->table('guest_book')->findCount());

        $connect->beginTransaction();

        $connect->table('guest_book')->where('id', 1)->delete();

        $this->assertSame(1, $connect->table('guest_book')->findCount());

        $connect->table('guest_book')->where('id', 2)->delete();

        $this->assertSame(0, $connect->table('guest_book')->findCount());

        $connect->commit();

        $this->assertSame(0, $connect->table('guest_book')->findCount());
    }

    /**
     * @api(
     *     title="beginTransaction.rollBack 启动事务和事务回滚",
     *     description="",
     *     note="",
     * )
     */
    public function testTransactionRollbackByCustom(): void
    {
        $connect = $this->createDatabaseConnect();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 1; $n++) {
            $connect
                ->table('guest_book')
                ->insert($data);
        }

        $this->assertSame(2, $connect->table('guest_book')->findCount());

        $this->assertFalse($connect->inTransaction());

        try {
            $connect->beginTransaction();

            $connect
                ->table('guest_book')
                ->where('id', 1)
                ->delete();

            $this->assertSame(1, $connect->table('guest_book')->findCount());

            $this->assertTrue($connect->inTransaction());

            throw new Exception('Will rollback');
            $connect->table('guest_book')->where('id', 2)->delete();

            $connect->commit();
        } catch (Throwable $e) {
            $this->assertSame('Will rollback', $e->getMessage());

            $connect->rollBack();
        }

        $this->assertFalse($connect->inTransaction());

        $this->assertSame(2, $connect->table('guest_book')->findCount());
    }

    /**
     * @api(
     *     title="procedure 查询存储过程数据记录",
     *     description="",
     *     note="",
     * )
     */
    public function testCallProcedure(): void
    {
        $connect = $this->createDatabaseConnect();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 1; $n++) {
            $connect
                ->table('guest_book')
                ->insert($data);
        }

        $result = $connect->procedure('CALL test_procedure(0)');

        $data = <<<'eot'
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
            $data,
            $this->varJson(
                $result
            )
        );
    }

    /**
     * @api(
     *     title="procedure 查询存储过程数据记录支持参数绑定",
     *     description="",
     *     note="",
     * )
     */
    public function testCallProcedure2(): void
    {
        $connect = $this->createDatabaseConnect();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 1; $n++) {
            $connect
                ->table('guest_book')
                ->insert($data);
        }

        $result = $connect->procedure('CALL test_procedure2(0,:name)', [
            'name' => [null, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 200],
        ]);

        $data = <<<'eot'
            [
                [
                    {
                        "content": "I love movie."
                    }
                ],
                [
                    {
                        "_name": "tom"
                    }
                ]
            ]
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $result
            )
        );
    }

    /**
     * @api(
     *     title="查询存储过程数据支持原生方法",
     *     description="",
     *     note="",
     * )
     */
    public function testCallProcedure3(): void
    {
        $connect = $this->createDatabaseConnect();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 1; $n++) {
            $connect
                ->table('guest_book')
                ->insert($data);
        }

        $pdoStatement = $connect->pdo(true)->prepare('CALL test_procedure2(0,:name)');
        $outName = null;
        $pdoStatement->bindParam(':name', $outName, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 200);
        $pdoStatement->execute();

        $result = [];
        do {
            try {
                $result[] = $pdoStatement->fetchAll(PDO::FETCH_OBJ);
            } catch (PDOException $e) {
            }
        } while ($pdoStatement->nextRowset());

        $data = <<<'eot'
            [
                [
                    {
                        "content": "I love movie."
                    }
                ],
                [
                    {
                        "_name": "tom"
                    }
                ]
            ]
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $result
            )
        );
    }

    /**
     * @api(
     *     title="pdo 返回 PDO 查询连接",
     *     description="",
     *     note="",
     * )
     */
    public function testPdo(): void
    {
        $connect = $this->createDatabaseConnect();

        $this->assertNull($connect->pdo(IDatabase::MASTER));
        $this->assertInstanceof(PDO::class, $connect->pdo(true));
        $this->assertInstanceof(PDO::class, $connect->pdo(IDatabase::MASTER));
        $this->assertNull($connect->pdo(5));

        $connect->close();
    }

    public function testQueryException(): void
    {
        $this->expectException(\PDOException::class);
        $this->expectExceptionMessage(
            'SQLSTATE[42S02]: Base table or view not found: 1146 Table \'test.db_not_found\' doesn\'t exist'
        );

        $connect = $this->createDatabaseConnect();
        $connect->query('SELECT * FROM db_not_found where id = 1;');
    }

    /**
     * @api(
     *     title="setSavepoints 设置是否启用部分事务回滚保存点",
     *     description="",
     *     note="",
     * )
     */
    public function testBeginTransactionWithCreateSavepoint(): void
    {
        if (isset($_SERVER['TRAVIS_COMMIT'])) {
            $this->markTestSkipped('Mysql of travis-ci not support savepoint.');

            return;
        }

        $connect = $this->createDatabaseConnect();

        $connect->setSavepoints(true);
        $connect->beginTransaction();
        $connect
            ->table('guest_book')
            ->insert(['name' => 'tom']); // `tom` will not rollBack

        $connect->beginTransaction();
        $this->assertSame('SAVEPOINT trans2', $connect->getLastSql());

        $connect
            ->table('guest_book')
            ->insert(['name' => 'jerry']);

        $connect->rollBack();
        $this->assertSame('ROLLBACK TO SAVEPOINT trans2', $connect->getLastSql());
        $connect->commit();

        $book = $connect
            ->table('guest_book')
            ->where('id', 1)
            ->findOne();

        $this->assertSame(1, $connect->table('guest_book')->findCount());
        $this->assertSame('tom', $book->name);
    }

    public function testCommitWithoutActiveTransaction(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'There was no active transaction.'
        );

        $connect = $this->createDatabaseConnect();
        $connect->commit();
    }

    public function testCommitButIsRollbackOnly(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Commit failed for rollback only.'
        );

        $connect = $this->createDatabaseConnect();
        $connect->beginTransaction();
        $connect->beginTransaction();
        $connect->rollBack();
        $connect->commit();
    }

    /**
     * @api(
     *     title="setSavepoints 设置是否启用部分事务提交保存点",
     *     description="",
     *     note="",
     * )
     */
    public function testCommitWithReleaseSavepoint(): void
    {
        if (isset($_SERVER['TRAVIS_COMMIT'])) {
            $this->markTestSkipped('Mysql of travis-ci not support savepoint.');

            return;
        }

        $connect = $this->createDatabaseConnect();
        $connect->setSavepoints(true);
        $connect->beginTransaction();

        $connect
            ->table('guest_book')
            ->insert(['name' => 'tom']);

        $connect->beginTransaction();
        $this->assertSame('SAVEPOINT trans2', $connect->getLastSql());

        $connect
            ->table('guest_book')
            ->insert(['name' => 'jerry']);

        $connect->commit();
        $this->assertSame('RELEASE SAVEPOINT trans2', $connect->getLastSql());
        $connect->commit();

        $book = $connect
            ->table('guest_book')
            ->where('id', 1)
            ->findOne();
        $book2 = $connect
            ->table('guest_book')
            ->where('id', 2)
            ->findOne();

        $this->assertSame(2, $connect->table('guest_book')->findCount());
        $this->assertSame('tom', $book->name);
        $this->assertSame('jerry', $book2->name);
    }

    public function testRollBackWithoutActiveTransaction(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'There was no active transaction.'
        );

        $connect = $this->createDatabaseConnect();

        $connect->rollBack();
    }

    /**
     * @api(
     *     title="numRows 返回影响记录",
     *     description="",
     *     note="",
     * )
     */
    public function testNumRows(): void
    {
        $connect = $this->createDatabaseConnect();

        $this->assertSame(0, $connect->numRows());

        $connect
            ->table('guest_book')
            ->insert(['name' => 'jerry', 'content' => '']);

        $this->assertSame(1, $connect->numRows());

        $connect
            ->table('guest_book')
            ->where('id', 1)
            ->update(['name' => 'jerry']);

        $this->assertSame(0, $connect->numRows());

        $connect
            ->table('guest_book')
            ->where('id', 1)
            ->update(['name' => 'tom']);

        $this->assertSame(1, $connect->numRows());
    }

    /**
     * @api(
     *     title="数据库主从",
     *     description="
     * 数据库配置项 `distributed` 表示主从，如果从数据库均连接失败，则还是会走主库。
     * ",
     *     note="",
     * )
     */
    public function testReadConnectDistributed(): void
    {
        $connect = $this->createDatabaseConnectMock([
            'driver'             => 'mysql',
            'separate'           => false,
            'distributed'        => true,
            'master'             => [
                'host'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['HOST'],
                'port'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PORT'],
                'name'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['NAME'],
                'user'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['USER'],
                'password' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PASSWORD'],
                'charset'  => 'utf8',
                'options'  => [
                    PDO::ATTR_PERSISTENT        => false,
                    PDO::ATTR_CASE              => PDO::CASE_NATURAL,
                    PDO::ATTR_ORACLE_NULLS      => PDO::NULL_NATURAL,
                    PDO::ATTR_STRINGIFY_FETCHES => false,
                    PDO::ATTR_EMULATE_PREPARES  => false,
                ],
            ],
            'slave' => [
                [
                    'host'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['HOST'],
                    'port'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PORT'],
                    'name'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['NAME'],
                    'user'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['USER'],
                    'password' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PASSWORD'],
                    'charset'  => 'utf8',
                    'options'  => [
                        PDO::ATTR_PERSISTENT        => false,
                        PDO::ATTR_CASE              => PDO::CASE_NATURAL,
                        PDO::ATTR_ORACLE_NULLS      => PDO::NULL_NATURAL,
                        PDO::ATTR_STRINGIFY_FETCHES => false,
                        PDO::ATTR_EMULATE_PREPARES  => false,
                    ],
                ],
                [
                    'host'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['HOST'],
                    'port'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PORT'],
                    'name'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['NAME'],
                    'user'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['USER'],
                    'password' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PASSWORD'],
                    'charset'  => 'utf8',
                    'options'  => [
                        PDO::ATTR_PERSISTENT        => false,
                        PDO::ATTR_CASE              => PDO::CASE_NATURAL,
                        PDO::ATTR_ORACLE_NULLS      => PDO::NULL_NATURAL,
                        PDO::ATTR_STRINGIFY_FETCHES => false,
                        PDO::ATTR_EMULATE_PREPARES  => false,
                    ],
                ],
            ],
        ]);

        $this->assertInstanceof(PDO::class, $connect->pdo());

        $connect->close();
    }

    public function testReadConnectDistributedButAllInvalid(): void
    {
        $connect = $this->createDatabaseConnectMock([
            'driver'             => 'mysql',
            'separate'           => false,
            'distributed'        => true,
            'master'             => [
                'host'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['HOST'],
                'port'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PORT'],
                'name'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['NAME'],
                'user'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['USER'],
                'password' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PASSWORD'],
                'charset'  => 'utf8',
                'options'  => [
                    PDO::ATTR_PERSISTENT        => false,
                    PDO::ATTR_CASE              => PDO::CASE_NATURAL,
                    PDO::ATTR_ORACLE_NULLS      => PDO::NULL_NATURAL,
                    PDO::ATTR_STRINGIFY_FETCHES => false,
                    PDO::ATTR_EMULATE_PREPARES  => false,
                ],
            ],
            'slave' => [
                [
                    'host'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['HOST'],
                    'port'     => '5555', // not invalid
                    'name'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['NAME'],
                    'user'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['USER'],
                    'password' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PASSWORD'],
                    'charset'  => 'utf8',
                    'options'  => [
                        PDO::ATTR_PERSISTENT        => false,
                        PDO::ATTR_CASE              => PDO::CASE_NATURAL,
                        PDO::ATTR_ORACLE_NULLS      => PDO::NULL_NATURAL,
                        PDO::ATTR_STRINGIFY_FETCHES => false,
                        PDO::ATTR_EMULATE_PREPARES  => false,
                    ],
                ],
                [
                    'host'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['HOST'],
                    'port'     => '6666', // not invalid
                    'name'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['NAME'],
                    'user'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['USER'],
                    'password' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PASSWORD'],
                    'charset'  => 'utf8',
                    'options'  => [
                        PDO::ATTR_PERSISTENT        => false,
                        PDO::ATTR_CASE              => PDO::CASE_NATURAL,
                        PDO::ATTR_ORACLE_NULLS      => PDO::NULL_NATURAL,
                        PDO::ATTR_STRINGIFY_FETCHES => false,
                        PDO::ATTR_EMULATE_PREPARES  => false,
                    ],
                ],
            ],
        ]);

        $this->assertInstanceof(PDO::class, $connect->pdo());
        $this->assertInstanceof(PDO::class, $connect->pdo());

        $connect->close();
    }

    /**
     * @api(
     *     title="数据库读写分离",
     *     description="
     * 数据库配置项 `separate` 表示读写分离，如果从数据库均连接失败，则读数据还是会走主库。
     * ",
     *     note="",
     * )
     */
    public function testReadConnectDistributedButAllInvalidAndAlsoIsSeparate(): void
    {
        $connect = $this->createDatabaseConnectMock([
            'driver'             => 'mysql',
            'separate'           => true,
            'distributed'        => true,
            'master'             => [
                'host'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['HOST'],
                'port'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PORT'],
                'name'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['NAME'],
                'user'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['USER'],
                'password' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PASSWORD'],
                'charset'  => 'utf8',
                'options'  => [
                    PDO::ATTR_PERSISTENT        => false,
                    PDO::ATTR_CASE              => PDO::CASE_NATURAL,
                    PDO::ATTR_ORACLE_NULLS      => PDO::NULL_NATURAL,
                    PDO::ATTR_STRINGIFY_FETCHES => false,
                    PDO::ATTR_EMULATE_PREPARES  => false,
                ],
            ],
            'slave' => [
                [
                    'host'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['HOST'],
                    'port'     => '5555', // not invalid
                    'name'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['NAME'],
                    'user'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['USER'],
                    'password' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PASSWORD'],
                    'charset'  => 'utf8',
                    'options'  => [
                        PDO::ATTR_PERSISTENT        => false,
                        PDO::ATTR_CASE              => PDO::CASE_NATURAL,
                        PDO::ATTR_ORACLE_NULLS      => PDO::NULL_NATURAL,
                        PDO::ATTR_STRINGIFY_FETCHES => false,
                        PDO::ATTR_EMULATE_PREPARES  => false,
                    ],
                ],
                [
                    'host'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['HOST'],
                    'port'     => '6666', // not invalid
                    'name'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['NAME'],
                    'user'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['USER'],
                    'password' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PASSWORD'],
                    'charset'  => 'utf8',
                    'options'  => [
                        PDO::ATTR_PERSISTENT        => false,
                        PDO::ATTR_CASE              => PDO::CASE_NATURAL,
                        PDO::ATTR_ORACLE_NULLS      => PDO::NULL_NATURAL,
                        PDO::ATTR_STRINGIFY_FETCHES => false,
                        PDO::ATTR_EMULATE_PREPARES  => false,
                    ],
                ],
            ],
        ]);

        $this->assertInstanceof(PDO::class, $connect->pdo());
        $this->assertInstanceof(PDO::class, $connect->pdo());

        $connect->close();
    }

    public function testConnectException(): void
    {
        $this->expectException(\PDOException::class);

        $connect = $this->createDatabaseConnectMock([
            'driver'             => 'mysql',
            'separate'           => false,
            'distributed'        => false,
            'master'             => [
                'host'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['HOST'],
                'port'     => '5566',
                'name'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['NAME'],
                'user'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['USER'],
                'password' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PASSWORD'],
                'charset'  => 'utf8',
                'options'  => [
                    PDO::ATTR_PERSISTENT        => false,
                    PDO::ATTR_CASE              => PDO::CASE_NATURAL,
                    PDO::ATTR_ORACLE_NULLS      => PDO::NULL_NATURAL,
                    PDO::ATTR_STRINGIFY_FETCHES => false,
                    PDO::ATTR_EMULATE_PREPARES  => false,
                ],
            ],
            'slave' => [],
        ]);

        $connect->pdo(true);
    }

    public function testReconnectRetryForQuery(): void
    {
        $this->expectException(\PDOException::class);
        $this->expectExceptionMessage(
            'SQLSTATE[42S02]: Base table or view not found: 1146 Table \'test.not_found_table\' doesn\'t exist'
        );

        $connect = $this->createDatabaseConnectMock([
            'driver'             => 'mysql',
            'separate'           => false,
            'distributed'        => true,
            'master'             => [
                'host'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['HOST'],
                'port'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PORT'],
                'name'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['NAME'],
                'user'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['USER'],
                'password' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PASSWORD'],
                'charset'  => 'utf8',
                'options'  => [
                    PDO::ATTR_PERSISTENT        => false,
                    PDO::ATTR_CASE              => PDO::CASE_NATURAL,
                    PDO::ATTR_ORACLE_NULLS      => PDO::NULL_NATURAL,
                    PDO::ATTR_STRINGIFY_FETCHES => false,
                    PDO::ATTR_EMULATE_PREPARES  => false,
                ],
            ],
            'slave' => [],
        ], MyMysql::class);

        $this->assertInstanceof(MyMysql::class, $connect);

        $connect->query('SELECT * FROM not_found_table');
    }

    public function testReconnectRetryForExecute(): void
    {
        $this->expectException(\PDOException::class);
        $this->expectExceptionMessage(
            'SQLSTATE[42S02]: Base table or view not found: 1146 Table \'test.not_found_table\' doesn\'t exist'
        );

        $connect = $this->createDatabaseConnectMock([
            'driver'             => 'mysql',
            'separate'           => false,
            'distributed'        => true,
            'master'             => [
                'host'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['HOST'],
                'port'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PORT'],
                'name'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['NAME'],
                'user'     => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['USER'],
                'password' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PASSWORD'],
                'charset'  => 'utf8',
                'options'  => [
                    PDO::ATTR_PERSISTENT        => false,
                    PDO::ATTR_CASE              => PDO::CASE_NATURAL,
                    PDO::ATTR_ORACLE_NULLS      => PDO::NULL_NATURAL,
                    PDO::ATTR_STRINGIFY_FETCHES => false,
                    PDO::ATTR_EMULATE_PREPARES  => false,
                ],
            ],
            'slave' => [],
        ], MyMysql::class);

        $this->assertInstanceof(MyMysql::class, $connect);

        $connect->execute('DELETE FROM not_found_table WHERE id > 0');
    }

    public function testBindNull(): void
    {
        $this->expectException(\PDOException::class);
        $this->expectExceptionMessage(
            'SQLSTATE[23000]: Integrity constraint violation: 1048 Column \'content\' cannot be null'
        );

        $connect = $this->createDatabaseConnect();
        $data = ['name' => 'tom', 'content' => null];
        $connect
            ->table('guest_book')
            ->insert($data);
    }

    public function testDatabaseSelect(): void
    {
        $connect = $this->createDatabaseConnect();
        $this->assertSame([], $connect->query('SELECT * FROM guest_book'));
        $this->assertInstanceof(Select::class, $connect->databaseSelect());
    }

    /**
     * @api(
     *     title="databaseSelect 返回查询对象",
     *     description="",
     *     note="",
     * )
     */
    public function testDatabaseSelectIsNotInit(): void
    {
        $connect = $this->createDatabaseConnect();
        $this->assertInstanceof(Select::class, $connect->databaseSelect());
    }

    /**
     * @api(
     *     title="getTableNames 取得数据库表名列表",
     *     description="",
     *     note="",
     * )
     */
    public function testGetTableNames(): void
    {
        $connect = $this->createDatabaseConnect();
        $result = $connect->getTableNames('test');
        $this->assertTrue(in_array('guest_book', $result, true));
    }

    /**
     * @api(
     *     title="getTableColumns 取得数据库表字段信息",
     *     description="",
     *     note="",
     * )
     */
    public function testGetTableColumns(): void
    {
        $connect = $this->createDatabaseConnect();
        $result = $connect->getTableColumns('guest_book');

        $sql = <<<'eot'
            {
                "list": {
                    "id": {
                        "field": "id",
                        "type": "bigint(20)",
                        "collation": null,
                        "null": false,
                        "key": "PRI",
                        "default": null,
                        "extra": "auto_increment",
                        "comment": "ID",
                        "primary_key": true,
                        "type_name": "bigint",
                        "type_length": "20",
                        "auto_increment": true
                    },
                    "name": {
                        "field": "name",
                        "type": "varchar(64)",
                        "collation": "utf8_general_ci",
                        "null": false,
                        "key": "",
                        "default": "",
                        "extra": "",
                        "comment": "名字",
                        "primary_key": false,
                        "type_name": "varchar",
                        "type_length": "64",
                        "auto_increment": false
                    },
                    "content": {
                        "field": "content",
                        "type": "longtext",
                        "collation": "utf8_general_ci",
                        "null": false,
                        "key": "",
                        "default": null,
                        "extra": "",
                        "comment": "评论内容",
                        "primary_key": false,
                        "type_name": "longtext",
                        "type_length": null,
                        "auto_increment": false
                    },
                    "create_at": {
                        "field": "create_at",
                        "type": "datetime",
                        "collation": null,
                        "null": false,
                        "key": "",
                        "default": "CURRENT_TIMESTAMP",
                        "extra": "",
                        "comment": "创建时间",
                        "primary_key": false,
                        "type_name": "datetime",
                        "type_length": null,
                        "auto_increment": false
                    }
                },
                "primary_key": [
                    "id"
                ],
                "auto_increment": "id",
                "table_collation": "utf8_general_ci",
                "table_comment": "留言板"
            }
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $result
            )
        );
    }

    public function testGetTableColumnsButTableNotFound(): void
    {
        $connect = $this->createDatabaseConnect();
        $result = $connect->getTableColumns('table_not_found');

        $sql = <<<'eot'
            {
                "list": [],
                "primary_key": null,
                "auto_increment": null,
                "table_collation": null,
                "table_comment": null
            }
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $result
            )
        );
    }

    protected function getDatabaseTable(): array
    {
        return ['guest_book'];
    }
}

class MyMysql extends Mysql
{
    /**
     * 是否需要重连.
     */
    protected function needReconnect(PDOException $e): bool
    {
        // 任意错误都需要重试，为了测试的需要
        return 1 && $this->reconnectRetry <= self::RECONNECT_MAX;
    }
}
