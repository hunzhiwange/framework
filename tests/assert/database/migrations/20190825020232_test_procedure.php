<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class TestProcedure extends AbstractMigration
{
    public function up(): void
    {
        $this->struct();
    }

    public function down(): void
    {
        $sql = <<<'EOT'
            DROP PROCEDURE IF EXISTS `test_procedure`;
            DROP PROCEDURE IF EXISTS `test_procedure2`;
            EOT;
        $this->execute($sql);
    }

    private function struct(): void
    {
        $sql = <<<'EOT'
            DROP PROCEDURE IF EXISTS `test_procedure`;
            DROP PROCEDURE IF EXISTS `test_procedure2`;
            
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
