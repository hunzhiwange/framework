<?php

declare(strict_types=1);

namespace Leevel\Database\Console;

use Phinx\Console\Command\SeedRun as PhinxSeedRun;

/**
 * 数据库执行测试数据.
 */
class SeedRun extends PhinxSeedRun
{
    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        parent::configure();
        $this->setName('migrate:seedrun');
    }
}
