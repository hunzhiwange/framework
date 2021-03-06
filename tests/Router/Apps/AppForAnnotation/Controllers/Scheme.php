<?php

declare(strict_types=1);

namespace Tests\Router\Apps\AppForAnnotation\Controllers;

class Scheme
{
    #[Route(
        path: "/scheme/test",
        scheme: "https",
    )]
    private function fooNotMatchedScheme(): void
    {
    }

    #[Route(
        path: "/scheme/test2",
        scheme: "http",
    )]
    public function barMatchedScheme(): string
    {
        return 'barMatchedScheme';
    }
}
