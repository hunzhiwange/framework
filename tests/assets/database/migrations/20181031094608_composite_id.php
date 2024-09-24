<?php

declare(strict_types=1);

use Leevel\Database\Migrations\Migration;

final class CompositeId extends Migration
{
    protected function getStructSql(): string
    {
        return <<<'EOT'
            CREATE TABLE `composite_id` (
                `id1` bigint(20) NOT NULL DEFAULT 0 COMMENT 'ID1',
                `id2` bigint(20) NOT NULL DEFAULT 0 COMMENT 'ID2',
                `name` varchar(32) NOT NULL DEFAULT '' COMMENT '名字',
                PRIMARY KEY (`id1`,`id2`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='带复合主键的表';
            EOT;
    }
}
