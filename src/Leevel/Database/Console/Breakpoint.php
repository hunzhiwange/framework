<?php

declare(strict_types=1);

namespace Leevel\Database\Console;

use Leevel\Console\RuntimeEnvironment;
use Leevel\Database\SelectDatabase;
use Phinx\Console\Command\Breakpoint as PhinxBreakpoint;

/**
 * 数据库迁移设置断点.
 */
class Breakpoint extends PhinxBreakpoint
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
        $this->setName('migrate:breakpoint');
    }
}
