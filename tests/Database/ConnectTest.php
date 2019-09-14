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

use Exception;
use Leevel\Database\IDatabase;
use Leevel\Database\Mysql;
use PDO;
use PDOException;
use Tests\Database\DatabaseTestCase as TestCase;
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
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnect();

        $sql = <<<'eot'
            [
                "INSERT INTO `guest_book` (`guest_book`.`name`,`guest_book`.`content`) VALUES (:name,:content)",
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
                $connect
                    ->sql()
                    ->table('guest_book')
                    ->insert($data)
            )
        );

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

    public function testQueryOnlyAllowedSelect(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The query method only allows select and procedure SQL statements.'
        );

        $connect = $this->createDatabaseConnect();

        $connect->query('insert into guest_book (name, content) values (?, ?)', ['小鸭子', '喜欢游泳']);
    }

    public function testExecuteNotAllowedSelect(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The query method not allows select and procedure SQL statements.'
        );

        $connect = $this->createDatabaseConnect();

        $connect->execute('select * from guest_book where id=?', [1]);
    }

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

    public function testCallProcedure(): void
    {
        $connect = $this->createDatabaseConnect();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 1; $n++) {
            $connect
                ->table('guest_book')
                ->insert($data);
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
    }

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

    public function testNormalizeColumnValueWithBool(): void
    {
        $connect = $this->createDatabaseConnect();

        $this->assertTrue($connect->normalizeColumnValue(true));
        $this->assertFalse($connect->normalizeColumnValue(false));
    }

    public function testNormalizeBindParamTypeWithBool(): void
    {
        $connect = $this->createDatabaseConnect();

        $this->assertSame(PDO::PARAM_BOOL, $connect->normalizeBindParamType(true));
        $this->assertSame(PDO::PARAM_BOOL, $connect->normalizeBindParamType(false));
    }

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
                    PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION,
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
                        PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION,
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
                        PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION,
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
                    PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION,
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
                        PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION,
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
                        PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION,
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
                    PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION,
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
                        PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION,
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
                        PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION,
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
                    PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION,
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
                    PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION,
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
                    PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION,
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

    protected function getDatabaseTable(): array
    {
        return ['guest_book'];
    }
}

class MyMysql extends Mysql
{
    /**
     * 是否需要重连.
     *
     * @return bool
     */
    protected function needReconnect(PDOException $e): bool
    {
        // 任意错误都需要重试，为了测试的需要
        return 1 && $this->reconnectRetry <= self::RECONNECT_MAX;
    }
}
