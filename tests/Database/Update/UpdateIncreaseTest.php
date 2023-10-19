<?php

declare(strict_types=1);

namespace Tests\Database\Update;

use Leevel\Database\Condition;
use Tests\Database\DatabaseTestCase as TestCase;

#[Api([
    'zh-CN:title' => '更新字段递增.updateIncrease',
    'path' => 'database/update/updateincrease',
])]
final class UpdateIncreaseTest extends TestCase
{
    #[Api([
        'zh-CN:title' => 'updateIncrease 基本用法',
        'zh-CN:description' => <<<'EOT'
更新成功后，返回影响行数，`updateIncrease` 实际上调用的是 `updateColumn` 方法。
EOT,
    ])]
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "UPDATE `test_query` SET `test_query`.`num` = `test_query`.`num`+3 WHERE `test_query`.`id` = :test_query_id",
                {
                    "test_query_id": [
                        503
                    ]
                },
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->where('id', 503)
                    ->updateIncrease('num', 3),
                $connect
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'updateIncrease 支持参数绑定',
    ])]
    public function testBind(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "UPDATE `test_query` SET `test_query`.`num` = `test_query`.`num`+3 WHERE `test_query`.`id` = ?",
                [
                    503
                ],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->where('id', Condition::raw('?'))
                    ->updateIncrease('num', 3, [503]),
                $connect
            )
        );
    }
}
