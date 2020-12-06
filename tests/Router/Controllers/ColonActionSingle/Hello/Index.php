<?php

declare(strict_types=1);

namespace Tests\Router\Controllers\ColonActionSingle\Hello;

/**
 * index.
 */
class Index
{
    public function handle(): string
    {
        return 'hello colon with controller and action is single';
    }
}
