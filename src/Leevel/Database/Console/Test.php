<?php

declare(strict_types=1);

namespace Leevel\Database\Console;

use Leevel\Console\RuntimeEnvironment;
use Phinx\Console\Command\Test as PhinxTest;

/**
 * 数据库测试环境是否正常.
 */
final class Test extends PhinxTest
{
    use RuntimeEnvironment;

    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        parent::configure();
        $this->setRuntimeEnvironment();
        $this->setName('migrate:test');
    }
}
