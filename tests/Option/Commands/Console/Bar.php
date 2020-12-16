<?php

declare(strict_types=1);

namespace Tests\Option\Commands\Console;

use Leevel\Console\Command;

/**
 * bar command.
 */
class Bar extends Command
{
    protected string $name = 'console:bar';

    protected string $description = 'This is a foo command';

    protected string $help = <<<'EOF'
        The <info>%command.name%</info> command to show how to make a command:
        
          <info>php %command.full_name%</info>
        EOF;

    public function handle()
    {
        $this->info('Hello my foo command.');
    }
}
