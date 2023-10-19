<?php

declare(strict_types=1);

namespace Tests\Database;

use Leevel\Database\Database;
use Leevel\Database\IDatabase;
use Leevel\Database\Mysql;
use Leevel\Database\Select;
use Leevel\Filesystem\Helper;
use Leevel\Kernel\Utils\Api;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\MysqlNeedReconnectMock;

#[Api([
    'zh-CN:title' => '数据库连接',
    'path' => 'database/database',
])]
final class DatabaseTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();

        $path = \dirname(__DIR__).'/databaseCacheManager';
        if (is_dir($path)) {
            Helper::deleteDirectory($path);
        }
    }

    #[Api([
        'zh-CN:title' => '基本使用',
    ])]
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnect();

        $database = $connect;

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        static::assertSame(
            1,
            $database
                ->table('guest_book')
                ->insert($data),
        );

        $result = $database
            ->table('guest_book', 'name,content')
            ->where('id', 1)
            ->findOne()
        ;

        static::assertSame('tom', $result->name);
        static::assertSame('I love movie.', $result->content);
    }

    public function testBaseUse2(): void
    {
        $connect = $this->createDatabaseConnect();

        $sql = <<<'eot'
            [
                "INSERT INTO `guest_book` (`guest_book`.`name`,`guest_book`.`content`) VALUES (:named_param_name,:named_param_content)",
                {
                    "named_param_name": [
                        "小鸭子"
                    ],
                    "named_param_content": [
                        "吃饭饭"
                    ]
                },
                false
            ]
            eot;

        $data = ['name' => '小鸭子', 'content' => '吃饭饭'];

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('guest_book')
                    ->insert($data),
                $connect
            )
        );

        $this->truncateDatabase(['guest_book']);

        // 写入数据
        static::assertSame(
            1,
            $connect
                ->table('guest_book')
                ->insert($data),
        );

        static::assertSame(1, $connect->table('guest_book')->findCount());

        $insertData = $connect
            ->table('guest_book')
            ->where('id', 1)
            ->findOne()
        ;

        static::assertSame(1, $insertData->id);
        static::assertSame('小鸭子', $insertData->name);
        static::assertSame('吃饭饭', $insertData->content);
        static::assertStringContainsString(date('Y-m'), $insertData->create_at);
    }

    #[Api([
        'zh-CN:title' => 'query 查询数据记录',
    ])]
    public function testQuery(): void
    {
        $connect = $this->createDatabaseConnect();
        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        static::assertSame(
            1,
            $connect
                ->table('guest_book')
                ->insert($data),
        );

        $insertData = $connect->query('select * from guest_book where id=?', [1]);
        $insertData = (array) $insertData[0];

        static::assertSame(1, $insertData['id']);
        static::assertSame('tom', $insertData['name']);
        static::assertSame('I love movie.', $insertData['content']);
        static::assertStringContainsString(date('Y-m'), $insertData['create_at']);
    }

    public function testQueryBindBoolType(): void
    {
        $connect = $this->createDatabaseConnect();
        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        static::assertSame(
            1,
            $connect
                ->table('guest_book')
                ->insert($data),
        );

        $insertData = $connect->query('select * from guest_book where id=?', [true]);
        $insertData = (array) $insertData[0];

        static::assertSame(1, $insertData['id']);
        static::assertSame('tom', $insertData['name']);
        static::assertSame('I love movie.', $insertData['content']);
        static::assertStringContainsString(date('Y-m'), $insertData['create_at']);
    }

    #[Api([
        'zh-CN:title' => 'query 查询数据记录支持缓存',
        'zh-CN:description' => <<<'EOT'
`query` 是一个底层查询方法支持直接设置缓存，实际上其它的查询都会走这个 `query` 查询方法。

