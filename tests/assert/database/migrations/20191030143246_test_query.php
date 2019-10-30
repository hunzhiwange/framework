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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
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
                `tid` int(11) NOT NULL AUTO_INCREMENT COMMENT 'tid',
                `id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'id',
                `id2` int(10) NOT NULL,
                `tname` varchar(64) NOT NULL DEFAULT '',
                `name` varchar(200) NOT NULL DEFAULT '',
                `create_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `num` int(11) NOT NULL,
                `post` varchar(255) NOT NULL,
                `value` varchar(255) NOT NULL,
                `value2` varchar(255) NOT NULL,
                `title` varchar(255) NOT NULL,
                `votes` int(11) NOT NULL,
                `posts` int(11) NOT NULL,
                `weidao` varchar(255) NOT NULL,
                `remark` varchar(255) NOT NULL,
                `goods` int(11) NOT NULL,
                `hello` varchar(255) NOT NULL,
                `child_one` varchar(255) NOT NULL,
                `child_two` varchar(255) NOT NULL,
                `goods_id` int(10) NOT NULL,
                `options_id` int(10) NOT NULL,
                `first_name` varchar(255) NOT NULL,
                `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `create_year` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `create_month` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `create_day` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `body` varchar(255) NOT NULL,
                `new` varchar(255) NOT NULL,
                `foo` varchar(255) NOT NULL,
                `bar` varchar(255) NOT NULL,
                `ttt` varchar(255) NOT NULL,
                `status` tinyint(3) NOT NULL,
                `test` varchar(255) NOT NULL,
                `user_name` varchar(255) NOT NULL,
                `UserName` varchar(255) NOT NULL,
                `sex` varchar(255) NOT NULL,
                PRIMARY KEY (`tid`) USING BTREE,
                KEY `statusindex` (`status`) USING BTREE,
                KEY `nameindex` (`name`) USING BTREE,
                KEY `testindex` (`test`) USING BTREE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            EOT;

        $this->execute($sql);
    }
}
