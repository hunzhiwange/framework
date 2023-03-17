<?php

declare(strict_types=1);

namespace Leevel\Database\Console;

use Leevel\Console\RuntimeEnvironment;
use Leevel\Database\SelectDatabase;
use Phinx\Console\Command\SeedRun as PhinxSeedRun;

/**
 * 数据库执行测试数据.
 */
class SeedRun extends PhinxSeedRun
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
        $this->setName('migrate:seedrun');
    }
}
