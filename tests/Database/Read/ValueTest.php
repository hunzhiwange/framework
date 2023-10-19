<?php

declare(strict_types=1);

namespace Tests\Database\Read;

use Tests\Database\DatabaseTestCase as TestCase;

#[Api([
    'zh-CN:title' => '查询一个字段的值.value',
    'path' => 'database/read/value',
])]
/**
 * @internal
 */
final class ValueTest extends TestCase
{
    #[Api([
        'zh-CN:title' => 'value 查询基础用法',
    ])]
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT `test`.`id` FROM `test` LIMIT 1",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect->table('test')
                    ->value('id'),
                $connect
            )
        );
    }
}
