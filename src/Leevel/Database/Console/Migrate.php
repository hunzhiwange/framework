<?php

declare(strict_types=1);

namespace Leevel\Database\Console;

use Phinx\Console\Command\Migrate as PhinxMigrate;

/**
 * 数据库迁移运行数据库脚本.
 */
class Migrate extends PhinxMigrate
{
    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        parent::configure();
        $this->setName('migrate:migrate');
    }
}
