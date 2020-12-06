<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CompositeId extends AbstractMigration
{
    public function up(): void
    {
        $this->struct();
    }

    public function down(): void
    {
        $this->table('composite_id')->drop()->save();
    }

    private function struct(): void
    {
        $sql = <<<'EOT'
            CREATE TABLE `composite_id` (
                `id1` bigint(20) NOT NULL DEFAULT 0 COMMENT 'ID 1',
                `id2` bigint(20) NOT NULL DEFAULT 0 COMMENT 'ID 2',
                `name` varchar(32) NOT NULL DEFAULT '' COMMENT '名字',
                PRIMARY KEY (`id1`,`id2`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='带复合主键的表';
            EOT;
        $this->execute($sql);
    }
}
