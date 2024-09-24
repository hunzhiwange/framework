<?php

declare(strict_types=1);

namespace Leevel\Database;

use Symfony\Component\Console\Input\InputOption;

trait SelectDatabase
{
    protected function selectDatabase(): void
    {
        $this->addOption(
            'database',
            null,
            InputOption::VALUE_REQUIRED,
            'Select database.',
        );
    }
}
