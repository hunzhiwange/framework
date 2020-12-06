<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class User extends AbstractMigration
{
    public function up(): void
    {
        $this->struct();
    }

    public function down(): void
    {
        $this->table('user')->drop()->save();
    }

    private function struct(): void
    {
        $sql = <<<'EOT'
            CREATE TABLE `user` (
                `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'ID',
                `name` varchar(64) NOT NULL DEFAULT '' COMMENT '用户名',
                `create_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
                PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户';
            EOT;
        $this->execute($sql);
    }
}
