<?php

declare(strict_types=1);

namespace Tests\Router\Controllers\Response;

class BoolResponse
{
    public function handle(): bool
    {
        return true;
    }
}
