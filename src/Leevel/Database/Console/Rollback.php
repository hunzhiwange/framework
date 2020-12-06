<?php

declare(strict_types=1);

namespace Leevel\Database\Console;

use Phinx\Console\Command\Rollback as PhinxRollback;

/**
 * 数据库迁移回滚数据库脚本.
 */
class Rollback extends PhinxRollback
{
    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        parent::configure();
        $this->setName('migrate:rollback');
    }
}
