<?php

declare(strict_types=1);

namespace Tests\Router\Controllers;

class ControllerConvertFooBar
{
    public function bar(): string
    {
        return 'hello controller convert';
    }
}
