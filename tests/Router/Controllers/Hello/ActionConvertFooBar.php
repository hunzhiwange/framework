<?php

declare(strict_types=1);

namespace Tests\Router\Controllers\Hello;

class ActionConvertFooBar
{
    public function handle(): string
    {
        return 'hello action convert foo bar';
    }
}
