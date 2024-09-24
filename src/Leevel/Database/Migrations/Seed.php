<?php

declare(strict_types=1);

namespace Leevel\Database\Migrations;

abstract class Seed
{
    use BaseSeedMigration;

    public function handle(): void
    {
        if (getenv('RUNTIME_SEED_CLEAR')) {
            if ($clearSql = $this->getClearSql()) {
                $this->execute($clearSql);
            }
        } elseif ($seedSql = $this->getSeedSql()) {
            $this->execute($seedSql);
        }
    }

    protected function getClearSql(): string
    {
        return '';
    }
}
