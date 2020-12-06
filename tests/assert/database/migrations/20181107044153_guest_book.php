<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class GuestBook extends AbstractMigration
{
    public function up(): void
    {
        $this->struct();
    }

    public function down(): void
    {
        $this->table('guest_book')->drop()->save();
    }

    private function struct(): void
    {
        $sql = <<<'EOT'
            CREATE TABLE `guest_book` (
                `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'ID',
                `name` varchar(64) NOT NULL DEFAULT '' COMMENT '名字',
                `content` longtext NOT NULL COMMENT '评论内容',
                `create_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
                PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='留言板';
            EOT;
        $this->execute($sql);
    }
}
