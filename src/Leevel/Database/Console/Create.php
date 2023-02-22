<?php

declare(strict_types=1);

namespace Leevel\Database\Console;

use Leevel\Console\RuntimeEnvironment;
use Phinx\Console\Command\Create as PhinxCreate;

/**
 * 数据库迁移创建一个脚本.
 */
class Create extends PhinxCreate
{
    use RuntimeEnvironment;

    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        parent::configure();
        $this->setRuntimeEnvironment();
        $this->setName('migrate:create');
    }
}
