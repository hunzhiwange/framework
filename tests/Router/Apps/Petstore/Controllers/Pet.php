<?php

declare(strict_types=1);

namespace Tests\Router\Apps\Petstore\Controllers;

use Leevel\Http\Request;

class Pet
{
    #[Route(
        path: "/api/v2/petLeevel/{petId:[A-Za-z]+}/",
        scheme: "https",
        domain: "{subdomain:[A-Za-z]+}-vip.{domain}",
        attributes: ["args1" => "hello", "args2" => "world"],
        bind: "\\PetLeevel\\Show",
        middlewares: "api",
    )]
    private function petLeevel(): void
    {
    }

    #[IgnoreRoute(
        path: "/api/v2/petLeevelIgnore/",
    )]
    private function petLeevelIgnore(): void
    {
    }

    #[Route(
        path: "/pet",
        method: Request::METHOD_POST,
    )]
    private function addPet(): void
    {
    }

    #[Route(
        path: "/pet",
        method: Request::METHOD_PUT,
    )]
    private function updatePet(): void
    {
    }

    #[IgnoreRoute(
        path: "/pet/findByStatus",
    )]
    private function findPetsByStatus(): void
    {
    }

    #[Route(
        path: "/pet/findByTags",
    )]
    private function findByTags(): void
    {
    }

    #[Route(
        path: "/pet/{petId}",
    )]
    private function getPetById(int $id): void
    {
    }

    #[Route(
        path: "/pet/{petId}",
        method: Request::METHOD_POST,
    )]
    private function updatePetWithForm(): void
    {
    }

    #[Route(
        path: "/pet/{petId}",
        method: Request::METHOD_DELETE,
    )]
    private function deletePet(): void
    {
    }

    #[Route(
        path: "/pet/{petId}/uploadImage",
        method: Request::METHOD_POST,
    )]
    private function uploadFile(): void
    {
    }
}
