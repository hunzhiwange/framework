<?php

declare(strict_types=1);

use Leevel\Database\Migrations\Migration;

final class TestNew extends Migration
{
    protected function sql(): string
    {
        return <<<'EOT'
            CREATE TABLE `test` (
                `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'ID',
                `name` varchar(64) NOT NULL DEFAULT '' COMMENT '名字',
                `create_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
                PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='测试表';
            EOT;
    }
}
