<?php

declare(strict_types=1);

use Leevel\Database\Migrations\Migration;

final class TestUnique extends Migration
{
    protected function sql(): string
    {
        return <<<'EOT'
            CREATE TABLE `test_unique` (
                `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'ID',
                `name` varchar(64) NOT NULL DEFAULT '' COMMENT '名字',
                `create_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
                `identity` varchar(64) NOT NULL DEFAULT '' COMMENT '唯一标识符',
                PRIMARY KEY (`id`),
                UNIQUE KEY `uniq_identity` (`identity`) USING BTREE COMMENT '唯一值'
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='带有唯一值的表';
            EOT;
    }
}
