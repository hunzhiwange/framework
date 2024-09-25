<?php

declare(strict_types=1);

use Leevel\Database\Migrations\Migration;

final class TestQuerySubsql extends Migration
{
    protected function sql(): string
    {
        return <<<'EOT'
            CREATE TABLE `test_query_subsql` (
                `id` bigint(20) NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL DEFAULT '',
                `value` varchar(255) NOT NULL DEFAULT '',
                `new` varchar(255) NOT NULL DEFAULT '',
                `hello` varchar(255) NOT NULL DEFAULT '',
                `test` varchar(255) NOT NULL DEFAULT '',
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用于子查询的表';
            EOT;
    }
}
