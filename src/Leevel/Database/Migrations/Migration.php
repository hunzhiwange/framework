<?php

declare(strict_types=1);

namespace Leevel\Database\Migrations;

abstract class Migration
{
    use BaseSeedMigration;

    public function handle(): void
    {
        if ($structSql = $this->getStructSql()) {
            $this->execute($structSql);
        }

        if ($seedSql = $this->getSeedSql()) {
            $this->execute($seedSql);
        }
    }

    protected function getStructSql(): string
    {
        return '';
    }
}