**query 原型**

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Leevel\Database\Database::class, 'query', 'define')]}
```
EOT,
    ])]
    public function testQueryCache(): void
    {
        $manager = $this->createDatabaseManager();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 5; ++$n) {
            $manager
                ->table('guest_book')
                ->insert($data)
            ;
        }

        $cacheDir = \dirname(__DIR__).'/databaseCacheManager';
        $cacheFile = $cacheDir.'/testcachekey.php';

        $result = $manager
            ->table('guest_book')
            ->query('SELECT * FROM guest_book')
        ;
        static::assertFileDoesNotExist($cacheFile);
        static::assertCount(6, $result);
        static::assertSame(1, $result[0]->id);
        static::assertSame('tom', $result[0]->name);
        static::assertSame('I love movie.', $result[0]->content);

        $resultWithoutCache = $manager
            ->query('SELECT * FROM guest_book', [], false, 'testcachekey')
        ;
        // cached data
        $resultWithCache = $manager
            ->query('SELECT * FROM guest_book', [], false, 'testcachekey')
        ;

        static::assertFileExists($cacheFile);
        static::assertCount(6, $resultWithCache);
        static::assertSame(1, $resultWithCache[0]->id);
        static::assertSame('tom', $resultWithCache[0]->name);
        static::assertSame('I love movie.', $resultWithCache[0]->content);
        static::assertEquals($result, $resultWithCache);
        static::assertFalse($result === $resultWithCache);
        static::assertEquals($resultWithCache, $resultWithoutCache);
    }

    public function testCacheQueryButCacheWasNotSet(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Cache was not set.');

        $connect = $this->createDatabaseConnect();
        $connect->query('SELECT * FROM guest_book', [], false, 'testcachekey');
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

    #[Api([
        'zh-CN:title' => 'execute 执行 SQL 语句',
    ])]
    public function testExecute(): void
    {
        $connect = $this->createDatabaseConnect();

        static::assertSame(1, $connect->execute('insert into guest_book (name, content) values (?, ?)', ['小鸭子', '喜欢游泳']));
        $insertData = $connect->query('select * from guest_book where id=?', [1]);
        $insertData = (array) $insertData[0];

        static::assertSame(1, $insertData['id']);
        static::assertSame('小鸭子', $insertData['name']);
        static::assertSame('喜欢游泳', $insertData['content']);
        static::assertStringContainsString(date('Y-m'), $insertData['create_at']);
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
        static::markTestSkipped('Skip query only allowed select.');

        $this->expectException(\PDOException::class);

        $connect = $this->createDatabaseConnect();
        // 由用户自己保证使用 query,procedure 还是 execute，系统不加限制，减少底层设计复杂度
        $result = $connect->query('insert into guest_book (name, content) values (?, ?)', ['小鸭子', '喜欢游泳']);
        static::assertSame([], $result);
    }

    public function testExecuteNotAllowedSelect(): void
    {
        static::markTestSkipped('Execute not allowed select.');

        $connect = $this->createDatabaseConnect();
        // 由用户自己保证使用 query,procedure 还是 execute，系统不加限制，减少底层设计复杂度
        $result = $connect->execute('select * from guest_book where id=?', [1]);
        static::assertSame(0, $result);
    }

    #[Api([
        'zh-CN:title' => 'cursor 游标查询',
        'zh-CN:description' => <<<'EOT'
`cursor` 游标查询可以节省内存。

**cursor 原型**

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Leevel\Database\Database::class, 'cursor', 'define')]}
```
EOT,
    ])]
    public function testCursor(): void
    {
        $manager = $this->createDatabaseManager();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 5; ++$n) {
            $manager
                ->table('guest_book')
                ->insert($data)
            ;
        }

        $result = $manager->cursor('SELECT * FROM guest_book');
        $this->assertInstanceof(\Generator::class, $result);
        $n = 1;
        foreach ($result as $v) {
            static::assertSame($n, $v->id);
            static::assertSame('tom', $v->name);
            static::assertSame('I love movie.', $v->content);
            ++$n;
        }
    }

    #[Api([
        'zh-CN:title' => 'select 原生 SQL 查询数据',
    ])]
    public function testSelect(): void
    {
        $connect = $this->createDatabaseConnect();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        static::assertSame(
            1,
            $connect
                ->table('guest_book')
                ->insert($data),
        );

        $insertData = $connect->select('select * from guest_book where id = ?', [1]);
        $insertData = (array) $insertData[0];

        static::assertSame(1, $insertData['id']);
        static::assertSame('tom', $insertData['name']);
        static::assertSame('I love movie.', $insertData['content']);
        static::assertStringContainsString(date('Y-m'), $insertData['create_at']);
    }

    #[Api([
        'zh-CN:title' => 'select 原生 SQL 查询数据支持参数绑定',
    ])]
    public function testSelectWithBind(): void
    {
        $connect = $this->createDatabaseConnect();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        static::assertSame(
            1,
            $connect
                ->table('guest_book')
                ->insert($data),
        );

        $insertData = $connect->select('select * from guest_book where id = :id', ['id' => 1]);
        $insertData = (array) $insertData[0];

        static::assertSame(1, $insertData['id']);
        static::assertSame('tom', $insertData['name']);
        static::assertSame('I love movie.', $insertData['content']);
        static::assertStringContainsString(date('Y-m'), $insertData['create_at']);
    }

    #[Api([
        'zh-CN:title' => 'insert 插入数据 insert (支持原生 SQL)',
    ])]
    public function testInsert(): void
    {
        $connect = $this->createDatabaseConnect();

        static::assertSame(1, $connect->insert('insert into guest_book (name, content) values (?, ?)', ['tom', 'I love movie.']));

        $insertData = $connect->select('select * from guest_book where id = :id', ['id' => 1]);
        $insertData = (array) $insertData[0];

        static::assertSame(1, $insertData['id']);
        static::assertSame('tom', $insertData['name']);
        static::assertSame('I love movie.', $insertData['content']);
        static::assertStringContainsString(date('Y-m'), $insertData['create_at']);
    }

    #[Api([
        'zh-CN:title' => 'update 更新数据 update (支持原生 SQL)',
    ])]
    public function testUpdate(): void
    {
        $connect = $this->createDatabaseConnect();

        static::assertSame(1, $connect->insert('insert into guest_book (name, content) values (?, ?)', ['tom', 'I love movie.']));

        $insertData = $connect->select('select * from guest_book where id = :id', ['id' => 1]);
        $insertData = (array) $insertData[0];

        static::assertSame(1, $insertData['id']);
        static::assertSame('tom', $insertData['name']);
        static::assertSame('I love movie.', $insertData['content']);
        static::assertStringContainsString(date('Y-m'), $insertData['create_at']);

        static::assertSame(1, $connect->update('update guest_book set name = "小牛" where id = ?', [1]));

        $insertData = $connect->select('select * from guest_book where id = :id', ['id' => 1]);
        $insertData = (array) $insertData[0];

        static::assertSame(1, $insertData['id']);
        static::assertSame('小牛', $insertData['name']);
        static::assertSame('I love movie.', $insertData['content']);
        static::assertStringContainsString(date('Y-m'), $insertData['create_at']);
    }

    #[Api([
        'zh-CN:title' => 'delete 删除数据 delete (支持原生 SQL)',
    ])]
    public function testDelete(): void
    {
        $connect = $this->createDatabaseConnect();

        static::assertSame(1, $connect->insert('insert into guest_book (name, content) values (?, ?)', ['tom', 'I love movie.']));

        $insertData = $connect->select('select * from guest_book where id = :id', ['id' => 1]);
        $insertData = (array) $insertData[0];

        static::assertSame(1, $insertData['id']);
        static::assertSame('tom', $insertData['name']);
        static::assertSame('I love movie.', $insertData['content']);
        static::assertStringContainsString(date('Y-m'), $insertData['create_at']);

        static::assertSame(1, $connect->delete('delete from guest_book where id = ?', [1]));
        static::assertSame(0, $connect->table('guest_book')->findCount());
    }

    #[Api([
        'zh-CN:title' => 'transaction 执行数据库事务',
    ])]
    public function testTransaction(): void
    {
        $connect = $this->createDatabaseConnect();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 1; ++$n) {
            $connect
                ->table('guest_book')
                ->insert($data)
            ;
        }

        static::assertSame(2, $connect->table('guest_book')->findCount());

        $connect->transaction(function ($connect): void {
            $connect
                ->table('guest_book')
                ->where('id', 1)
                ->delete()
            ;

            $this->assertSame(1, $connect->table('guest_book')->findCount());

            $connect
                ->table('guest_book')
                ->where('id', 2)
                ->delete()
            ;

            $this->assertSame(0, $connect->table('guest_book')->findCount());
        });

        static::assertSame(0, $connect->table('guest_book')->findCount());
    }

    #[Api([
        'zh-CN:title' => 'transaction 执行数据库事务回滚例子',
    ])]
    public function testTransactionRollback(): void
    {
        $connect = $this->createDatabaseConnect();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 1; ++$n) {
            $connect
                ->table('guest_book')
                ->insert($data)
            ;
        }

        static::assertSame(2, $connect->table('guest_book')->findCount());

        static::assertFalse($connect->inTransaction());

        try {
            $connect->transaction(function ($connect): void {
                $connect->table('guest_book')->where('id', 1)->delete();

                $this->assertSame(1, $connect->table('guest_book')->findCount());

                $this->assertTrue($connect->inTransaction());

                throw new \Exception('Will rollback');
                $connect->table('guest_book')->where('id', 2)->delete();
            });
        } catch (\Throwable $e) {
            static::assertSame('Will rollback', $e->getMessage());
        }

        static::assertFalse($connect->inTransaction());

        static::assertSame(2, $connect->table('guest_book')->findCount());
    }

    #[Api([
        'zh-CN:title' => 'beginTransaction.commit 启动事务和用于非自动提交状态下面的查询提交',
    ])]
    public function testTransactionByCustom(): void
    {
        $connect = $this->createDatabaseConnect();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 1; ++$n) {
            $connect
                ->table('guest_book')
                ->insert($data)
            ;
        }

        static::assertSame(2, $connect->table('guest_book')->findCount());

        $connect->beginTransaction();

        $connect->table('guest_book')->where('id', 1)->delete();

        static::assertSame(1, $connect->table('guest_book')->findCount());

        $connect->table('guest_book')->where('id', 2)->delete();

        static::assertSame(0, $connect->table('guest_book')->findCount());

        $connect->commit();

        static::assertSame(0, $connect->table('guest_book')->findCount());
    }

    #[Api([
        'zh-CN:title' => 'beginTransaction.rollBack 启动事务和事务回滚',
    ])]
    public function testTransactionRollbackByCustom(): void
    {
        $connect = $this->createDatabaseConnect();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 1; ++$n) {
            $connect
                ->table('guest_book')
                ->insert($data)
            ;
        }

        static::assertSame(2, $connect->table('guest_book')->findCount());

        static::assertFalse($connect->inTransaction());

        try {
            $connect->beginTransaction();

            $connect
                ->table('guest_book')
                ->where('id', 1)
                ->delete()
            ;

            static::assertSame(1, $connect->table('guest_book')->findCount());

            static::assertTrue($connect->inTransaction());

            throw new \Exception('Will rollback');
            $connect->table('guest_book')->where('id', 2)->delete();

            $connect->commit();
        } catch (\Throwable $e) {
            static::assertSame('Will rollback', $e->getMessage());

            $connect->rollBack();
        }

        static::assertFalse($connect->inTransaction());

        static::assertSame(2, $connect->table('guest_book')->findCount());
    }

    #[Api([
        'zh-CN:title' => 'procedure 查询存储过程数据记录',
    ])]
    public function testCallProcedure(): void
    {
        static::markTestSkipped('Skip procedure.');

        $connect = $this->createDatabaseConnect();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 1; ++$n) {
            $connect
                ->table('guest_book')
                ->insert($data)
            ;
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

        static::assertSame(
            $data,
            $this->varJson(
                $result
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'procedure 查询存储过程数据记录支持参数绑定',
    ])]
    public function testCallProcedure2(): void
    {
        static::markTestSkipped('Skip procedure.');

        $connect = $this->createDatabaseConnect();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 1; ++$n) {
            $connect
                ->table('guest_book')
                ->insert($data)
            ;
        }

        $result = $connect->procedure('CALL test_procedure2(0,:name)', [
            'name' => [null, \PDO::PARAM_STR | \PDO::PARAM_INPUT_OUTPUT, 200],
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

        static::assertSame(
            $data,
            $this->varJson(
                $result
            )
        );
    }

    #[Api([
        'zh-CN:title' => '查询存储过程数据支持原生方法',
    ])]
    public function testCallProcedure3(): void
    {
        static::markTestSkipped('Skip procedure.');

        $connect = $this->createDatabaseConnect();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 1; ++$n) {
            $connect
                ->table('guest_book')
                ->insert($data)
            ;
        }

        $pdoStatement = $connect->pdo(true)->prepare('CALL test_procedure2(0,:name)');
        $outName = null;
        $pdoStatement->bindParam(':name', $outName, \PDO::PARAM_STR | \PDO::PARAM_INPUT_OUTPUT, 200);
        $pdoStatement->execute();

        $result = [];
        while ($pdoStatement->columnCount()) {
            $result[] = $pdoStatement->fetchAll(\PDO::FETCH_OBJ);
            $pdoStatement->nextRowset();
        }

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

        static::assertSame(
            $data,
            $this->varJson(
                $result
            )
        );
    }

    #[Api([
        'zh-CN:title' => '查询存储过程数据支持缓存',
        'zh-CN:description' => <<<'EOT'
