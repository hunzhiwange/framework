<?php

declare(strict_types=1);

namespace Tests\Console\Load1;

use Leevel\Console\Command;

/**
 * test1 command.
 */
class Test1 extends Command
{
    protected string $name = 'load1:test1';

    protected string $description = 'load1 test1 for command';

    public function handle(): int
    {
        $this->info('load1 test1');

        return 0;
    }
}
