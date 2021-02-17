<?php

declare(strict_types=1);

namespace Tests\Console\Load2;

use Leevel\Console\Command;

class Test2 extends Command
{
    protected string $name = 'load2:test2';

    protected string $description = 'load2 test2 for command';

    public function handle(): int
    {
        $this->info('load2 test2');

        return 0;
    }
}
