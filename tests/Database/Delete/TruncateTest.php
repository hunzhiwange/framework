<?php

declare(strict_types=1);

namespace Tests\Database\Delete;

use Tests\Database\DatabaseTestCase as TestCase;

/**
 * @api(
 *     zh-CN:title="清空数据.truncate",
 *     path="database/delete/truncate",
 *     zh-CN:description="",
 * )
 */
final class TruncateTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="truncate 基本用法",
     *     zh-CN:description="清理没有返回值。",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "TRUNCATE TABLE `test`",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect->table('test')
                    ->truncate(),
                $connect
            )
        );
    }
}
