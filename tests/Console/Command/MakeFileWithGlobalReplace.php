<?php

declare(strict_types=1);

namespace Tests\Console\Command;

use Leevel\Console\Make;
use Symfony\Component\Console\Input\InputArgument;

class MakeFileWithGlobalReplace extends Make
{
    protected string $name = 'makewithglobal:test';

    protected string $description = 'Create a test file.';

    public function handle(): int
    {
        Make::setGlobalReplace(['key2' => 'hello key2 global']);

        $this->setTemplatePath(__DIR__.'/'.($this->getArgument('template') ?: 'template'));
        $this->setCustomReplaceKeyValue('key1', 'hello key1');
        $this->setCustomReplaceKeyValue(['key3' => 'hello key3', 'key4' => 'hello key4']);
        $this->setSaveFilePath(__DIR__.'/'.$this->getArgument('cache').'/'.$this->getArgument('name'));
        $this->setMakeType('test');
        $this->create();

        Make::setGlobalReplace([]);

        return 0;
    }

    protected function getArguments(): array
    {
        return [
            [
                'name',
                InputArgument::OPTIONAL,
                'This is a name.',
            ],
            [
                'template',
                InputArgument::OPTIONAL,
                'This is a template.',
            ],
            [
                'cache',
                InputArgument::OPTIONAL,
                'This is a cache path.',
                'cache',
            ],
        ];
    }
}
