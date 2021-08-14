<?php

declare(strict_types=1);

namespace Tests\Router\Controllers\Api\V1\Hello;

class Index
{
    public function handle(): string
    {
        return 'hello api vi';
    }
}
