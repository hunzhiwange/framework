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

namespace Tests\Database\Query;

use Tests\Database\DatabaseTestCase as TestCase;

/**
 * @api(
 *     title="Query lang.forUpdate",
 *     zh-CN:title="查询语言.forUpdate",
 *     path="database/query/forupdate",
 *     zh-CN:description="
 * 对数据库悲观锁的支持，排它锁和共享锁。
 * ",
 * )
 */
class ForUpdateTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="forUpdate 排它锁 FOR UPDATE 查询",
     *     zh-CN:description="
     * **第一步事务中加入排它锁未提交**
     *
     * 在未提交前，表 test_query 的 `tid = 1` 行将会锁住，其它查询在这一行数据无法加上排它锁和共享锁，更不能更新改行数据，一直等待直到 commit 或者超时。
     *
     * ``` sql
     * BEGIN;
     * SELECT `test_query`.* FROM `test_query` WHERE `tid` = 1 FOR UPDATE;
     * -- COMMIT;
     * ```
     *
     * 提交后 commit，其它会正常执行。
     *
     * **排它锁失败**
     *
     * ``` sql
     * SELECT `test_query`.* FROM `test_query` WHERE `tid` = 1 FOR UPDATE;
     * ```
     *
     * **共享锁失败**
     *
     * ``` sql
     * SELECT `test_query`.* FROM `test_query` WHERE `tid` = 1 LOCK IN SHARE MODE;
     * ```
     *
     * **更改失败**
     *
     * ``` sql
     * UPDATE `test_query` SET `name` = 'hello' WHERE `tid` = 1;
     * ```
     *
     * **普通查询正常**
     *
     * ``` sql
     * SELECT `test_query`.* FROM `test_query` WHERE `tid` = 1;
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testForUpdate(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` FOR UPDATE",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->forUpdate()
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="forUpdate 取消排它锁 FOR UPDATE 查询",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testCancelForUpdate(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query`",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->forUpdate()
                    ->forUpdate(false)
                    ->findAll(true),
                1
            )
        );
    }

    public function testForUpdateFlow(): void
    {
        $condition = false;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query`",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->if($condition)
                    ->forUpdate()
                    ->else()
                    ->forUpdate(false)
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testForUpdateFlow2(): void
    {
        $condition = true;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` FOR UPDATE",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->if($condition)
                    ->forUpdate()
                    ->else()
                    ->forUpdate(false)
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="lockShare 共享锁 LOCK SHARE 查询",
     *     zh-CN:description="
     * **第一步事务中加入排它锁未提交**
     *
     * 在未提交前，表 test_query 的 `tid = 1` 行将会锁住，其它查询在这一行数据无法加上排它锁，更不能更新改行数据，但是共享锁是可以的，一直等待直到 commit 或者超时。
     *
     * ``` sql
     * BEGIN;
     * SELECT `test_query`.* FROM `test_query` WHERE `tid` = 1 LOCK IN SHARE MODE;
     * -- COMMIT;
     * ```
     *
     * 提交后 commit，其它会正常执行。
     *
     * **排它锁失败**
     *
     * ``` sql
     * SELECT `test_query`.* FROM `test_query` WHERE `tid` = 1 FOR UPDATE;
     * ```
     *
     * **共享锁成功**
     *
     * ``` sql
     * SELECT `test_query`.* FROM `test_query` WHERE `tid` = 1 LOCK IN SHARE MODE;
     * ```
     *
     * **更改失败**
     *
     * ``` sql
     * UPDATE `test_query` SET `name` = 'hello' WHERE `tid` = 1;
     * ```
     *
     * **普通查询正常**
     *
     * ``` sql
     * SELECT `test_query`.* FROM `test_query` WHERE `tid` = 1;
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testLockShare(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` LOCK IN SHARE MODE",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->lockShare()
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="lockShare 取消共享锁 LOCK SHARE 查询",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testCancelLockShare(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query`",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->lockShare()
                    ->lockShare(false)
                    ->findAll(true),
                1
            )
        );
    }

    public function testLockShareFlow(): void
    {
        $condition = false;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query`",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->if($condition)
                    ->lockShare()
                    ->else()
                    ->lockShare(false)
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testLockShareFlow2(): void
    {
        $condition = true;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` LOCK IN SHARE MODE",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->if($condition)
                    ->lockShare()
                    ->else()
                    ->lockShare(false)
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testForUpdateAndLockShare(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Lock share and for update cannot exist at the same time.'
        );

        $connect = $this->createDatabaseConnectMock();
        $connect
            ->table('test_query')
            ->forUpdate()
            ->lockShare()
            ->findAll(true);
    }

    public function testLockShareAndForUpdate(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Lock share and for update cannot exist at the same time.'
        );

        $connect = $this->createDatabaseConnectMock();
        $connect
            ->table('test_query')
            ->lockShare()
            ->forUpdate()
            ->findAll(true);
    }
}
