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

final class FieldAllowedNull extends AbstractMigration
{
    public function up(): void
    {
        $this->struct();
    }

    public function down(): void
    {
        $this->table('field_allowed_null')->drop()->save();
    }

    private function struct(): void
    {
        $sql = <<<'EOT'
            CREATE TABLE `field_allowed_null` (
                `goods_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '商品 ID',
                `description` varchar(255) DEFAULT '' COMMENT '商品描述',
                `name` varchar(100) DEFAULT '' COMMENT '商品名称'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='字段允许 NULL 的表';
            EOT;
        $this->execute($sql);
    }
}
