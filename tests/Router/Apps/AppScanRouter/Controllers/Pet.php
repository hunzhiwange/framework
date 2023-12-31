<?php

declare(strict_types=1);

namespace Tests\Router\Apps\AppScanRouter\Controllers;

use Leevel\Http\Request;
use Leevel\Router\Route;

class Pet
{
    #[Route(
        path: '/api/v1/petLeevel/{petId:[A-Za-z]+}/',
    )]
    public function petLeevel(): void
    {
    }

    #[Route(
        path: '/web/petLeevelNotSupportedMethod/',
        method: Request::METHOD_CONNECT,
    )]
    public function petLeevelNotSupportedMethod(): void
    {
    }
}
