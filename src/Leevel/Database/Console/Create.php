<?php

declare(strict_types=1);

namespace Leevel\Database\Console;

use Phinx\Console\Command\Create as PhinxCreate;

/**
 * 数据库迁移创建一个脚本.
 */
class Create extends PhinxCreate
{
    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        parent::configure();
        $this->setName('migrate:create');
    }
}
