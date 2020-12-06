<?php

declare(strict_types=1);

namespace Tests\Router\Apps\Petstore\Controllers;

class Api
{
    #[Route(
        path: "/api/v1/petLeevelForApi/{petId:[A-Za-z]+}/",
    )]
    private function petLeevelForApi(): void
    {
    }

    #[IgnoreRoute(
        path: "/api/v2/petLeevelV2Api/",
    )]
    private function petLeevelV2ForApi(): void
    {
    }

    #[IgnoreRoute(
        path: "/api/v1/petLeevelIgnoreForApi/",
    )]
    private function petLeevelIgnoreForApi(): void
    {
    }
}
