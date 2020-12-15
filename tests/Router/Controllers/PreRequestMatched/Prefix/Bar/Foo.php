<?php

declare(strict_types=1);

namespace Tests\Router\Controllers\PreRequestMatched\Prefix\Bar;

class Foo
{
    public function handle(): string
    {
        return 'hello preRequestMatched';
    }
}
