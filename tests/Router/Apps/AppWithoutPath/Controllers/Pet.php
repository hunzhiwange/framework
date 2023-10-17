<?php

declare(strict_types=1);

namespace Tests\Router\Apps\AppWithoutPath\Controllers;

use Leevel\Router\Route;

class Pet
{
    #[Route(
    )]
    public function demo(): void
    {
    }
}
