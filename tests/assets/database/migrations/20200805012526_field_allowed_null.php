<?php

declare(strict_types=1);

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
