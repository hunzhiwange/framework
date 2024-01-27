<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class TestQuery extends AbstractMigration
{
    public function up(): void
    {
        $this->struct();
    }

    public function down(): void
    {
        $this->table('test_query')->drop()->save();
    }

    private function struct(): void
    {
        $sql = <<<'EOT'
            CREATE TABLE `test_query` (
                `tid` bigint(20) NOT NULL AUTO_INCREMENT,
                `id` bigint(20) unsigned NOT NULL DEFAULT '0',
                `id2` int(10) NOT NULL DEFAULT '0',
                `tname` varchar(64) NOT NULL DEFAULT '',
                `name` varchar(200) NOT NULL DEFAULT '',
                `create_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `num` bigint(20) NOT NULL DEFAULT '0',
                `post` varchar(255) NOT NULL DEFAULT '',
                `value` varchar(255) NOT NULL DEFAULT '',
                `value2` varchar(255) NOT NULL DEFAULT '',
                `title` varchar(255) NOT NULL DEFAULT '',
                `votes` bigint(20) NOT NULL DEFAULT '0',
                `posts` bigint(20) NOT NULL DEFAULT '0',
                `weidao` varchar(255) NOT NULL DEFAULT '',
                `remark` varchar(255) NOT NULL DEFAULT '',
                `goods` bigint(20) NOT NULL DEFAULT '0',
                `hello` varchar(255) NOT NULL DEFAULT '',
                `child_one` varchar(255) NOT NULL DEFAULT '',
                `child_two` varchar(255) NOT NULL DEFAULT '',
                `goods_id` int(10) NOT NULL DEFAULT '0',
                `configs_id` int(10) NOT NULL DEFAULT '0',
                `first_name` varchar(255) NOT NULL DEFAULT '',
                `create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `create_year` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `create_month` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `create_day` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `body` varchar(255) NOT NULL DEFAULT '',
                `new` varchar(255) NOT NULL DEFAULT '',
                `foo` varchar(255) NOT NULL DEFAULT '',
                `bar` varchar(255) NOT NULL DEFAULT '',
                `ttt` varchar(255) NOT NULL DEFAULT '',
                `status` tinyint(3) NOT NULL DEFAULT '0',
                `test` varchar(255) NOT NULL DEFAULT '',
                `user_name` varchar(255) NOT NULL DEFAULT '',
                `UserName` varchar(255) NOT NULL DEFAULT '',
                `sex` varchar(255) NOT NULL DEFAULT '',
                `中国加油` varchar(255) NOT NULL DEFAULT '',
                `战伊` varchar(255) NOT NULL DEFAULT '',
                `a-b_c@!!defg` varchar(255) NOT NULL DEFAULT '',
                `goods_id_1` int(10) NOT NULL DEFAULT '0',
                PRIMARY KEY (`tid`) USING BTREE,
                KEY `idx_statusindex` (`status`) USING BTREE,
                KEY `idx_nameindex` (`name`) USING BTREE,
                KEY `idx_testindex` (`test`) USING BTREE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用于查询的表';
            EOT;
        $this->execute($sql);
    }
}
