<?php

declare(strict_types=1);

namespace Tests\Router\Controllers\ColonActionSingle\Hello\World\Foo;

class Index
{
    public function handle(): string
    {
        return 'hello colon with more than one in controller and action is single';
    }
}
