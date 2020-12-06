<?php

declare(strict_types=1);

namespace Tests\Router\Apps\AppForAnnotation\Controllers;

class BasePath
{
    #[Route(
        path: "/basePath/normalize/",
        bind: "\\Tests\\Router\\Controllers\\Annotation\\BasePath@normalize",
    )]
    private function foo(): void
    {
    }
}
