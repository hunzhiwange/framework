<?php

declare(strict_types=1);

namespace Tests\Router\Controllers\Hello;

/**
 * throughMiddleware.
 */
class ThroughMiddleware
{
    public function handle(): string
    {
        return 'hello throughMiddleware';
    }
}
