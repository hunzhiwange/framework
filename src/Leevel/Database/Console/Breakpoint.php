<?php

declare(strict_types=1);

namespace Leevel\Database\Console;

use Phinx\Console\Command\Breakpoint as PhinxBreakpoint;

/**
 * 数据库迁移设置断点.
 */
class Breakpoint extends PhinxBreakpoint
{
    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        parent::configure();
        $this->setName('migrate:breakpoint');
    }
}
