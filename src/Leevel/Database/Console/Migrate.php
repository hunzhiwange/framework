<?php

declare(strict_types=1);

namespace Leevel\Database\Console;

use Leevel\Console\RuntimeEnvironment;
use Leevel\Database\SelectDatabase;
use Phinx\Console\Command\Migrate as PhinxMigrate;

/**
 * 数据库迁移运行数据库脚本.
 */
class Migrate extends PhinxMigrate
{
    use RuntimeEnvironment;
    use SelectDatabase;

    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        parent::configure();
        $this->setRuntimeEnvironment();
        $this->selectDatabase();
        $this->setName('migrate:migrate');
    }
}
