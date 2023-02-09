<?php

declare(strict_types=1);

namespace Tests\Router\Apps\AppWithControllerDirNotMatche\Controllers;

use Leevel\Router\Route;

class Pet
{
    #[Route(
        path: '/api/v1/petLeevel/{petId:[A-Za-z]+}/',
    )]
    private function petLeevel(): void
    {
    }
}
