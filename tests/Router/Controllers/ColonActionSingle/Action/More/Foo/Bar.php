<?php

declare(strict_types=1);

namespace Tests\Router\Controllers\ColonActionSingle\Action\More\Foo;

class Bar
{
    public function handle(): string
    {
        return 'hello colon with action and action is not single class with more than one and action is single';
    }
}
