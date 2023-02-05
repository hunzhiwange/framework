<?php

declare(strict_types=1);

namespace Leevel\Database\Console;

use Phinx\Console\Command\Test as PhinxTest;

/**
 * 数据库测试环境是否正常.
 *
 * @internal
 *
 * @coversNothing
 */
final class Test extends PhinxTest
{
    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        parent::configure();
        $this->setName('migrate:test');
    }
}
