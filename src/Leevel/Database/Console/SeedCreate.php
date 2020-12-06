<?php

declare(strict_types=1);

namespace Leevel\Database\Console;

use Phinx\Console\Command\SeedCreate as PhinxSeedCreate;

/**
 * 数据库测试数据.
 */
class SeedCreate extends PhinxSeedCreate
{
    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        parent::configure();
        $this->setName('migrate:seedcreate');
    }
}
