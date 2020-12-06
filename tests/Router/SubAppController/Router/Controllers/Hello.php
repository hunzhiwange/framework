<?php

declare(strict_types=1);

namespace Tests\Router\SubAppController\Router\Controllers;

class Hello
{
    public function index(): string
    {
        return 'hello sub app';
    }
}
