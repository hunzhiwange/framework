<?php

declare(strict_types=1);

namespace Tests\Router\Apps\Petstore\Controllers;

use Leevel\Http\Request;

class User
{
    #[Route(
        path: '/user',
        method: Request::METHOD_POST,
    )]
    private function createUser(): void
    {
    }

    #[Route(
        path: '/user/createWithArray',
        method: Request::METHOD_POST,
    )]
    private function createUsersWithListInput(): void
    {
    }

    #[Route(
        path: '/user/login',
    )]
    private function loginUser(): void
    {
    }

    #[Route(
        path: '/user/logout',
    )]
    private function logoutUser(): void
    {
    }

    #[Route(
        path: '/user/{username}',
    )]
    private function getUserByName(): void
    {
    }

    #[Route(
        path: '/user/{username}',
        method: Request::METHOD_PUT,
    )]
    private function updateUser(): void
    {
    }

    #[Route(
        path: '/user/{username}',
        method: Request::METHOD_DELETE,
    )]
    private function deleteUser(): void
    {
    }
}
