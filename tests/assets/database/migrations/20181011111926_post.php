<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Post extends AbstractMigration
{
    public function up(): void
    {
        $this->struct();
    }

    public function down(): void
    {
        $this->table('post')->drop()->save();
    }

    private function struct(): void
    {
        $sql = <<<'EOT'
            CREATE TABLE `post` (
                `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'ID',
                `title` varchar(64) NOT NULL DEFAULT '' COMMENT '标题',
                `user_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '用户 ID',
                `summary` varchar(200) NOT NULL DEFAULT '' COMMENT '文章摘要',
                `create_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
                `delete_at` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间 0=未删除;大于0=删除时间;',
                PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='文章';
            EOT;
        $this->execute($sql);
    }
}
