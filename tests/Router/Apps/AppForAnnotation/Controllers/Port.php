<?php

declare(strict_types=1);

namespace Tests\Router\Apps\AppForAnnotation\Controllers;

class Port
{
    #[Route(
        path: '/port/test2',
        port: '9527',
    )]
    public function barMatchedPort(): string
    {
        return 'barMatchedPort';
    }

    #[Route(
        path: '/port/test',
        port: '9527',
    )]
    private function fooNotMatchedPort(): void
    {
    }
}
