<?php

declare(strict_types=1);

namespace Tests\Router\Controllers\ColonRestful\Hello;

class Show
{
    public function handle(): string
    {
        return 'hello colon restful with controller';
    }
}
