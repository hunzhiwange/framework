<?php

declare(strict_types=1);

namespace Tests\Console\Load2;

use Leevel\Console\Command;

class Test1 extends Command
{
    protected string $name = 'load2:test1';

    protected string $description = 'load2 test1 for command';

    public function handle(): int
    {
        $this->info('load2 test1');

        return 0;
    }
}
