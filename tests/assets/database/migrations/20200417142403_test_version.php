<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class TestVersion extends AbstractMigration
{
    public function up(): void
    {
        $this->struct();
    }

    public function down(): void
    {
        $this->table('test_version')->drop()->save();
    }

    private function struct(): void
    {
        $sql = <<<'EOT'
            CREATE TABLE `test_version` (
                `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'ID',
                `name` varchar(255) NOT NULL DEFAULT '' COMMENT '名字',
                `available_number` decimal(14,4) NOT NULL DEFAULT '0.0000' COMMENT '可售库存',
                `real_number` decimal(14,4) NOT NULL DEFAULT '0.0000' COMMENT '实际库存',
                PRIMARY KEY (`id`),
                `version` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '版本'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='带版本字段的表';
            EOT;
        $this->execute($sql);
    }
}
