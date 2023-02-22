<?php

declare(strict_types=1);

namespace Leevel\Database\Console;

use Leevel\Console\RuntimeEnvironment;
use Phinx\Console\Command\Breakpoint as PhinxBreakpoint;

/**
 * 数据库迁移设置断点.
 */
class Breakpoint extends PhinxBreakpoint
{
    use RuntimeEnvironment;

    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        parent::configure();
        $this->setRuntimeEnvironment();
        $this->setName('migrate:breakpoint');
    }
}