`procedure` 是一个底层查询方法支持直接设置缓存。

**procedure 原型**

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Leevel\Database\Database::class, 'procedure', 'define')]}
```
EOT,
    ])]
    public function testCacheProcedure(): void
    {
        static::markTestSkipped('Skip procedure.');

        $manager = $this->createDatabaseManager();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 1; ++$n) {
            $manager
                ->table('guest_book')
                ->insert($data)
            ;
        }

        $cacheDir = \dirname(__DIR__).'/databaseCacheManager';
        $cacheFile = $cacheDir.'/testcachekey.php';

        $result = $manager
            ->procedure('CALL test_procedure(0)')
        ;
        static::assertFileDoesNotExist($cacheFile);
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
        static::assertSame(
            $data,
            $this->varJson(
                $result
            )
        );

        $resultWithoutCache = $manager
            ->procedure('CALL test_procedure(0)', [], false, 'testcachekey')
        ;
        static::assertFileExists($cacheFile);
        // cached data
        $resultWithCache = $manager
            ->procedure('CALL test_procedure(0)', [], false, 'testcachekey')
        ;
        static::assertFileExists($cacheFile);
        static::assertSame(
            $data,
            $this->varJson(
                $resultWithCache
            )
        );
        static::assertSame($result, $resultWithCache);
        static::assertFalse($result === $resultWithCache);
        static::assertSame($resultWithCache, $resultWithoutCache);
    }

    public function testCacheProcedureButCacheWasNotSet(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Cache was not set.');

        $connect = $this->createDatabaseConnect();
        $connect->procedure('CALL test_procedure(0)', [], false, 'testcachekey');
    }

    #[Api([
        'zh-CN:title' => 'pdo 返回 PDO 查询连接',
    ])]
    public function testPdo(): void
    {
        $connect = $this->createDatabaseConnect();

        static::assertNull($connect->pdo(IDatabase::MASTER));
        $this->assertInstanceof(\PDO::class, $connect->pdo(true));
        $this->assertInstanceof(\PDO::class, $connect->pdo(IDatabase::MASTER));
        static::assertNull($connect->pdo(5));

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
     * @group ignoredGroup
     */
    #[Api([
        'zh-CN:title' => 'setSavepoints 设置是否启用部分事务回滚保存点',
    ])]
    public function testBeginTransactionWithCreateSavepoint(): void
    {
        $connect = $this->createDatabaseConnect();

        $connect->setSavepoints(true);
        $connect->beginTransaction();
        $connect
            ->table('guest_book')
            ->insert(['name' => 'tom']) // `tom` will not rollBack
        ;

        $connect->beginTransaction();
        static::assertSame('SAVEPOINT trans2', $connect->getLastSql());

        $connect
            ->table('guest_book')
            ->insert(['name' => 'jerry'])
        ;

        $connect->rollBack();
        static::assertSame('ROLLBACK TO SAVEPOINT trans2', $connect->getLastSql());
        $connect->commit();

        $book = $connect
            ->table('guest_book')
            ->where('id', 1)
            ->findOne()
        ;

        static::assertSame(1, $connect->table('guest_book')->findCount());
        static::assertSame('tom', $book->name);
    }

    public function testCommitWithoutActiveTransaction(): void
    {
        $this->expectException(\Leevel\Database\ConnectionException::class);
        $this->expectExceptionMessage(
            '[Commit]There was no active transaction.'
        );

        $connect = $this->createDatabaseConnect();
        $connect->commit();
    }

    public function testCommitButIsRollbackOnly(): void
    {
        $this->expectException(\Leevel\Database\ConnectionException::class);
        $this->expectExceptionMessage(
            'Only transaction rollback is allowed.'
        );

        $connect = $this->createDatabaseConnect();
        $connect->beginTransaction();
        $connect->beginTransaction();
        $connect->rollBack();
        $connect->commit();
    }

    /**
     * @group ignoredGroup
     */
    #[Api([
        'zh-CN:title' => 'setSavepoints 设置是否启用部分事务提交保存点',
    ])]
    public function testCommitWithReleaseSavepoint(): void
    {
        $connect = $this->createDatabaseConnect();
        $connect->setSavepoints(true);
        $connect->beginTransaction();

        $connect
            ->table('guest_book')
            ->insert(['name' => 'tom'])
        ;

        $connect->beginTransaction();
        static::assertSame('SAVEPOINT trans2', $connect->getLastSql());

        $connect
            ->table('guest_book')
            ->insert(['name' => 'jerry'])
        ;

        $connect->commit();
        static::assertSame('RELEASE SAVEPOINT trans2', $connect->getLastSql());
        $connect->commit();

        $book = $connect
            ->table('guest_book')
            ->where('id', 1)
            ->findOne()
        ;
        $book2 = $connect
            ->table('guest_book')
            ->where('id', 2)
            ->findOne()
        ;

        static::assertSame(2, $connect->table('guest_book')->findCount());
        static::assertSame('tom', $book->name);
        static::assertSame('jerry', $book2->name);
    }

    public function testRollBackWithoutActiveTransaction(): void
    {
        $this->expectException(\Leevel\Database\ConnectionException::class);
        $this->expectExceptionMessage(
            '[RollBack]There was no active transaction.'
        );

        $connect = $this->createDatabaseConnect();

        $connect->rollBack();
    }

    #[Api([
        'zh-CN:title' => 'numRows 返回影响记录',
    ])]
    public function testNumRows(): void
    {
        $connect = $this->createDatabaseConnect();

        static::assertSame(0, $connect->numRows());

        $connect
            ->table('guest_book')
            ->insert(['name' => 'jerry', 'content' => ''])
        ;

        static::assertSame(1, $connect->numRows());

        $connect
            ->table('guest_book')
            ->where('id', 1)
            ->update(['name' => 'jerry'])
        ;

        static::assertSame(0, $connect->numRows());

        $connect
            ->table('guest_book')
            ->where('id', 1)
            ->update(['name' => 'tom'])
        ;

        static::assertSame(1, $connect->numRows());
    }

    #[Api([
        'zh-CN:title' => '数据库主从',
        'zh-CN:description' => <<<'EOT'
数据库配置项 `distributed` 表示主从，如果从数据库均连接失败，则还是会走主库。
EOT,
    ])]
    public function testReadConnectDistributed(): void
    {
        $connect = $this->createDatabaseConnectMock([
            'driver' => 'mysql',
            'separate' => false,
            'distributed' => true,
            'master' => [
                'host' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['HOST'],
                'port' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PORT'],
                'name' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['NAME'],
                'user' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['USER'],
                'password' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PASSWORD'],
                'charset' => 'utf8',
                'options' => [
                    \PDO::ATTR_PERSISTENT => false,
                    \PDO::ATTR_CASE => \PDO::CASE_NATURAL,
                    \PDO::ATTR_ORACLE_NULLS => \PDO::NULL_NATURAL,
                    \PDO::ATTR_STRINGIFY_FETCHES => false,
                    \PDO::ATTR_EMULATE_PREPARES => false,
                    \PDO::ATTR_TIMEOUT => 30,
                ],
            ],
            'slave' => [
                [
                    'host' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['HOST'],
                    'port' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PORT'],
                    'name' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['NAME'],
                    'user' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['USER'],
                    'password' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PASSWORD'],
                    'charset' => 'utf8',
                    'options' => [
                        \PDO::ATTR_PERSISTENT => false,
                        \PDO::ATTR_CASE => \PDO::CASE_NATURAL,
                        \PDO::ATTR_ORACLE_NULLS => \PDO::NULL_NATURAL,
                        \PDO::ATTR_STRINGIFY_FETCHES => false,
                        \PDO::ATTR_EMULATE_PREPARES => false,
                        \PDO::ATTR_TIMEOUT => 30,
                    ],
                ],
                [
                    'host' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['HOST'],
                    'port' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PORT'],
                    'name' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['NAME'],
                    'user' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['USER'],
                    'password' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PASSWORD'],
                    'charset' => 'utf8',
                    'options' => [
                        \PDO::ATTR_PERSISTENT => false,
                        \PDO::ATTR_CASE => \PDO::CASE_NATURAL,
                        \PDO::ATTR_ORACLE_NULLS => \PDO::NULL_NATURAL,
                        \PDO::ATTR_STRINGIFY_FETCHES => false,
                        \PDO::ATTR_EMULATE_PREPARES => false,
                        \PDO::ATTR_TIMEOUT => 30,
                    ],
                ],
            ],
        ]);

        $this->assertInstanceof(\PDO::class, $connect->pdo());

        $connect->close();
    }

    public function testReadConnectDistributedButAllInvalid(): void
    {
        $connect = $this->createDatabaseConnectMock([
            'driver' => 'mysql',
            'separate' => false,
            'distributed' => true,
            'master' => [
                'host' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['HOST'],
                'port' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PORT'],
                'name' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['NAME'],
                'user' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['USER'],
                'password' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PASSWORD'],
                'charset' => 'utf8',
                'options' => [
                    \PDO::ATTR_PERSISTENT => false,
                    \PDO::ATTR_CASE => \PDO::CASE_NATURAL,
                    \PDO::ATTR_ORACLE_NULLS => \PDO::NULL_NATURAL,
                    \PDO::ATTR_STRINGIFY_FETCHES => false,
                    \PDO::ATTR_EMULATE_PREPARES => false,
                    \PDO::ATTR_TIMEOUT => 30,
                ],
            ],
            'slave' => [
                [
                    'host' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['HOST'],
                    'port' => '5555', // not invalid
                    'name' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['NAME'],
                    'user' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['USER'],
                    'password' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PASSWORD'],
                    'charset' => 'utf8',
                    'options' => [
                        \PDO::ATTR_PERSISTENT => false,
                        \PDO::ATTR_CASE => \PDO::CASE_NATURAL,
                        \PDO::ATTR_ORACLE_NULLS => \PDO::NULL_NATURAL,
                        \PDO::ATTR_STRINGIFY_FETCHES => false,
                        \PDO::ATTR_EMULATE_PREPARES => false,
                        \PDO::ATTR_TIMEOUT => 30,
                    ],
                ],
                [
                    'host' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['HOST'],
                    'port' => '6666', // not invalid
                    'name' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['NAME'],
                    'user' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['USER'],
                    'password' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PASSWORD'],
                    'charset' => 'utf8',
                    'options' => [
                        \PDO::ATTR_PERSISTENT => false,
                        \PDO::ATTR_CASE => \PDO::CASE_NATURAL,
                        \PDO::ATTR_ORACLE_NULLS => \PDO::NULL_NATURAL,
                        \PDO::ATTR_STRINGIFY_FETCHES => false,
                        \PDO::ATTR_EMULATE_PREPARES => false,
                        \PDO::ATTR_TIMEOUT => 30,
                    ],
                ],
            ],
        ]);

        $this->assertInstanceof(\PDO::class, $connect->pdo());
        $this->assertInstanceof(\PDO::class, $connect->pdo());

        $connect->close();
    }

    #[Api([
        'zh-CN:title' => '数据库读写分离',
        'zh-CN:description' => <<<'EOT'
数据库配置项 `separate` 表示读写分离，如果从数据库均连接失败，则读数据还是会走主库。
EOT,
    ])]
    public function testReadConnectDistributedButAllInvalidAndAlsoIsSeparate(): void
    {
        $connect = $this->createDatabaseConnectMock([
            'driver' => 'mysql',
            'separate' => true,
            'distributed' => true,
            'master' => [
                'host' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['HOST'],
                'port' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PORT'],
                'name' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['NAME'],
                'user' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['USER'],
                'password' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PASSWORD'],
                'charset' => 'utf8',
                'options' => [
                    \PDO::ATTR_PERSISTENT => false,
                    \PDO::ATTR_CASE => \PDO::CASE_NATURAL,
                    \PDO::ATTR_ORACLE_NULLS => \PDO::NULL_NATURAL,
                    \PDO::ATTR_STRINGIFY_FETCHES => false,
                    \PDO::ATTR_EMULATE_PREPARES => false,
                    \PDO::ATTR_TIMEOUT => 30,
                ],
            ],
            'slave' => [
                [
                    'host' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['HOST'],
                    'port' => '5555', // not invalid
                    'name' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['NAME'],
                    'user' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['USER'],
                    'password' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PASSWORD'],
                    'charset' => 'utf8',
                    'options' => [
                        \PDO::ATTR_PERSISTENT => false,
                        \PDO::ATTR_CASE => \PDO::CASE_NATURAL,
                        \PDO::ATTR_ORACLE_NULLS => \PDO::NULL_NATURAL,
                        \PDO::ATTR_STRINGIFY_FETCHES => false,
                        \PDO::ATTR_EMULATE_PREPARES => false,
                        \PDO::ATTR_TIMEOUT => 30,
                    ],
                ],
                [
                    'host' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['HOST'],
                    'port' => '6666', // not invalid
                    'name' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['NAME'],
                    'user' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['USER'],
                    'password' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PASSWORD'],
                    'charset' => 'utf8',
                    'options' => [
                        \PDO::ATTR_PERSISTENT => false,
                        \PDO::ATTR_CASE => \PDO::CASE_NATURAL,
                        \PDO::ATTR_ORACLE_NULLS => \PDO::NULL_NATURAL,
                        \PDO::ATTR_STRINGIFY_FETCHES => false,
                        \PDO::ATTR_EMULATE_PREPARES => false,
                        \PDO::ATTR_TIMEOUT => 30,
                    ],
                ],
            ],
        ]);

        $this->assertInstanceof(\PDO::class, $connect->pdo());
        $this->assertInstanceof(\PDO::class, $connect->pdo());

        $connect->close();
    }

    public function testConnectException(): void
    {
        $this->expectException(\PDOException::class);

        $connect = $this->createDatabaseConnectMock([
            'driver' => 'mysql',
            'separate' => false,
            'distributed' => false,
            'master' => [
                'host' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['HOST'],
                'port' => '5566',
                'name' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['NAME'],
                'user' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['USER'],
                'password' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PASSWORD'],
                'charset' => 'utf8',
                'options' => [
                    \PDO::ATTR_PERSISTENT => false,
                    \PDO::ATTR_CASE => \PDO::CASE_NATURAL,
                    \PDO::ATTR_ORACLE_NULLS => \PDO::NULL_NATURAL,
                    \PDO::ATTR_STRINGIFY_FETCHES => false,
                    \PDO::ATTR_EMULATE_PREPARES => false,
                    \PDO::ATTR_TIMEOUT => 30,
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
            'driver' => 'mysql',
            'separate' => false,
            'distributed' => true,
            'master' => [
                'host' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['HOST'],
                'port' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PORT'],
                'name' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['NAME'],
                'user' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['USER'],
                'password' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PASSWORD'],
                'charset' => 'utf8',
                'options' => [
                    \PDO::ATTR_PERSISTENT => false,
                    \PDO::ATTR_CASE => \PDO::CASE_NATURAL,
                    \PDO::ATTR_ORACLE_NULLS => \PDO::NULL_NATURAL,
                    \PDO::ATTR_STRINGIFY_FETCHES => false,
                    \PDO::ATTR_EMULATE_PREPARES => false,
                    \PDO::ATTR_TIMEOUT => 30,
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
            'driver' => 'mysql',
            'separate' => false,
            'distributed' => true,
            'master' => [
                'host' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['HOST'],
                'port' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PORT'],
                'name' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['NAME'],
                'user' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['USER'],
                'password' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PASSWORD'],
                'charset' => 'utf8',
                'options' => [
                    \PDO::ATTR_PERSISTENT => false,
                    \PDO::ATTR_CASE => \PDO::CASE_NATURAL,
                    \PDO::ATTR_ORACLE_NULLS => \PDO::NULL_NATURAL,
                    \PDO::ATTR_STRINGIFY_FETCHES => false,
                    \PDO::ATTR_EMULATE_PREPARES => false,
                    \PDO::ATTR_TIMEOUT => 30,
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
            ->insert($data)
        ;
    }

    public function testDatabaseSelect(): void
    {
        $connect = $this->createDatabaseConnect();
        static::assertSame([], $connect->query('SELECT * FROM guest_book'));
        $this->assertInstanceof(Select::class, $connect->databaseSelect());
    }

    #[Api([
        'zh-CN:title' => 'databaseSelect 返回查询对象',
    ])]
    public function testDatabaseSelectIsNotInit(): void
    {
        $connect = $this->createDatabaseConnect();
        $this->assertInstanceof(Select::class, $connect->databaseSelect());
    }

    #[Api([
        'zh-CN:title' => 'getTableNames 取得数据库表名列表',
    ])]
    public function testGetTableNames(): void
    {
        $connect = $this->createDatabaseConnect();
        $result = $connect->getTableNames('test');
        static::assertTrue(\in_array('guest_book', $result, true));
    }

    #[Api([
        'zh-CN:title' => 'getTableColumns 取得数据库表字段信息',
    ])]
    public function testGetTableColumns(): void
    {
        $connect = $this->createDatabaseConnect();
        $result = $connect->getTableColumns('table_columns');
        unset($result['table_collation']);
        foreach ($result['list'] as &$column) {
            unset($column['collation']);
        }
        unset($column);

        $sql = <<<'eot'
{
    "list": {
        "id": {
            "field": "id",
            "type": "bigint",
            "null": false,
            "key": "PRI",
            "default": null,
            "extra": "auto_increment",
            "comment": "ID",
            "primary_key": true,
            "type_extra": null,
            "type_length": 0,
            "auto_increment": true
        },
        "name": {
            "field": "name",
            "type": "varchar",
            "null": false,
            "key": "",
            "default": "",
            "extra": "",
            "comment": "名字",
            "primary_key": false,
            "type_extra": 64,
            "type_length": 64,
            "auto_increment": false
        },
        "content": {
            "field": "content",
            "type": "longtext",
            "null": false,
            "key": "",
            "default": null,
            "extra": "",
            "comment": "评论内容",
            "primary_key": false,
            "type_extra": null,
            "type_length": 0,
            "auto_increment": false
        },
        "create_at": {
            "field": "create_at",
            "type": "datetime",
            "null": false,
            "key": "",
            "default": "CURRENT_TIMESTAMP",
            "extra": "",
            "comment": "创建时间",
            "primary_key": false,
            "type_extra": null,
            "type_length": 0,
            "auto_increment": false
        },
        "price": {
            "field": "price",
            "type": "decimal",
            "null": false,
            "key": "",
            "default": "0.0000",
            "extra": "",
            "comment": "价格",
            "primary_key": false,
            "type_extra": "14,4",
            "type_length": 14,
            "auto_increment": false
        },
        "enum": {
            "field": "enum",
            "type": "enum",
            "null": false,
            "key": "",
            "default": "T",
            "extra": "",
            "comment": "",
            "primary_key": false,
            "type_extra": "'T','F'",
            "type_length": 0,
            "auto_increment": false
        }
    },
    "primary_key": [
        "id"
    ],
    "auto_increment": "id",
    "table_comment": "表字段"
}
eot;

        static::assertSame(
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

        static::assertSame(
            $sql,
            $this->varJson(
                $result
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'getUniqueIndex 取得数据库表唯一索引信息',
    ])]
    public function testGetUniqueIndex(): void
    {
        $connect = $this->createDatabaseConnect();
        $result = $connect->getUniqueIndex('test_unique');

        $sql = <<<'eot'
{
    "PRIMARY": {
        "field": [
            "id"
        ],
        "comment": "ID"
    },
    "uniq_identity": {
        "field": [
            "identity"
        ],
        "comment": "唯一值"
    }
}
eot;

        static::assertSame(
            $sql,
            $this->varJson(
                $result
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'getRawSql 游标查询',
        'zh-CN:description' => <<<'EOT'
`getRawSql` 返回原生查询真实 SQL，以便于更加直观。

**getRawSql 原型**

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Leevel\Database\Database::class, 'getRawSql', 'define')]}
```
EOT,
    ])]
    public function testGetRawSql(): void
    {
        $sql = Database::getRawSql('SELECT * FROM guest_book WHERE id = :id', [
            ':id' => [1],
        ]);
        static::assertSame($sql, 'SELECT * FROM guest_book WHERE id = 1');
    }

    public function testGetRawSqlString(): void
    {
        $sql = Database::getRawSql('SELECT * FROM guest_book WHERE id = :id', [
            ':id' => ['hello'],
        ]);
        static::assertSame($sql, 'SELECT * FROM guest_book WHERE id = \'hello\'');
    }

    public function testGetRawSqlInt(): void
    {
        $sql = Database::getRawSql('SELECT * FROM guest_book WHERE id = :id', [
            ':id' => [5],
        ]);
        static::assertSame($sql, 'SELECT * FROM guest_book WHERE id = 5');
    }

    public function testGetRawSqlFloat(): void
    {
        $sql = Database::getRawSql('SELECT * FROM guest_book WHERE id = :id', [
            ':id' => [0.5],
        ]);
        static::assertSame($sql, 'SELECT * FROM guest_book WHERE id = 0.5');
    }

    public function testGetRawSqlArray(): void
    {
        $sql = Database::getRawSql('SELECT * FROM guest_book WHERE id IN (:id)', [
            ':id' => [[1, 2, 3]],
        ]);
        static::assertSame($sql, 'SELECT * FROM guest_book WHERE id IN (1,2,3)');
    }

    public function testGetRawSqlNull(): void
    {
        $sql = Database::getRawSql('SELECT * FROM guest_book WHERE id IS :id', [
            ':id' => [null],
        ]);
        static::assertSame($sql, 'SELECT * FROM guest_book WHERE id IS NULL');
    }

    public function testGetRawSqlForPdoParamBool(): void
    {
        $sql = Database::getRawSql('SELECT * FROM guest_book WHERE id = :id', [
            ':id' => [true, \PDO::PARAM_BOOL],
        ]);
        static::assertSame($sql, 'SELECT * FROM guest_book WHERE id = 1');
    }

    public function testGetRawSqlForPdoParamInt(): void
    {
        $sql = Database::getRawSql('SELECT * FROM guest_book WHERE id = :id', [
            ':id' => [true, \PDO::PARAM_INT],
        ]);
        static::assertSame($sql, 'SELECT * FROM guest_book WHERE id = 1');
    }

    public function testGetRawSqlForPdoParamNull(): void
    {
        $sql = Database::getRawSql('SELECT * FROM guest_book WHERE id IS :id', [
            ':id' => [null, \PDO::PARAM_NULL],
        ]);
        static::assertSame($sql, 'SELECT * FROM guest_book WHERE id IS NULL');
    }

    public function testGetRawSqlForPdoParamString(): void
    {
        $sql = Database::getRawSql('SELECT * FROM guest_book WHERE id = :id', [
            ':id' => ['1', \PDO::PARAM_STR],
        ]);
        static::assertSame($sql, 'SELECT * FROM guest_book WHERE id = \'1\'');
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
    protected function needReconnect(\PDOException $e): bool
    {
        // 任意错误都需要重试，为了测试的需要
        return 1 && $this->reconnectRetry <= self::RECONNECT_MAX;
    }
}
