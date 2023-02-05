<?php

declare(strict_types=1);

namespace Tests\Option\Commands;

use Leevel\Console\Command;

/**
 * @internal
 *
 * @coversNothing
 */
final class Test extends Command
{
    protected string $name = 'test';

    protected string $description = 'This is a test command';

    protected string $help = <<<'EOF'
        The <info>%command.name%</info> command to show how to make a command:

          <info>php %command.full_name%</info>
        EOF;

    public function handle(): void
    {
        $this->info('Hello my test command.');
    }
}
