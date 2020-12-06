<?php

declare(strict_types=1);

namespace Tests\Router\Apps\AppScanRouter\Controllers;

class Pet
{
    #[Route(
        path: "/api/v1/petLeevel/{petId:[A-Za-z]+}/",
    )]
    private function petLeevel(): void
    {
    }

    #[Route(
        path: "/web/petLeevelWithPort/",
        port: "9527",
    )]
    private function petLeevelWithPort(): void
    {
    }
}
