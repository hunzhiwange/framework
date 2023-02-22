<?php

declare(strict_types=1);

namespace Leevel\Console;

use Symfony\Component\Console\Input\InputOption;

trait RuntimeEnvironment
{
    protected function setRuntimeEnvironment(): void
    {
        $this->addOption(
            'runtime_environment',
            'env',
            InputOption::VALUE_OPTIONAL,
            'Set runtime environment file.',
        );
    }
}
