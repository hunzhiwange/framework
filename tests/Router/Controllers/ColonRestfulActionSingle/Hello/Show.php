<?php

declare(strict_types=1);

namespace Tests\Router\Controllers\ColonRestfulActionSingle\Hello;

class Show
{
    public function handle(): string
    {
        return 'hello colon restful with controller and action is single';
    }
}
