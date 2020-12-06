<?php

declare(strict_types=1);

namespace Tests\Router\Controllers\Response;

class IntResponse
{
    public function handle(): int
    {
        return 123456;
    }
}
