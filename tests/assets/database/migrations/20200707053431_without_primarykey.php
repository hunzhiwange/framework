<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class WithoutPrimarykey extends AbstractMigration
{
    public function up(): void
    {
        $this->struct();
    }

    public function down(): void
    {
        $this->table('without_primarykey')->drop()->save();
    }

    private function struct(): void
    {
        $sql = <<<'EOT'
            CREATE TABLE `without_primarykey` (
                `goods_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '商品 ID',
                `description` varchar(255) NOT NULL DEFAULT '' COMMENT '商品描述',
                `name` varchar(100) NOT NULL DEFAULT '' COMMENT '商品名称'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='没有主键的表';
            EOT;
        $this->execute($sql);
    }
}
