<?php

declare(strict_types=1);

namespace Tests\Router\Controllers\ColonRestful\Hello;

/**
 * show.
 */
class Show
{
    public function handle(): string
    {
        return 'hello colon restful with controller';
    }
}
