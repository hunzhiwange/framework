<?php

declare(strict_types=1);

namespace Leevel\Database\Console;

use Leevel\Console\RuntimeEnvironment;
use Phinx\Console\Command\SeedRun as PhinxSeedRun;

/**
 * 数据库执行测试数据.
 */
class SeedRun extends PhinxSeedRun
{
    use RuntimeEnvironment;

    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        parent::configure();
        $this->setRuntimeEnvironment();
        $this->setName('migrate:seedrun');
    }
}
