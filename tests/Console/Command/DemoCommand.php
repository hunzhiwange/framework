<?php

declare(strict_types=1);

namespace Tests\Console\Command;

use Leevel\Console\Command;

class DemoCommand extends Command
{
    protected string $name = 'demo';

    protected string $description = 'Demo.';

    public function handle(): int
    {
        $this->info('call other command test.');

        return 0;
    }
}
