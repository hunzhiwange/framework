<?php

declare(strict_types=1);

namespace Leevel\Database\Console;

use Leevel\Console\RuntimeEnvironment;
use Phinx\Console\Command\Rollback as PhinxRollback;

/**
 * 数据库迁移回滚数据库脚本.
 */
class Rollback extends PhinxRollback
{
    use RuntimeEnvironment;

    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        parent::configure();
        $this->setRuntimeEnvironment();
        $this->setName('migrate:rollback');
    }
}
