<?php

declare(strict_types=1);

use Leevel\Database\Migrations\Migration;

final class UserRole extends Migration
{
    public function handle(): void
    {
        $this->struct();
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
