<?php

declare(strict_types=1);

use Leevel\Database\Migrations\Migration;

final class FieldAllowedNull extends Migration
{
    protected function sql(): string
    {
        return <<<'EOT'
            CREATE TABLE `field_allowed_null` (
                `goods_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '商品ID',
                `description` varchar(255) DEFAULT '' COMMENT '商品描述',
                `name` varchar(100) DEFAULT '' COMMENT '商品名称'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='字段允许 NULL 的表';
            EOT;
    }
}
