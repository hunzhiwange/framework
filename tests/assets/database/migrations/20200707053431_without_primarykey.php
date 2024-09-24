<?php

declare(strict_types=1);

use Leevel\Database\Migrations\Migration;

final class WithoutPrimarykey extends Migration
{
    public function handle(): void
    {
        $this->struct();
    }

    private function struct(): void
    {
        $sql = <<<'EOT'
            CREATE TABLE `without_primarykey` (
                `goods_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '商品ID',
                `description` varchar(255) NOT NULL DEFAULT '' COMMENT '商品描述',
                `name` varchar(100) NOT NULL DEFAULT '' COMMENT '商品名称'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='没有主键的表';
            EOT;
        $this->execute($sql);
    }
}
