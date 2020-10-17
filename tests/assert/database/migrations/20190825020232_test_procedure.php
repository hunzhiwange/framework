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

final class TestProcedure extends AbstractMigration
{
    /**
     * Down Method.
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
    public function down(): void
    {
        $this->struct();
    }

    /**
     * Up Method.
     */
    public function up(): void
    {
        $sql = <<<'EOT'
            DROP PROCEDURE IF EXISTS `test_procedure`;
            DROP PROCEDURE IF EXISTS `test_procedure2`;
            EOT;
        $this->execute($sql);
    }

    /**
     * struct.
     */
    private function struct(): void
    {
        $sql = <<<'EOT'
            CREATE PROCEDURE `test_procedure`(IN _min INT)
                BEGIN
                SELECT `name` FROM `guest_book` WHERE id > _min;
                SELECT `content` FROM `guest_book` WHERE id > _min+1;
                END;

            CREATE PROCEDURE `test_procedure2`(IN _min INT, OUT _name VARCHAR(200))
                BEGIN
                SELECT `name` INTO _name FROM `guest_book` WHERE id > _min LIMIT 1;
                SELECT `content` FROM `guest_book` WHERE id > _min+1;
                SELECT _name;
                END;
            EOT;
        $this->execute($sql);
    }
}
