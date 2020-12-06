<?php

declare(strict_types=1);

namespace Tests\Router\Controllers\ColonRestful;

class Hello
{
    public function fooBar(): string
    {
        return 'hello colon restful with controller and action fooBar';
    }
}
