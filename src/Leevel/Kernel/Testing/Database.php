<?php

declare(strict_types=1);

namespace Leevel\Kernel\Testing;

use Leevel\Database\Proxy\Db;

/**
 * 数据库助手方法.
 */
trait Database
{
    /**
     * 清理数据表.
     */
    protected function truncateDatabase(array $tables, ?string $connect = null): void
    {
        if (!$tables) {
            return;
        }

        foreach ($tables as $table) {
            $sql = <<<'eot'
                [
                    "TRUNCATE TABLE `%s`",
                    [],
                    false
                ]
                eot;
            Db::connect($connect)
                ->table($table)
                ->truncate()
            ;

            $this->assertSame(
                sprintf($sql, $table),
                $this->varJson(
                    Db::connect($connect)->getRealLastSql()
                )
            );

            Db::connect($connect)
                ->table($table)
                ->truncate()
            ;
        }
    }
}
