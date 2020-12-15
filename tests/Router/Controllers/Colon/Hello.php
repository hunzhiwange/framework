<?php

declare(strict_types=1);

namespace Tests\Router\Controllers\Colon;

class Hello
{
    public function index(): string
    {
        return 'hello colon with controller';
    }
}
