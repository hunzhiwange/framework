<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Comment extends AbstractMigration
{
    public function up(): void
    {
        $this->struct();
    }

    public function down(): void
    {
        $this->table('comment')->drop()->save();
    }

    private function struct(): void
    {
        $sql = <<<'EOT'
            CREATE TABLE `comment` (
                `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'ID',
                `title` varchar(64) NOT NULL DEFAULT '' COMMENT '标题',
                `post_id` bigint(20) NOT NULL COMMENT '文章 ID',
                `content` varchar(200) NOT NULL DEFAULT '' COMMENT '评论内容',
                `create_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
                PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='评论';
            EOT;
        $this->execute($sql);
    }
}
