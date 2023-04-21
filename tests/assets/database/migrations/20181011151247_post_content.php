<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class PostContent extends AbstractMigration
{
    public function up(): void
    {
        $this->struct();
    }

    public function down(): void
    {
        $this->table('post_content')->drop()->save();
    }

    private function struct(): void
    {
        $sql = <<<'EOT'
            CREATE TABLE `post_content` (
                `post_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '文章ID',
                `content` text NOT NULL COMMENT '文章内容',
                PRIMARY KEY (`post_id`),
                KEY `idx_post_id` (`post_id`) USING BTREE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='文章内容';
            EOT;
        $this->execute($sql);
    }
}
