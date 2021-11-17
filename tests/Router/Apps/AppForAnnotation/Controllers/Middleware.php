<?php

declare(strict_types=1);

namespace Tests\Router\Apps\AppForAnnotation\Controllers;

class Middleware
{
    #[Route(
        path: '/middleware/test',
        middlewares: 'group1',
    )]
    public function foo(): string
    {
        return 'Middleware matched';
    }

    #[Route(
        path: '/middleware/test2',
        middlewares: ['group1', 'group2'],
    )]
    public function bar(): string
    {
        return 'Middleware matched 2';
    }

    #[Route(
        path: '/middleware/test3',
        middlewares: ['group1', 'group2', 'demo_for_base_path'],
    )]
    public function hello(): string
    {
        return 'Middleware matched 3';
    }

    #[Route(
        path: '/middleware/test4',
        middlewares: ['Tests\\Router\\Middlewares\\Demo1'],
    )]
    public function world(): string
    {
        return 'Middleware matched 4';
    }
}
