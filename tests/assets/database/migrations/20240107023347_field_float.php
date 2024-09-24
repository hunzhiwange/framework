<?php

declare(strict_types=1);

use Leevel\Database\Migrations\Migration;

final class FieldFloat extends Migration
{
    protected function getStructSql(): string
    {
        return <<<'EOT'
            CREATE TABLE `field_float` (
                `id` bigint(20) NOT NULL DEFAULT '0' COMMENT 'ID',
                `price` float(8,4) NOT NULL DEFAULT '0.0000' COMMENT '价格'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='字段存在 float 的表';
            EOT;
    }
}
