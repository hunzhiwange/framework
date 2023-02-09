<?php

declare(strict_types=1);

namespace Tests\Router\Apps\AppForAnnotation\Controllers;

use Leevel\Router\Route;

class Pet
{
    #[Route(
        path: '/api/notInGroup/petLeevel/{petId:[A-Za-z]+}/',
    )]
    public function petLeevelNotInGroup(): string
    {
        return 'petLeevelNotInGroup';
    }

    #[Route(
        path: '/api/v1/petLeevel/{petId:[A-Za-z]+}/',
        bind: '\\Tests\\Router\\Controllers\\Annotation\\PetLeevel',
    )]
    private function petLeevel(): void
    {
    }

    #[Route(
        path: '/newPrefix/v1/petLeevel/{petId:[A-Za-z]+}/',
        bind: '\\Tests\\Router\\Controllers\\Annotation\\NewPrefix',
    )]
    private function newPrefix(): void
    {
    }
}
