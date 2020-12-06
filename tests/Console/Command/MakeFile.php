<?php

declare(strict_types=1);

namespace Tests\Console\Command;

use Leevel\Console\Argument;
use Leevel\Console\Make;

/**
 * makeFile.
 */
class MakeFile extends Make
{
    protected string $name = 'make:test';

    protected string $description = 'Create a test file.';

    public function handle(): int
    {
        $this->setTemplatePath(__DIR__.'/'.($this->getArgument('template') ?: 'template'));

        $this->setCustomReplaceKeyValue('key1', 'hello key1');

        $this->setCustomReplaceKeyValue('key2', 'hello key2');

        $this->setCustomReplaceKeyValue(['key3' => 'hello key3', 'key4' => 'hello key4']);
        $this->setSaveFilePath(__DIR__.'/'.$this->getArgument('cache').'/'.$this->getArgument('name'));

        $this->setMakeType('test');

        $this->create();

        return 0;
    }

    protected function getArguments(): array
    {
        return [
            [
                'name',
                Argument::OPTIONAL,
                'This is a name.',
            ],
            [
                'template',
                Argument::OPTIONAL,
                'This is a template.',
            ],
            [
                'cache',
                Argument::OPTIONAL,
                'This is a cache path.',
                'cache',
            ],
        ];
    }
}
