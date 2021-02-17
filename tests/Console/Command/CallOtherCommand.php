<?php

declare(strict_types=1);

namespace Tests\Console\Command;

use Leevel\Console\Command;

class CallOtherCommand extends Command
{
    protected string $name = 'call:other';

    protected string $description = 'call other command for test.';

    public function handle(): int
    {
        $this->info('call other command test.');

        $this->info('argument is '.json_encode($this->getArgument()));

        $this->info('option is '.json_encode($this->getOption()));

        $this->table([
            'Item',
            'Value',
        ], [
            ['hello', 'world'],
            ['foo', 'bar'],
        ]);

        $this->info($this->time('test time'));

        $this->question('a question');

        $this->error('a error message');

        $this->error('a error message');

        $this->call('load1:test1');

        return 0;
    }
}
