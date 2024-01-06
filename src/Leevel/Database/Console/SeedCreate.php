<?php

declare(strict_types=1);

namespace Leevel\Database\Console;

use Leevel\Console\RuntimeEnvironment;
use Leevel\Database\SelectDatabase;
use Phinx\Console\Command\SeedCreate as PhinxSeedCreate;

/**
 * 数据库测试数据.
 */
class SeedCreate extends PhinxSeedCreate
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
        $this->setName('migrate:seedcreate');
    }
}
