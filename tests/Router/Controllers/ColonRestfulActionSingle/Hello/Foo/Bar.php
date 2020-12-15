<?php

declare(strict_types=1);

namespace Tests\Router\Controllers\ColonRestfulActionSingle\Hello\Foo;

class Bar
{
    public function handle(): string
    {
        return 'hello colon restful with action and action is single';
    }
}
