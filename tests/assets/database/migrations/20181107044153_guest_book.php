<?php

declare(strict_types=1);

use Leevel\Database\Migrations\Migration;

final class GuestBook extends Migration
{
    protected function sql(): string
    {
        return <<<'EOT'
            CREATE TABLE `guest_book` (
                `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'ID',
                `name` varchar(64) NOT NULL DEFAULT '' COMMENT '名字',
                `content` longtext NOT NULL COMMENT '评论内容',
                `create_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
                PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='留言板';
            EOT;
    }
}
