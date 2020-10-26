<?php

declare(strict_types=1);

/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Phinx\Migration\AbstractMigration;

final class UserRoleSoftDeleted extends AbstractMigration
{
    public function up(): void
    {
        $this->struct();
    }

    public function down(): void
    {
        $this->table('user_role_soft_deleted')->drop()->save();
    }

    private function struct(): void
    {
        $sql = <<<'EOT'
            CREATE TABLE `user_role_soft_deleted` (
                `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'ID',
                `user_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '用户 ID',
                `role_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '角色 ID',
                `create_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
                `delete_at` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间 0=未删除;大于0=删除时间;',
                PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='带有软删除的用户角色关联';
            EOT;
        $this->execute($sql);
    }
}
