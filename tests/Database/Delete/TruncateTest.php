<?php

declare(strict_types=1);

namespace Tests\Database\Delete;

use Tests\Database\DatabaseTestCase as TestCase;

#[Api([
    'zh-CN:title' => '清空数据.truncate',
    'path' => 'database/delete/truncate',
])]
final class TruncateTest extends TestCase
{
    #[Api([
        'zh-CN:title' => 'truncate 基本用法',
        'zh-CN:description' => <<<'EOT'
清理没有返回值。
EOT,
    ])]
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
