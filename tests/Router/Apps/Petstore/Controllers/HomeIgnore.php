<?php

declare(strict_types=1);

namespace Tests\Router\Apps\Petstore\Controllers;

class HomeIgnore
{
    #[Route(
        path: '/',
    )]
    private function Home1(): void
    {
    }

    #[IgnoreRoute(
        path: '',
    )]
    private function home2(): void
    {
    }
}
