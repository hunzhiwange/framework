<?php

declare(strict_types=1);

/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Router\Apps\Petstore\Controllers;

use Leevel\Http\Request;

class Pet
{
    #[Route(
        path: "/api/v2/petLeevel/{petId:[A-Za-z]+}/",
        scheme: "https",
        domain: "{subdomain:[A-Za-z]+}-vip.{domain}",
        attributes: ["args1" => "hello", "args2" => "world"],
        bind: "\\PetLeevel\\show",
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
