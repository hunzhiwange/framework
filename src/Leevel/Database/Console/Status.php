<?php

declare(strict_types=1);

namespace Leevel\Database\Console;

use Phinx\Console\Command\Status as PhinxStatus;

/**
 * 数据库打印所有迁移脚本和他们的状态
 */
class Status extends PhinxStatus
{
    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        parent::configure();
        $this->setName('migrate:status');
    }
}
