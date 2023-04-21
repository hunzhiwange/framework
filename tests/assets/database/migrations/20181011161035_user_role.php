<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class UserRole extends AbstractMigration
{
    public function up(): void
    {
        $this->struct();
    }

    public function down(): void
    {
        $this->table('user_role')->drop()->save();
    }

    private function struct(): void
    {
        $sql = <<<'EOT'
            CREATE TABLE `user_role` (
                `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'ID',
                `user_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '用户ID',
                `role_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '角色ID',
                `create_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
                PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户角色关联';
            EOT;
        $this->execute($sql);
    }
}
