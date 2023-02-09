<?php

declare(strict_types=1);

namespace Tests\Router\Apps\AppForAnnotation\Controllers;

use Leevel\Router\Route;

class BasePath
{
    #[Route(
        path: '/basePath/normalize/',
        bind: '\\Tests\\Router\\Controllers\\Annotation\\BasePath@normalize',
    )]
    private function foo(): void
    {
    }
}
