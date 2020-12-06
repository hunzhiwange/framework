<?php

declare(strict_types=1);

namespace Tests\Router\Controllers;

/**
 * home.
 */
class Home
{
    public function index(): string
    {
        return 'hello my home';
    }
}
