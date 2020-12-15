<?php

declare(strict_types=1);

namespace Tests\Router\Controllers\Colon\Hello\World;

class Foo
{
    public function index(): string
    {
        return 'hello colon with more than one in controller';
    }
}
