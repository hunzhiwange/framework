<?php

declare(strict_types=1);

namespace Tests\Router\Apps\AppForAnnotation\Controllers;

class BindNotSet
{
    #[Route(
        path: '/bindNotSet/test',
        bind: null,
    )]
    private function routePlaceholderFoo(): void
    {
    }

    #[Route(
        path: '/bindNotSet/test2',
        bind: '',
    )]
    private function routePlaceholderBar(): void
    {
    }
}
