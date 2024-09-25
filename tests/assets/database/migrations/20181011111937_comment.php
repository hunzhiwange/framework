<?php

declare(strict_types=1);

use Leevel\Database\Migrations\Migration;

final class Comment extends Migration
{
    protected function sql(): string
    {
        return <<<'EOT'
            CREATE TABLE `comment` (
                `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'ID',
                `title` varchar(64) NOT NULL DEFAULT '' COMMENT '标题',
                `post_id` bigint(20) NOT NULL COMMENT '文章ID',
                `content` varchar(200) NOT NULL DEFAULT '' COMMENT '评论内容',
                `create_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
                PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='评论';
            EOT;
    }
}
