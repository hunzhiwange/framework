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

class TestQuery extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $this->struct();
    }

    /**
     * struct.
     */
    private function struct(): void
    {
        $sql = <<<'EOT'
            CREATE TABLE `test_query` (
                `tid` int(11) NOT NULL AUTO_INCREMENT,
                `id` int(11) unsigned NOT NULL DEFAULT '0',
                `id2` int(10) NOT NULL DEFAULT '0',
                `tname` varchar(64) NOT NULL DEFAULT '',
                `name` varchar(200) NOT NULL DEFAULT '',
                `create_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `num` int(11) NOT NULL DEFAULT '0',
                `post` varchar(255) NOT NULL DEFAULT '',
                `value` varchar(255) NOT NULL DEFAULT '',
                `value2` varchar(255) NOT NULL DEFAULT '',
                `title` varchar(255) NOT NULL DEFAULT '',
                `votes` int(11) NOT NULL DEFAULT '0',
                `posts` int(11) NOT NULL DEFAULT '0',
                `weidao` varchar(255) NOT NULL DEFAULT '',
                `remark` varchar(255) NOT NULL DEFAULT '',
                `goods` int(11) NOT NULL DEFAULT '0',
                `hello` varchar(255) NOT NULL DEFAULT '',
                `child_one` varchar(255) NOT NULL DEFAULT '',
                `child_two` varchar(255) NOT NULL DEFAULT '',
                `goods_id` int(10) NOT NULL DEFAULT '0',
                `options_id` int(10) NOT NULL DEFAULT '0',
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
                PRIMARY KEY (`tid`) USING BTREE,
                KEY `statusindex` (`status`) USING BTREE,
                KEY `nameindex` (`name`) USING BTREE,
                KEY `testindex` (`test`) USING BTREE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用于查询的表';
            EOT;

        $this->execute($sql);
    }
}
