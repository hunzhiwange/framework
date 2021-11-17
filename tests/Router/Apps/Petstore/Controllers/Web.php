<?php

declare(strict_types=1);

namespace Tests\Router\Apps\Petstore\Controllers;

class Web
{
    #[Route(
        path: '/web/v1/petLeevelForWeb/{petId:[A-Za-z]+}/',
    )]
    private function petLeevelForWeb(): void
    {
    }

    #[IgnoreRoute(
        path: '/web/v2/petLeevelV2Web/',
    )]
    private function petLeevelV2ForWeb(): void
    {
    }

    #[IgnoreRoute(
        path: '/web/v1/petLeevelIgnoreForWeb/',
    )]
    private function petLeevelIgnoreForWeb(): void
    {
    }
}
