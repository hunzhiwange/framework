<?php

declare(strict_types=1);

namespace Tests\Router\Apps\AppForAnnotation\Controllers;

class BindNotFound
{
    #[Route(
        path: "/bindNotFound/test/",
        bind: "\\Tests\\Router\\Controllers\\Annotation\\BindNotFound@notFound",
    )]
    private function foo(): void
    {
    }

    #[Route(
        path: "/bindNotFound/test2/",
        bind: "\\Tests\\Router\\Controllers\\Annotation\\BindNotFound",
    )]
    private function bar(): void
    {
    }

    #[Route(
        path: "/bindNotFound/test3/",
        bind: "\\Tests\\Router\\Controllers\\Annotation\\BindMethodNotFound",
    )]
    private function bar3(): void
    {
    }
}
