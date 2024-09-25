<?php

declare(strict_types=1);

use Leevel\Database\Migrations\Migration;

final class TableColumns extends Migration
{
    protected function sql(): string
    {
        return <<<'EOT'
            CREATE TABLE `table_columns` (
              `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'ID',
              `name` varchar(64) NOT NULL DEFAULT '' COMMENT '名字',
              `content` longtext NOT NULL COMMENT '评论内容',
              `create_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
              `price` decimal(14,4) unsigned NOT NULL DEFAULT '0.0000' COMMENT '价格',
              `enum` enum('T','F') CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT 'T',
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='表字段';
            EOT;
    }
}
